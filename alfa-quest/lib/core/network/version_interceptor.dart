import 'package:dio/dio.dart';

import '../config/app_config.dart';
import '../errors/app_exception.dart';

class VersionInterceptor extends Interceptor {
  @override
  void onResponse(Response response, ResponseInterceptorHandler handler) {
    final minVersion = response.headers.value('x-api-min-version');
    if (minVersion != null && _isOutdated(AppConfig.versionName, minVersion)) {
      handler.reject(
        DioException(
          requestOptions: response.requestOptions,
          response: response,
          type: DioExceptionType.badResponse,
          error: const ForceUpdateRequiredException(),
        ),
      );
      return;
    }
    handler.next(response);
  }

  /// Compara semver simples (MAJOR.MINOR.PATCH)
  static bool _isOutdated(String current, String minimum) {
    final currentParts = current.split('.').map(int.tryParse).toList();
    final minimumParts = minimum.split('.').map(int.tryParse).toList();

    for (var i = 0; i < 3; i++) {
      final c = i < currentParts.length ? (currentParts[i] ?? 0) : 0;
      final m = i < minimumParts.length ? (minimumParts[i] ?? 0) : 0;
      if (c < m) return true;
      if (c > m) return false;
    }
    return false;
  }
}
