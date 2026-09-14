import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';

import 'package:alfa_quest/core/cache/cache_manager.dart';
import 'package:alfa_quest/core/network/api_client.dart';
import 'package:alfa_quest/data/models/ranking.dart';
import 'package:alfa_quest/data/repositories/ranking_repository.dart';

class _MockApiClient extends Mock implements ApiClient {}

class _MockCacheManager extends Mock implements CacheManager {}

Response<Map<String, dynamic>> _resp(Map<String, dynamic> body) => Response(
      requestOptions: RequestOptions(path: '/api/ranking'),
      statusCode: 200,
      data: body,
    );

void main() {
  late _MockApiClient api;
  late _MockCacheManager cache;
  late RankingRepository repo;

  setUp(() {
    api = _MockApiClient();
    cache = _MockCacheManager();
    repo = RankingRepository(api: api, cache: cache);

    // Cache sempre vazio -> forca o fetch na API.
    when(() => cache.read<List<RankingEntry>>(any(),
        decode: any(named: 'decode'))).thenAnswer((_) async => null);
    when(() => cache.read<MyRankingPosition>(any(),
        decode: any(named: 'decode'))).thenAnswer((_) async => null);
    when(() => cache.write(any(), any())).thenAnswer((_) async {});
  });

  test('getWeeklyRanking parseia o envelope real do backend (data.entries)',
      () async {
    // Formato retornado pelo Laravel JsonResource (RankingResource).
    final body = {
      'data': {
        'type': 'weekly',
        'entries': [
          {
            'userId': 10,
            'userName': 'Alice',
            'userAvatar': null,
            'score': 950,
            'totalExams': 5,
            'correctAnswers': 40,
            'position': 1,
            'careerId': null,
          },
          {
            'userId': 11,
            'userName': 'Bob',
            'score': 800,
            'position': 2,
          },
        ],
        'careerId': null,
        'careerName': null,
        'totalEntries': 2,
      },
    };

    when(() => api.get<Map<String, dynamic>>('/api/ranking',
            queryParameters: any(named: 'queryParameters')))
        .thenAnswer((_) async => _resp(body));

    final result = await repo.getWeeklyRanking();

    expect(result, hasLength(2));
    expect(result[0].position, 1);
    expect(result[0].userName, 'Alice');
    expect(result[0].score, 950);
    expect(result[1].userName, 'Bob');
    expect(result[1].userId, 11);
  });

  test('getWeeklyRanking retorna vazio quando entries ausente', () async {
    when(() => api.get<Map<String, dynamic>>('/api/ranking',
            queryParameters: any(named: 'queryParameters')))
        .thenAnswer((_) async => _resp({'data': {'type': 'weekly'}}));

    final result = await repo.getWeeklyRanking();
    expect(result, isEmpty);
  });

  test('getMyPosition parseia objeto aninhado em data', () async {
    when(() => api.get<Map<String, dynamic>>('/api/ranking/my-position'))
        .thenAnswer((_) async => _resp({
              'data': {
                'userId': 10,
                'userName': 'Alice',
                'score': 950,
                'position': 3,
              }
            }));

    final pos = await repo.getMyPosition();
    expect(pos, isNotNull);
    expect(pos!.position, 3);
    expect(pos.score, 950);
  });

  test('getMyPosition retorna null quando data == null', () async {
    when(() => api.get<Map<String, dynamic>>('/api/ranking/my-position'))
        .thenAnswer((_) async => _resp({'message': 'not found', 'data': null}));

    final pos = await repo.getMyPosition();
    expect(pos, isNull);
  });
}
