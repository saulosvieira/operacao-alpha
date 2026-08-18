import 'dart:async';
import '../../core/cache/cache_manager.dart';
import '../../core/network/api_client.dart';
import '../../core/errors/app_exception.dart';
import '../models/ranking.dart';

class RankingRepository {
  final ApiClient _api;
  final CacheManager _cache;

  RankingRepository({required ApiClient api, required CacheManager cache})
      : _api = api, _cache = cache;

  Future<List<RankingEntry>> getWeeklyRanking() async {
    final key = 'ranking:weekly';
    final cached = await _cache.read<List<RankingEntry>>(key, decode: (json) {
      return (json['items'] as List).map((e) => RankingEntry.fromJson(e as Map<String, dynamic>)).toList();
    });
    if (cached != null) {
      if (cached.stale) unawaited(_revalidateWeekly());
      return cached.value;
    }
    return _fetchWeekly();
  }

  Future<List<RankingEntry>> _fetchWeekly() async {
    final response = await _api.get<Map<String, dynamic>>('/api/ranking', queryParameters: {'type': 'weekly'});
    final data = response.data!;
    final items = (data['data'] as List? ?? []).map((e) => RankingEntry.fromJson(e as Map<String, dynamic>)).toList();
    await _cache.write('ranking:weekly', {'items': items.map((e) => e.toJson()).toList()});
    return items;
  }

  Future<void> _revalidateWeekly() async {
    try { await _fetchWeekly(); } catch (_) {}
  }

  Future<MyRankingPosition?> getMyPosition() async {
    final key = 'ranking:my_position';
    final cached = await _cache.read<MyRankingPosition>(key, decode: (json) => MyRankingPosition.fromJson(json));
    if (cached != null) {
      if (cached.stale) unawaited(_revalidatePosition());
      return cached.value;
    }
    return _fetchPosition();
  }

  Future<MyRankingPosition?> _fetchPosition() async {
    try {
      final response = await _api.get<Map<String, dynamic>>('/api/ranking/my-position');
      final pos = MyRankingPosition.fromJson(response.data!);
      await _cache.write('ranking:my_position', pos.toJson());
      return pos;
    } on NotFoundException {
      return null;
    }
  }

  Future<void> _revalidatePosition() async {
    try { await _fetchPosition(); } catch (_) {}
  }
}
