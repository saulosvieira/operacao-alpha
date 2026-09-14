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
    final body = response.data!;
    // Backend retorna { "data": { ...estatisticas... } }.
    final json = body['data'] is Map<String, dynamic>
        ? body['data'] as Map<String, dynamic>
        : body;
    final stats = PerformanceStatistics.fromJson(json);
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
    final rawList = data['data'] as List? ?? const [];
    final items = rawList
        .map((e) => HistoryItem.fromJson(_normalizeHistory(e as Map<String, dynamic>)))
        .toList();
    await _cache.write('perf:history', {'items': items.map((e) => e.toJson()).toList()});
    return items;
  }

  /// O backend (HistoryResource) envia snake_case e usa 'score' como
  /// percentual de acerto e 'completed_at' como data de conclusao.
  /// Convertemos para as chaves esperadas pelo modelo HistoryItem.
  Map<String, dynamic> _normalizeHistory(Map<String, dynamic> raw) {
    return <String, dynamic>{
      // Preserva chaves originais; as normalizadas abaixo tem precedencia.
      ...raw,
      if (raw['attempt_id'] != null) 'attemptId': raw['attempt_id'].toString(),
      if (raw['exam_id'] != null) 'examId': raw['exam_id'].toString(),
      if (raw['exam_title'] != null) 'examTitle': raw['exam_title'],
      if (raw['career_name'] != null) 'careerName': raw['career_name'],
      if (raw['completed_at'] != null) 'completedAt': raw['completed_at'],
      if (raw['score'] != null) 'accuracyPercentage': raw['score'],
      if (raw['correct_answers'] != null) 'correctAnswers': raw['correct_answers'],
      if (raw['total_questions'] != null) 'totalQuestions': raw['total_questions'],
      if (raw['time_spent_minutes'] != null) 'timeSpentMinutes': raw['time_spent_minutes'],
    };
  }

  Future<void> _revalidateHistory() async {
    try { await _fetchHistory(); } catch (_) {}
  }
}
