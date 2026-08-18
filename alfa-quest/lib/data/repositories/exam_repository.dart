import 'dart:async';
import '../../core/cache/cache_manager.dart';
import '../../core/network/api_client.dart';
import '../models/exam.dart';

class ExamRepository {
  final ApiClient _api;
  final CacheManager _cache;

  ExamRepository({required ApiClient api, required CacheManager cache})
      : _api = api, _cache = cache;

  Future<List<Exam>> listExams({int? careerId}) async {
    final key = 'exams:list:career=${careerId ?? "all"}';
    final cached = await _cache.read<List<Exam>>(key, decode: (json) {
      return (json['items'] as List).map((e) => Exam.fromJson(e as Map<String, dynamic>)).toList();
    });

    if (cached != null) {
      if (cached.stale) unawaited(_revalidateList(key, careerId));
      return cached.value;
    }
    return _fetchList(key, careerId);
  }

  Future<List<Exam>> _fetchList(String key, int? careerId) async {
    final params = <String, dynamic>{};
    if (careerId != null) params['career_id'] = careerId;
    final response = await _api.get<Map<String, dynamic>>('/api/exams', queryParameters: params);
    final data = response.data!;
    final items = (data['data'] as List? ?? data['exams'] as List? ?? [data])
        .map((e) => Exam.fromJson(e as Map<String, dynamic>)).toList();
    await _cache.write(key, {'items': items.map((e) => e.toJson()).toList()});
    return items;
  }

  Future<void> _revalidateList(String key, int? careerId) async {
    try { await _fetchList(key, careerId); } catch (_) {}
  }

  Future<Exam> getExam(String id) async {
    final key = 'exams:detail:$id';
    final cached = await _cache.read<Exam>(key, decode: (json) => Exam.fromJson(json));
    if (cached != null) {
      if (cached.stale) unawaited(_revalidateDetail(key, id));
      return cached.value;
    }
    return _fetchDetail(key, id);
  }

  Future<Exam> _fetchDetail(String key, String id) async {
    final response = await _api.get<Map<String, dynamic>>('/api/exams/$id');
    final data = response.data!;
    final examJson = data.containsKey('data')
        ? data['data'] as Map<String, dynamic>
        : data;
    final exam = Exam.fromJson(examJson);
    await _cache.write(key, exam.toJson());
    return exam;
  }

  Future<void> _revalidateDetail(String key, String id) async {
    try { await _fetchDetail(key, id); } catch (_) {}
  }

  Future<StartAttemptResponse> startAttempt(String examId) async {
    final response = await _api.post<Map<String, dynamic>>('/api/exams/$examId/start');
    final data = response.data!;
    // API returns: {"data": {"attempt": {"id": ..., "examId": ...}, ...}}
    final wrapper = data['data'] as Map<String, dynamic>? ?? data;
    final attempt = wrapper['attempt'] as Map<String, dynamic>? ?? wrapper;
    return StartAttemptResponse(
      attemptId: (attempt['id'] ?? attempt['attemptId'] ?? '').toString(),
      examId: (attempt['examId'] ?? examId).toString(),
    );
  }
}
