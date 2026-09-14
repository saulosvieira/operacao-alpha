import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';

import 'package:alfa_quest/core/cache/cache_manager.dart';
import 'package:alfa_quest/core/network/api_client.dart';
import 'package:alfa_quest/data/models/performance.dart';
import 'package:alfa_quest/data/models/history_item.dart';
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

  test('getHistory parseia o formato snake_case do backend', () async {
    when(() => cache.read<List<HistoryItem>>(any(), decode: any(named: 'decode')))
        .thenAnswer((_) async => null);
    when(() => api.get<Map<String, dynamic>>('/api/performance/history'))
        .thenAnswer((_) async => _resp({
              'data': [
                {
                  'exam_id': 'e1',
                  'exam_title': 'Simulado 1',
                  'career_name': 'Carreira X',
                  'score': 82.5,
                  'correct_answers': 33,
                  'total_questions': 40,
                  'time_spent_minutes': 55,
                  'completed_at': '2026-09-10T14:30:00Z',
                },
              ],
            }));

    final items = await repo.getHistory();
    expect(items, hasLength(1));
    final item = items.first;
    expect(item.examId, 'e1');
    expect(item.examTitle, 'Simulado 1');
    expect(item.accuracyPercentage, 82.5);
    expect(item.correctAnswers, 33);
    expect(item.totalQuestions, 40);
    expect(item.timeSpentMinutes, 55);
    expect(item.completedAt, isNotNull);
    expect(item.attemptId, isNull); // backend nao fornece
  });
}
