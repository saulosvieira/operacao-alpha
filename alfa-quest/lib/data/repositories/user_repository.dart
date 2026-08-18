import 'dart:async';
import '../../core/cache/cache_manager.dart';
import '../../core/network/api_client.dart';
import '../models/user.dart';

class UserRepository {
  final ApiClient _api;
  final CacheManager _cache;

  UserRepository({required ApiClient api, required CacheManager cache})
      : _api = api, _cache = cache;

  Future<User> getMe() async {
    final key = 'user:me';
    final cached = await _cache.read<User>(key, decode: (json) => User.fromJson(json));
    if (cached != null) {
      if (cached.stale) unawaited(_revalidateMe());
      return cached.value;
    }
    return _fetchMe();
  }

  Future<User> _fetchMe() async {
    final response = await _api.get<Map<String, dynamic>>('/api/me');
    final user = User.fromJson(response.data!);
    await _cache.write('user:me', user.toJson());
    return user;
  }

  Future<void> _revalidateMe() async {
    try { await _fetchMe(); } catch (_) {}
  }

  Future<User> getProfile() async {
    final key = 'user:profile';
    final cached = await _cache.read<User>(key, decode: (json) => User.fromJson(json));
    if (cached != null) {
      if (cached.stale) unawaited(_revalidateProfile());
      return cached.value;
    }
    return _fetchProfile();
  }

  Future<User> _fetchProfile() async {
    final response = await _api.get<Map<String, dynamic>>('/api/user/profile');
    final user = User.fromJson(response.data!);
    await _cache.write('user:profile', user.toJson());
    return user;
  }

  Future<void> _revalidateProfile() async {
    try { await _fetchProfile(); } catch (_) {}
  }

  Future<User> updateProfile(Map<String, dynamic> changes) async {
    final response = await _api.put<Map<String, dynamic>>('/api/user/profile', data: changes);
    final user = User.fromJson(response.data!);
    await _cache.write('user:profile', user.toJson());
    await _cache.write('user:me', user.toJson());
    return user;
  }

  Future<void> changePassword({required String currentPassword, required String newPassword, required String confirmPassword}) async {
    await _api.put('/api/user/password', data: {
      'current_password': currentPassword,
      'password': newPassword,
      'password_confirmation': confirmPassword,
    });
  }

  Future<void> deleteAccount() async {
    await _api.delete('/api/user/account');
  }
}
