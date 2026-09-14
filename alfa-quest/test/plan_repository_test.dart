import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';

import 'package:alfa_quest/core/cache/cache_manager.dart';
import 'package:alfa_quest/core/network/api_client.dart';
import 'package:alfa_quest/data/models/plan.dart';
import 'package:alfa_quest/data/repositories/plan_repository.dart';

class _MockApiClient extends Mock implements ApiClient {}

class _MockCacheManager extends Mock implements CacheManager {}

Response<Map<String, dynamic>> _resp(Map<String, dynamic> body, String path) =>
    Response(
      requestOptions: RequestOptions(path: path),
      statusCode: 200,
      data: body,
    );

void main() {
  late _MockApiClient api;
  late _MockCacheManager cache;
  late PlanRepository repo;

  setUp(() {
    api = _MockApiClient();
    cache = _MockCacheManager();
    repo = PlanRepository(api: api, cache: cache);
    when(() => cache.read<List<Plan>>(any(), decode: any(named: 'decode')))
        .thenAnswer((_) async => null);
    when(() => cache.write(any(), any())).thenAnswer((_) async {});
  });

  test('listPlans parseia o formato do backend (duration_days, id numerico)',
      () async {
    when(() => api.get<Map<String, dynamic>>('/api/plans'))
        .thenAnswer((_) async => _resp({
              'data': [
                {
                  'id': 7,
                  'name': 'Mensal',
                  'description': 'Plano mensal',
                  'type': 'monthly',
                  'price': 29.9,
                  'duration_days': 30,
                  'features': ['a', 'b'],
                },
              ],
            }, '/api/plans'));

    final plans = await repo.listPlans();
    expect(plans, hasLength(1));
    expect(plans[0].id, '7');
    expect(plans[0].durationDays, 30);
    expect(plans[0].price, 29.9);
    expect(plans[0].features, ['a', 'b']);
  });

  test('startCheckout mapeia checkout_url -> checkoutUrl', () async {
    when(() => api.post<Map<String, dynamic>>('/api/edduz/checkout',
            data: any(named: 'data')))
        .thenAnswer((_) async => _resp(
            {'checkout_url': 'https://pay.example/abc'}, '/api/edduz/checkout'));

    final res = await repo.startCheckout('7');
    expect(res.checkoutUrl, 'https://pay.example/abc');
  });
}
