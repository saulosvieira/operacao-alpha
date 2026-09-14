import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';

import 'package:alfa_quest/core/cache/cache_manager.dart';
import 'package:alfa_quest/core/network/api_client.dart';
import 'package:alfa_quest/data/models/performance.dart';
import 'package:alfa_quest/data/repositories/performance_repository.dart';

class _MockApiClient extends Mock implements ApiClient {}

class _MockCacheManager extends Mock implements CacheManager {}

Response<Map<String, dynamic>> _resp(Map<String, dynamic> body) => Response(
      requestOptions: RequestOptions(path: '/api/performance/statistics'),
      statusCode: 200,
      data: body,
    );

void main() {
  late _MockApiClient api;
  late _MockCacheManager cache;
  late PerformanceRepository repo;

  setUp(() {
    api = _MockApiClient();
    cache = _MockCacheManager();
    repo = PerformanceRepository(api: api, cache: cache);
    when(() => cache.read<PerformanceStatistics>(any(),
        decode: any(named: 'decode'))).thenAnswer((_) async => null);
    when(() => cache.write(any(), any())).thenAnswer((_) async {});
  });

  test('getStatistics desembrulha o envelope { data: {...} }', () async {
    when(() => api.get<Map<String, dynamic>>('/api/performance/statistics'))
        .thenAnswer((_) async => _resp({
              'data': {
                'totalExamsCompleted': 12,
                'accuracyPercentage': 73.5,
                'totalQuestionsAnswered': 300,
                'totalCorrectAnswers': 220,
              }
            }));

    final stats = await repo.getStatistics();
    expect(stats.totalExamsCompleted, 12);
    expect(stats.accuracyPercentage, 73.5);
    expect(stats.totalQuestionsAnswered, 300);
  });
}
