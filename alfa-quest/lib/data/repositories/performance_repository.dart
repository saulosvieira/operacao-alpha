import 'dart:async';
import '../../core/cache/cache_manager.dart';
import '../../core/network/api_client.dart';
import '../models/performance.dart';
import '../models/history_item.dart';

class PerformanceRepository {
  final ApiClient _api;
  final CacheManager _cache;

  PerformanceRepository({required ApiClient api, required CacheManager cache})
      : _api = api, _cache = cache;

  Future<PerformanceStatistics> getStatistics() async {
    final key = 'perf:statistics';
    final cached = await _cache.read<PerformanceStatistics>(key, decode: (json) => PerformanceStatistics.fromJson(json));
    if (cached != null) {
      if (cached.stale) unawaited(_revalidateStats());
      return cached.value;
    }
    return _fetchStats();
  }

  Future<PerformanceStatistics> _fetchStats() async {
    final response = await _api.get<Map<String, dynamic>>('/api/performance/statistics');
    final stats = PerformanceStatistics.fromJson(response.data!);
    await _cache.write('perf:statistics', stats.toJson());
    return stats;
  }

  Future<void> _revalidateStats() async {
    try { await _fetchStats(); } catch (_) {}
  }

  Future<List<HistoryItem>> getHistory() async {
    final key = 'perf:history';
    final cached = await _cache.read<List<HistoryItem>>(key, decode: (json) {
      return (json['items'] as List).map((e) => HistoryItem.fromJson(e as Map<String, dynamic>)).toList();
    });
    if (cached != null) {
      if (cached.stale) unawaited(_revalidateHistory());
      return cached.value;
    }
    return _fetchHistory();
  }

  Future<List<HistoryItem>> _fetchHistory() async {
    final response = await _api.get<Map<String, dynamic>>('/api/performance/history');
    final data = response.data!;
    final items = (data['data'] as List? ?? []).map((e) => HistoryItem.fromJson(e as Map<String, dynamic>)).toList();
    await _cache.write('perf:history', {'items': items.map((e) => e.toJson()).toList()});
    return items;
  }

  Future<void> _revalidateHistory() async {
    try { await _fetchHistory(); } catch (_) {}
  }
}
