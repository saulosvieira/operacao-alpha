import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';

import 'package:alfa_quest/core/cache/cache_manager.dart';
import 'package:alfa_quest/core/network/api_client.dart';
import 'package:alfa_quest/data/models/user.dart';
import 'package:alfa_quest/data/repositories/user_repository.dart';

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
  late UserRepository repo;

  final userJson = {
    'id': '42',
    'name': 'Carlos',
    'email': 'carlos@example.com',
    'phone': null,
    'role': 'user',
    'subscriptionStatus': 'active',
    'subscriptionExpiresAt': null,
    'subscriptionPlatformId': null,
  };

  setUp(() {
    api = _MockApiClient();
    cache = _MockCacheManager();
    repo = UserRepository(api: api, cache: cache);

    when(() => cache.read<User>(any(), decode: any(named: 'decode')))
        .thenAnswer((_) async => null);
    when(() => cache.write(any(), any())).thenAnswer((_) async {});
  });

  test('getMe desembrulha o envelope { user: {...} } de /api/me', () async {
    when(() => api.get<Map<String, dynamic>>('/api/me'))
        .thenAnswer((_) async => _resp({'user': userJson}, '/api/me'));

    final user = await repo.getMe();
    expect(user.id, 42);
    expect(user.name, 'Carlos');
    expect(user.email, 'carlos@example.com');
    expect(user.subscriptionStatus, SubscriptionStatus.active);
  });

  test('getProfile desembrulha o envelope { data: {...} }', () async {
    when(() => api.get<Map<String, dynamic>>('/api/user/profile'))
        .thenAnswer((_) async => _resp({'data': userJson}, '/api/user/profile'));

    final user = await repo.getProfile();
    expect(user.id, 42);
    expect(user.email, 'carlos@example.com');
  });

  test('updateProfile desembrulha { message, data: {...} }', () async {
    when(() => api.put<Map<String, dynamic>>('/api/user/profile',
            data: any(named: 'data')))
        .thenAnswer((_) async => _resp(
            {'message': 'Profile updated successfully', 'data': userJson},
            '/api/user/profile'));

    final user = await repo.updateProfile({'name': 'Carlos'});
    expect(user.name, 'Carlos');
    expect(user.id, 42);
  });
}
