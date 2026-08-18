import 'package:drift/drift.dart';
import 'package:drift_flutter/drift_flutter.dart';

part 'database.g.dart';

class CacheEntries extends Table {
  TextColumn get key => text()();
  TextColumn get jsonPayload => text().named('json_payload')();
  IntColumn get byteSize => integer().named('byte_size')();
  IntColumn get fetchedAtMs => integer().named('fetched_at_ms')();

  @override
  Set<Column> get primaryKey => {key};
}

class InboxNotifications extends Table {
  TextColumn get id => text()();
  TextColumn get title => text().nullable()();
  TextColumn get body => text().nullable()();
  TextColumn get url => text().nullable()();
  IntColumn get receivedAtMs => integer().named('received_at_ms')();
  IntColumn get read => integer().withDefault(const Constant(0))();

  @override
  Set<Column> get primaryKey => {id};
}

class PendingFcmTokens extends Table {
  TextColumn get deviceId => text().named('device_id')();
  TextColumn get token => text()();
  IntColumn get attempts => integer().withDefault(const Constant(0))();
  IntColumn get lastAttemptMs =>
      integer().named('last_attempt_ms').withDefault(const Constant(0))();

  @override
  Set<Column> get primaryKey => {deviceId};
}

class PendingAttempts extends Table {
  TextColumn get attemptId => text().named('attempt_id')();
  TextColumn get examId => text().named('exam_id')();
  IntColumn get pausedAtMs => integer().named('paused_at_ms')();

  @override
  Set<Column> get primaryKey => {attemptId};
}

@DriftDatabase(
  tables: [CacheEntries, InboxNotifications, PendingFcmTokens, PendingAttempts],
)
class AppDatabase extends _$AppDatabase {
  AppDatabase() : super(_openConnection());

  @override
  int get schemaVersion => 1;

  static QueryExecutor _openConnection() {
    return driftDatabase(name: 'operacao_alfa_db');
  }
}
