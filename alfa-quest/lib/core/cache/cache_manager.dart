import 'dart:convert';

import 'package:drift/drift.dart';

import '../storage/database.dart';

class CacheEntry<T> {
  final T value;
  final DateTime fetchedAt;
  final bool stale;

  const CacheEntry({
    required this.value,
    required this.fetchedAt,
    required this.stale,
  });
}

class CacheManager {
  static const _maxBytes = 50 * 1024 * 1024; // 50MB
  static const _targetBytes = 40 * 1024 * 1024; // 40MB
  static const _staleAge = Duration(minutes: 5);

  final AppDatabase _db;

  CacheManager(this._db);

  Future<CacheEntry<T>?> read<T>(
    String key, {
    required T Function(Map<String, dynamic>) decode,
  }) async {
    final row = await (_db.select(_db.cacheEntries)
          ..where((t) => t.key.equals(key)))
        .getSingleOrNull();

    if (row == null) return null;

    final fetchedAt = DateTime.fromMillisecondsSinceEpoch(row.fetchedAtMs);
    final age = DateTime.now().difference(fetchedAt);
    final json = jsonDecode(row.jsonPayload) as Map<String, dynamic>;

    return CacheEntry<T>(
      value: decode(json),
      fetchedAt: fetchedAt,
      stale: age > _staleAge,
    );
  }

  Future<void> write<T>(
    String key,
    Map<String, dynamic> json,
  ) async {
    final payload = jsonEncode(json);
    final byteSize = utf8.encode(payload).length;

    await _db.into(_db.cacheEntries).insertOnConflictUpdate(
          CacheEntriesCompanion(
            key: Value(key),
            jsonPayload: Value(payload),
            byteSize: Value(byteSize),
            fetchedAtMs: Value(DateTime.now().millisecondsSinceEpoch),
          ),
        );

    await evictIfNeeded();
  }

  Future<void> invalidate(Iterable<String> keys) async {
    for (final key in keys) {
      await (_db.delete(_db.cacheEntries)
            ..where((t) => t.key.equals(key)))
          .go();
    }
  }

  Future<void> invalidateByPrefix(String prefix) async {
    await (_db.delete(_db.cacheEntries)
          ..where((t) => t.key.like('$prefix%')))
        .go();
  }

  Future<void> clearAll() async {
    await _db.delete(_db.cacheEntries).go();
  }

  Future<int> totalBytes() async {
    final result = await _db.customSelect(
      'SELECT COALESCE(SUM(byte_size), 0) as total FROM cache_entries',
    ).getSingle();
    return result.data['total'] as int;
  }

  Future<void> evictIfNeeded() async {
    final total = await totalBytes();
    if (total <= _maxBytes) return;

    // Remove LRU entries until <= 40MB
    final entries = await (_db.select(_db.cacheEntries)
          ..orderBy([(t) => OrderingTerm.asc(t.fetchedAtMs)]))
        .get();

    var currentTotal = total;
    for (final entry in entries) {
      if (currentTotal <= _targetBytes) break;
      await (_db.delete(_db.cacheEntries)
            ..where((t) => t.key.equals(entry.key)))
          .go();
      currentTotal -= entry.byteSize;
    }
  }
}
