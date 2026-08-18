import 'package:dio/dio.dart';

typedef TokenReader = Future<String?> Function();

class AuthInterceptor extends Interceptor {
  final TokenReader tokenReader;

  static const _publicPaths = [
    '/api/login',
    '/api/register',
    '/api/careers',
    '/api/plans',
  ];

  AuthInterceptor({required this.tokenReader});

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final isPublic = _publicPaths.any((p) => options.path.startsWith(p));
    if (!isPublic) {
      final token = await tokenReader();
      if (token != null) {
        options.headers['Authorization'] = 'Bearer $token';
      }
    }
    handler.next(options);
  }
}
