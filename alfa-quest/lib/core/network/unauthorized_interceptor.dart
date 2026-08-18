import 'dart:async';

import 'package:dio/dio.dart';

class UnauthorizedInterceptor extends Interceptor {
  final Future<void> Function() onUnauthorized;
  bool _isHandling = false;

  UnauthorizedInterceptor({required this.onUnauthorized});

  @override
  void onError(DioException err, ErrorInterceptorHandler handler) {
    if (err.response?.statusCode == 401 && !_isHandling) {
      _isHandling = true;
      onUnauthorized().whenComplete(() => _isHandling = false);
    }
    handler.next(err);
  }
}
