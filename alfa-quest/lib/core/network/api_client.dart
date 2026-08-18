import 'dart:io' hide TlsException;

import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';

import '../config/app_config.dart';
import '../errors/app_exception.dart';

class ApiClient {
  late final Dio dio;

  ApiClient({
    List<Interceptor> interceptors = const [],
  }) {
    dio = Dio(BaseOptions(
      baseUrl: AppConfig.apiBaseUrl,
      connectTimeout: const Duration(seconds: 60),
      receiveTimeout: const Duration(seconds: 60),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'User-Agent': _buildUserAgent(),
      },
    ));

    dio.interceptors.addAll(interceptors);

    // Add logging interceptor LAST in debug mode (sees final request/response)
    if (kDebugMode) {
      dio.interceptors.add(LogInterceptor(
        requestBody: true,
        responseBody: true,
        error: true,
        logPrint: (obj) => debugPrint('[DIO] $obj'),
      ));
    }
  }

  static String _buildUserAgent() {
    final platform = Platform.isIOS ? 'iOS' : 'Android';
    return 'OperacaoAlfa/${AppConfig.versionName} '
        '($platform; flavor=${AppConfig.env})';
  }

  Future<Response<T>> get<T>(
    String path, {
    Map<String, dynamic>? queryParameters,
    Options? options,
  }) async {
    try {
      return await dio.get<T>(path,
          queryParameters: queryParameters, options: options);
    } on DioException catch (e) {
      throw mapDioError(e);
    }
  }

  Future<Response<T>> post<T>(
    String path, {
    dynamic data,
    Options? options,
    Duration? timeout,
  }) async {
    try {
      final opts = options ?? Options();
      if (timeout != null) {
        opts.sendTimeout = timeout;
        opts.receiveTimeout = timeout;
      }
      return await dio.post<T>(path, data: data, options: opts);
    } on DioException catch (e) {
      throw mapDioError(e);
    }
  }

  Future<Response<T>> put<T>(
    String path, {
    dynamic data,
    Options? options,
  }) async {
    try {
      return await dio.put<T>(path, data: data, options: options);
    } on DioException catch (e) {
      throw mapDioError(e);
    }
  }

  Future<Response<T>> delete<T>(
    String path, {
    dynamic data,
    Options? options,
  }) async {
    try {
      return await dio.delete<T>(path, data: data, options: options);
    } on DioException catch (e) {
      throw mapDioError(e);
    }
  }

  static AppException mapDioError(DioException e) {
    // Always log the real error in debug
    debugPrint('[ApiClient] DioException type=${e.type} '
        'message=${e.message} '
        'error=${e.error?.runtimeType}: ${e.error}');

    switch (e.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
      case DioExceptionType.transformTimeout:
        return const TimeoutException();
      case DioExceptionType.badCertificate:
        return const TlsException();
      case DioExceptionType.connectionError:
        return _mapConnectionError(e);
      case DioExceptionType.badResponse:
        return _mapStatusCode(e.response);
      case DioExceptionType.cancel:
        return const TimeoutException();
      case DioExceptionType.unknown:
        return _mapUnknownError(e);
    }
  }

  static AppException _mapConnectionError(DioException e) {
    final error = e.error;
    if (error is HandshakeException) {
      return const TlsException();
    }
    if (error is SocketException) {
      final message = error.message.toLowerCase();
      if (message.contains('no address') ||
          message.contains('failed host lookup') ||
          message.contains('name or service not known')) {
        // DNS resolution failed - likely real connectivity issue
        return const NoConnectionException();
      }
      if (message.contains('connection refused')) {
        return ServerException(503);
      }
    }
    return const NoConnectionException();
  }

  static AppException _mapUnknownError(DioException e) {
    final error = e.error;
    if (error is SocketException) {
      return _mapConnectionError(e);
    }
    if (error is HandshakeException) {
      return const TlsException();
    }
    if (error is HttpException) {
      return ServerException(502);
    }
    // Fallback: show the actual error message for debugging
    final errorMsg = e.message ?? e.error?.toString() ?? 'Erro desconhecido';
    debugPrint('[ApiClient] Unknown error not mapped: $errorMsg');
    return ApiClientException(0, 'Erro de conexão: $errorMsg');
  }

  static AppException _mapStatusCode(Response<dynamic>? response) {
    final statusCode = response?.statusCode ?? 500;
    final data = response?.data;

    switch (statusCode) {
      case 401:
        final message = data is Map
            ? (data['message'] as String? ?? 'Não autenticado')
            : 'Não autenticado';
        return UnauthenticatedException(message);
      case 404:
        return const NotFoundException();
      case 422:
        if (data is Map) {
          final errors = <String, List<String>>{};
          final rawErrors = data['errors'];
          if (rawErrors is Map) {
            for (final entry in rawErrors.entries) {
              errors[entry.key.toString()] =
                  (entry.value as List).cast<String>();
            }
          }
          return ValidationException(
            fieldErrors: errors,
            message: data['message'] as String? ?? 'Erro de validação',
          );
        }
        return const ValidationException(fieldErrors: {});
      case 429:
        Duration? retryAfter;
        final retryHeader = response?.headers['retry-after']?.first;
        if (retryHeader != null) {
          final seconds = int.tryParse(retryHeader);
          if (seconds != null) retryAfter = Duration(seconds: seconds);
        }
        return RateLimitException(retryAfter: retryAfter);
      default:
        if (statusCode >= 500) return ServerException(statusCode);
        final message = data is Map
            ? (data['message'] as String? ?? 'Erro do cliente')
            : 'Erro do cliente';
        return ApiClientException(statusCode, message);
    }
  }
}
