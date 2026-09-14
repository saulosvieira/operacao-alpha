import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:alfa_quest/core/network/auth_interceptor.dart';

/// Handler que captura as options repassadas por handler.next().
class _CapturingHandler extends RequestInterceptorHandler {
  RequestOptions? forwarded;

  @override
  void next(RequestOptions options) {
    forwarded = options;
    super.next(options);
  }
}

Future<RequestOptions> _run(AuthInterceptor interceptor, String path) async {
  final handler = _CapturingHandler();
  final options = RequestOptions(path: path);
  await interceptor.onRequest(options, handler);
  return handler.forwarded ?? options;
}

void main() {
  const token = 'abc.token.123';
  final interceptor = AuthInterceptor(tokenReader: () async => token);

  test('/api/plans agora envia o header Authorization', () async {
    final opts = await _run(interceptor, '/api/plans');
    expect(opts.headers['Authorization'], 'Bearer $token');
  });

  test('rotas autenticadas recebem o token', () async {
    for (final path in [
      '/api/user/profile',
      '/api/performance/statistics',
      '/api/ranking',
      '/api/exams/1/start',
    ]) {
      final opts = await _run(interceptor, path);
      expect(opts.headers['Authorization'], 'Bearer $token', reason: path);
    }
  });

  test('rotas publicas NAO recebem o token', () async {
    for (final path in ['/api/login', '/api/register', '/api/careers']) {
      final opts = await _run(interceptor, path);
      expect(opts.headers.containsKey('Authorization'), isFalse, reason: path);
    }
  });
}
