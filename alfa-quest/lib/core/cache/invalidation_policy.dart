import 'cache_manager.dart';

enum CacheInvalidationEvent {
  examFinished,
  profileUpdated,
  subscriptionChanged,
  logout,
}

class InvalidationPolicy {
  final CacheManager _cache;

  InvalidationPolicy(this._cache);

  static const _eventKeys = <CacheInvalidationEvent, List<String>>{
    CacheInvalidationEvent.examFinished: [
      'perf:statistics',
      'perf:history',
      'ranking:weekly',
      'ranking:my_position',
    ],
    CacheInvalidationEvent.profileUpdated: [
      'user:profile',
      'user:me',
    ],
    CacheInvalidationEvent.subscriptionChanged: [
      'user:me',
    ],
  };

  Future<void> invalidate(CacheInvalidationEvent event) async {
    if (event == CacheInvalidationEvent.logout) {
      await _cache.clearAll();
      return;
    }

    final keys = _eventKeys[event];
    if (keys == null) return;

    await _cache.invalidate(keys);

    // Also invalidate exam lists by prefix
    if (event == CacheInvalidationEvent.examFinished ||
        event == CacheInvalidationEvent.subscriptionChanged) {
      await _cache.invalidateByPrefix('exams:');
    }
  }
}
