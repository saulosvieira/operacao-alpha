import 'package:drift/drift.dart';
import '../../core/storage/database.dart';
import '../models/notification_models.dart';

class NotificationsRepository {
  final AppDatabase _db;

  NotificationsRepository(this._db);

  Future<void> insert(AppInboxNotification notification) async {
    await _db.into(_db.inboxNotifications).insertOnConflictUpdate(
          InboxNotificationsCompanion(
            id: Value(notification.id),
            title: Value(notification.title),
            body: Value(notification.body),
            url: Value(notification.url),
            receivedAtMs: Value(notification.receivedAt.millisecondsSinceEpoch),
            read: Value(notification.read ? 1 : 0),
          ),
        );
    await _purgeOldest();
  }

  Future<List<AppInboxNotification>> listLatest({int limit = 100}) async {
    final rows = await (_db.select(_db.inboxNotifications)
          ..orderBy([(t) => OrderingTerm.desc(t.receivedAtMs)])
          ..limit(limit))
        .get();
    return rows.map(_mapRow).toList();
  }

  Future<void> markRead(String id) async {
    await (_db.update(_db.inboxNotifications)
          ..where((t) => t.id.equals(id)))
        .write(const InboxNotificationsCompanion(read: Value(1)));
  }

  Future<void> markAllRead() async {
    await _db
        .update(_db.inboxNotifications)
        .write(const InboxNotificationsCompanion(read: Value(1)));
  }

  Future<int> unreadCount() async {
    final result = await _db.customSelect(
      'SELECT COUNT(*) as cnt FROM inbox_notifications WHERE read = 0',
    ).getSingle();
    return result.data['cnt'] as int;
  }

  Stream<int> watchUnreadCount() {
    return _db
        .customSelect(
          'SELECT COUNT(*) as cnt FROM inbox_notifications WHERE read = 0',
          readsFrom: {_db.inboxNotifications},
        )
        .watch()
        .map((rows) => rows.first.data['cnt'] as int);
  }

  Future<void> _purgeOldest() async {
    final count = await _db.customSelect(
      'SELECT COUNT(*) as cnt FROM inbox_notifications',
    ).getSingle();
    if ((count.data['cnt'] as int) > 100) {
      await _db.customStatement(
        'DELETE FROM inbox_notifications WHERE id NOT IN '
        '(SELECT id FROM inbox_notifications ORDER BY received_at_ms DESC LIMIT 100)',
      );
    }
  }

  AppInboxNotification _mapRow(InboxNotification row) {
    return AppInboxNotification(
      id: row.id,
      title: row.title,
      body: row.body,
      url: row.url,
      receivedAt: DateTime.fromMillisecondsSinceEpoch(row.receivedAtMs),
      read: row.read == 1,
    );
  }
}
