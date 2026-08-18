// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'database.dart';

// ignore_for_file: type=lint
class $CacheEntriesTable extends CacheEntries
    with TableInfo<$CacheEntriesTable, CacheEntry> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $CacheEntriesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _keyMeta = const VerificationMeta('key');
  @override
  late final GeneratedColumn<String> key = GeneratedColumn<String>(
      'key', aliasedName, false,
      type: DriftSqlType.string, requiredDuringInsert: true);
  static const VerificationMeta _jsonPayloadMeta =
      const VerificationMeta('jsonPayload');
  @override
  late final GeneratedColumn<String> jsonPayload = GeneratedColumn<String>(
      'json_payload', aliasedName, false,
      type: DriftSqlType.string, requiredDuringInsert: true);
  static const VerificationMeta _byteSizeMeta =
      const VerificationMeta('byteSize');
  @override
  late final GeneratedColumn<int> byteSize = GeneratedColumn<int>(
      'byte_size', aliasedName, false,
      type: DriftSqlType.int, requiredDuringInsert: true);
  static const VerificationMeta _fetchedAtMsMeta =
      const VerificationMeta('fetchedAtMs');
  @override
  late final GeneratedColumn<int> fetchedAtMs = GeneratedColumn<int>(
      'fetched_at_ms', aliasedName, false,
      type: DriftSqlType.int, requiredDuringInsert: true);
  @override
  List<GeneratedColumn> get $columns =>
      [key, jsonPayload, byteSize, fetchedAtMs];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'cache_entries';
  @override
  VerificationContext validateIntegrity(Insertable<CacheEntry> instance,
      {bool isInserting = false}) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('key')) {
      context.handle(
          _keyMeta, key.isAcceptableOrUnknown(data['key']!, _keyMeta));
    } else if (isInserting) {
      context.missing(_keyMeta);
    }
    if (data.containsKey('json_payload')) {
      context.handle(
          _jsonPayloadMeta,
          jsonPayload.isAcceptableOrUnknown(
              data['json_payload']!, _jsonPayloadMeta));
    } else if (isInserting) {
      context.missing(_jsonPayloadMeta);
    }
    if (data.containsKey('byte_size')) {
      context.handle(_byteSizeMeta,
          byteSize.isAcceptableOrUnknown(data['byte_size']!, _byteSizeMeta));
    } else if (isInserting) {
      context.missing(_byteSizeMeta);
    }
    if (data.containsKey('fetched_at_ms')) {
      context.handle(
          _fetchedAtMsMeta,
          fetchedAtMs.isAcceptableOrUnknown(
              data['fetched_at_ms']!, _fetchedAtMsMeta));
    } else if (isInserting) {
      context.missing(_fetchedAtMsMeta);
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {key};
  @override
  CacheEntry map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return CacheEntry(
      key: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}key'])!,
      jsonPayload: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}json_payload'])!,
      byteSize: attachedDatabase.typeMapping
          .read(DriftSqlType.int, data['${effectivePrefix}byte_size'])!,
      fetchedAtMs: attachedDatabase.typeMapping
          .read(DriftSqlType.int, data['${effectivePrefix}fetched_at_ms'])!,
    );
  }

  @override
  $CacheEntriesTable createAlias(String alias) {
    return $CacheEntriesTable(attachedDatabase, alias);
  }
}

class CacheEntry extends DataClass implements Insertable<CacheEntry> {
  final String key;
  final String jsonPayload;
  final int byteSize;
  final int fetchedAtMs;
  const CacheEntry(
      {required this.key,
      required this.jsonPayload,
      required this.byteSize,
      required this.fetchedAtMs});
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['key'] = Variable<String>(key);
    map['json_payload'] = Variable<String>(jsonPayload);
    map['byte_size'] = Variable<int>(byteSize);
    map['fetched_at_ms'] = Variable<int>(fetchedAtMs);
    return map;
  }

  CacheEntriesCompanion toCompanion(bool nullToAbsent) {
    return CacheEntriesCompanion(
      key: Value(key),
      jsonPayload: Value(jsonPayload),
      byteSize: Value(byteSize),
      fetchedAtMs: Value(fetchedAtMs),
    );
  }

  factory CacheEntry.fromJson(Map<String, dynamic> json,
      {ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return CacheEntry(
      key: serializer.fromJson<String>(json['key']),
      jsonPayload: serializer.fromJson<String>(json['jsonPayload']),
      byteSize: serializer.fromJson<int>(json['byteSize']),
      fetchedAtMs: serializer.fromJson<int>(json['fetchedAtMs']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'key': serializer.toJson<String>(key),
      'jsonPayload': serializer.toJson<String>(jsonPayload),
      'byteSize': serializer.toJson<int>(byteSize),
      'fetchedAtMs': serializer.toJson<int>(fetchedAtMs),
    };
  }

  CacheEntry copyWith(
          {String? key,
          String? jsonPayload,
          int? byteSize,
          int? fetchedAtMs}) =>
      CacheEntry(
        key: key ?? this.key,
        jsonPayload: jsonPayload ?? this.jsonPayload,
        byteSize: byteSize ?? this.byteSize,
        fetchedAtMs: fetchedAtMs ?? this.fetchedAtMs,
      );
  CacheEntry copyWithCompanion(CacheEntriesCompanion data) {
    return CacheEntry(
      key: data.key.present ? data.key.value : this.key,
      jsonPayload:
          data.jsonPayload.present ? data.jsonPayload.value : this.jsonPayload,
      byteSize: data.byteSize.present ? data.byteSize.value : this.byteSize,
      fetchedAtMs:
          data.fetchedAtMs.present ? data.fetchedAtMs.value : this.fetchedAtMs,
    );
  }

  @override
  String toString() {
    return (StringBuffer('CacheEntry(')
          ..write('key: $key, ')
          ..write('jsonPayload: $jsonPayload, ')
          ..write('byteSize: $byteSize, ')
          ..write('fetchedAtMs: $fetchedAtMs')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(key, jsonPayload, byteSize, fetchedAtMs);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is CacheEntry &&
          other.key == this.key &&
          other.jsonPayload == this.jsonPayload &&
          other.byteSize == this.byteSize &&
          other.fetchedAtMs == this.fetchedAtMs);
}

class CacheEntriesCompanion extends UpdateCompanion<CacheEntry> {
  final Value<String> key;
  final Value<String> jsonPayload;
  final Value<int> byteSize;
  final Value<int> fetchedAtMs;
  final Value<int> rowid;
  const CacheEntriesCompanion({
    this.key = const Value.absent(),
    this.jsonPayload = const Value.absent(),
    this.byteSize = const Value.absent(),
    this.fetchedAtMs = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  CacheEntriesCompanion.insert({
    required String key,
    required String jsonPayload,
    required int byteSize,
    required int fetchedAtMs,
    this.rowid = const Value.absent(),
  })  : key = Value(key),
        jsonPayload = Value(jsonPayload),
        byteSize = Value(byteSize),
        fetchedAtMs = Value(fetchedAtMs);
  static Insertable<CacheEntry> custom({
    Expression<String>? key,
    Expression<String>? jsonPayload,
    Expression<int>? byteSize,
    Expression<int>? fetchedAtMs,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (key != null) 'key': key,
      if (jsonPayload != null) 'json_payload': jsonPayload,
      if (byteSize != null) 'byte_size': byteSize,
      if (fetchedAtMs != null) 'fetched_at_ms': fetchedAtMs,
      if (rowid != null) 'rowid': rowid,
    });
  }

  CacheEntriesCompanion copyWith(
      {Value<String>? key,
      Value<String>? jsonPayload,
      Value<int>? byteSize,
      Value<int>? fetchedAtMs,
      Value<int>? rowid}) {
    return CacheEntriesCompanion(
      key: key ?? this.key,
      jsonPayload: jsonPayload ?? this.jsonPayload,
      byteSize: byteSize ?? this.byteSize,
      fetchedAtMs: fetchedAtMs ?? this.fetchedAtMs,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (key.present) {
      map['key'] = Variable<String>(key.value);
    }
    if (jsonPayload.present) {
      map['json_payload'] = Variable<String>(jsonPayload.value);
    }
    if (byteSize.present) {
      map['byte_size'] = Variable<int>(byteSize.value);
    }
    if (fetchedAtMs.present) {
      map['fetched_at_ms'] = Variable<int>(fetchedAtMs.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('CacheEntriesCompanion(')
          ..write('key: $key, ')
          ..write('jsonPayload: $jsonPayload, ')
          ..write('byteSize: $byteSize, ')
          ..write('fetchedAtMs: $fetchedAtMs, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $InboxNotificationsTable extends InboxNotifications
    with TableInfo<$InboxNotificationsTable, InboxNotification> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $InboxNotificationsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
      'id', aliasedName, false,
      type: DriftSqlType.string, requiredDuringInsert: true);
  static const VerificationMeta _titleMeta = const VerificationMeta('title');
  @override
  late final GeneratedColumn<String> title = GeneratedColumn<String>(
      'title', aliasedName, true,
      type: DriftSqlType.string, requiredDuringInsert: false);
  static const VerificationMeta _bodyMeta = const VerificationMeta('body');
  @override
  late final GeneratedColumn<String> body = GeneratedColumn<String>(
      'body', aliasedName, true,
      type: DriftSqlType.string, requiredDuringInsert: false);
  static const VerificationMeta _urlMeta = const VerificationMeta('url');
  @override
  late final GeneratedColumn<String> url = GeneratedColumn<String>(
      'url', aliasedName, true,
      type: DriftSqlType.string, requiredDuringInsert: false);
  static const VerificationMeta _receivedAtMsMeta =
      const VerificationMeta('receivedAtMs');
  @override
  late final GeneratedColumn<int> receivedAtMs = GeneratedColumn<int>(
      'received_at_ms', aliasedName, false,
      type: DriftSqlType.int, requiredDuringInsert: true);
  static const VerificationMeta _readMeta = const VerificationMeta('read');
  @override
  late final GeneratedColumn<int> read = GeneratedColumn<int>(
      'read', aliasedName, false,
      type: DriftSqlType.int,
      requiredDuringInsert: false,
      defaultValue: const Constant(0));
  @override
  List<GeneratedColumn> get $columns =>
      [id, title, body, url, receivedAtMs, read];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'inbox_notifications';
  @override
  VerificationContext validateIntegrity(Insertable<InboxNotification> instance,
      {bool isInserting = false}) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('title')) {
      context.handle(
          _titleMeta, title.isAcceptableOrUnknown(data['title']!, _titleMeta));
    }
    if (data.containsKey('body')) {
      context.handle(
          _bodyMeta, body.isAcceptableOrUnknown(data['body']!, _bodyMeta));
    }
    if (data.containsKey('url')) {
      context.handle(
          _urlMeta, url.isAcceptableOrUnknown(data['url']!, _urlMeta));
    }
    if (data.containsKey('received_at_ms')) {
      context.handle(
          _receivedAtMsMeta,
          receivedAtMs.isAcceptableOrUnknown(
              data['received_at_ms']!, _receivedAtMsMeta));
    } else if (isInserting) {
      context.missing(_receivedAtMsMeta);
    }
    if (data.containsKey('read')) {
      context.handle(
          _readMeta, read.isAcceptableOrUnknown(data['read']!, _readMeta));
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  InboxNotification map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return InboxNotification(
      id: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}id'])!,
      title: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}title']),
      body: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}body']),
      url: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}url']),
      receivedAtMs: attachedDatabase.typeMapping
          .read(DriftSqlType.int, data['${effectivePrefix}received_at_ms'])!,
      read: attachedDatabase.typeMapping
          .read(DriftSqlType.int, data['${effectivePrefix}read'])!,
    );
  }

  @override
  $InboxNotificationsTable createAlias(String alias) {
    return $InboxNotificationsTable(attachedDatabase, alias);
  }
}

class InboxNotification extends DataClass
    implements Insertable<InboxNotification> {
  final String id;
  final String? title;
  final String? body;
  final String? url;
  final int receivedAtMs;
  final int read;
  const InboxNotification(
      {required this.id,
      this.title,
      this.body,
      this.url,
      required this.receivedAtMs,
      required this.read});
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    if (!nullToAbsent || title != null) {
      map['title'] = Variable<String>(title);
    }
    if (!nullToAbsent || body != null) {
      map['body'] = Variable<String>(body);
    }
    if (!nullToAbsent || url != null) {
      map['url'] = Variable<String>(url);
    }
    map['received_at_ms'] = Variable<int>(receivedAtMs);
    map['read'] = Variable<int>(read);
    return map;
  }

  InboxNotificationsCompanion toCompanion(bool nullToAbsent) {
    return InboxNotificationsCompanion(
      id: Value(id),
      title:
          title == null && nullToAbsent ? const Value.absent() : Value(title),
      body: body == null && nullToAbsent ? const Value.absent() : Value(body),
      url: url == null && nullToAbsent ? const Value.absent() : Value(url),
      receivedAtMs: Value(receivedAtMs),
      read: Value(read),
    );
  }

  factory InboxNotification.fromJson(Map<String, dynamic> json,
      {ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return InboxNotification(
      id: serializer.fromJson<String>(json['id']),
      title: serializer.fromJson<String?>(json['title']),
      body: serializer.fromJson<String?>(json['body']),
      url: serializer.fromJson<String?>(json['url']),
      receivedAtMs: serializer.fromJson<int>(json['receivedAtMs']),
      read: serializer.fromJson<int>(json['read']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'title': serializer.toJson<String?>(title),
      'body': serializer.toJson<String?>(body),
      'url': serializer.toJson<String?>(url),
      'receivedAtMs': serializer.toJson<int>(receivedAtMs),
      'read': serializer.toJson<int>(read),
    };
  }

  InboxNotification copyWith(
          {String? id,
          Value<String?> title = const Value.absent(),
          Value<String?> body = const Value.absent(),
          Value<String?> url = const Value.absent(),
          int? receivedAtMs,
          int? read}) =>
      InboxNotification(
        id: id ?? this.id,
        title: title.present ? title.value : this.title,
        body: body.present ? body.value : this.body,
        url: url.present ? url.value : this.url,
        receivedAtMs: receivedAtMs ?? this.receivedAtMs,
        read: read ?? this.read,
      );
  InboxNotification copyWithCompanion(InboxNotificationsCompanion data) {
    return InboxNotification(
      id: data.id.present ? data.id.value : this.id,
      title: data.title.present ? data.title.value : this.title,
      body: data.body.present ? data.body.value : this.body,
      url: data.url.present ? data.url.value : this.url,
      receivedAtMs: data.receivedAtMs.present
          ? data.receivedAtMs.value
          : this.receivedAtMs,
      read: data.read.present ? data.read.value : this.read,
    );
  }

  @override
  String toString() {
    return (StringBuffer('InboxNotification(')
          ..write('id: $id, ')
          ..write('title: $title, ')
          ..write('body: $body, ')
          ..write('url: $url, ')
          ..write('receivedAtMs: $receivedAtMs, ')
          ..write('read: $read')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(id, title, body, url, receivedAtMs, read);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is InboxNotification &&
          other.id == this.id &&
          other.title == this.title &&
          other.body == this.body &&
          other.url == this.url &&
          other.receivedAtMs == this.receivedAtMs &&
          other.read == this.read);
}

class InboxNotificationsCompanion extends UpdateCompanion<InboxNotification> {
  final Value<String> id;
  final Value<String?> title;
  final Value<String?> body;
  final Value<String?> url;
  final Value<int> receivedAtMs;
  final Value<int> read;
  final Value<int> rowid;
  const InboxNotificationsCompanion({
    this.id = const Value.absent(),
    this.title = const Value.absent(),
    this.body = const Value.absent(),
    this.url = const Value.absent(),
    this.receivedAtMs = const Value.absent(),
    this.read = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  InboxNotificationsCompanion.insert({
    required String id,
    this.title = const Value.absent(),
    this.body = const Value.absent(),
    this.url = const Value.absent(),
    required int receivedAtMs,
    this.read = const Value.absent(),
    this.rowid = const Value.absent(),
  })  : id = Value(id),
        receivedAtMs = Value(receivedAtMs);
  static Insertable<InboxNotification> custom({
    Expression<String>? id,
    Expression<String>? title,
    Expression<String>? body,
    Expression<String>? url,
    Expression<int>? receivedAtMs,
    Expression<int>? read,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (title != null) 'title': title,
      if (body != null) 'body': body,
      if (url != null) 'url': url,
      if (receivedAtMs != null) 'received_at_ms': receivedAtMs,
      if (read != null) 'read': read,
      if (rowid != null) 'rowid': rowid,
    });
  }

  InboxNotificationsCompanion copyWith(
      {Value<String>? id,
      Value<String?>? title,
      Value<String?>? body,
      Value<String?>? url,
      Value<int>? receivedAtMs,
      Value<int>? read,
      Value<int>? rowid}) {
    return InboxNotificationsCompanion(
      id: id ?? this.id,
      title: title ?? this.title,
      body: body ?? this.body,
      url: url ?? this.url,
      receivedAtMs: receivedAtMs ?? this.receivedAtMs,
      read: read ?? this.read,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (title.present) {
      map['title'] = Variable<String>(title.value);
    }
    if (body.present) {
      map['body'] = Variable<String>(body.value);
    }
    if (url.present) {
      map['url'] = Variable<String>(url.value);
    }
    if (receivedAtMs.present) {
      map['received_at_ms'] = Variable<int>(receivedAtMs.value);
    }
    if (read.present) {
      map['read'] = Variable<int>(read.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('InboxNotificationsCompanion(')
          ..write('id: $id, ')
          ..write('title: $title, ')
          ..write('body: $body, ')
          ..write('url: $url, ')
          ..write('receivedAtMs: $receivedAtMs, ')
          ..write('read: $read, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $PendingFcmTokensTable extends PendingFcmTokens
    with TableInfo<$PendingFcmTokensTable, PendingFcmToken> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $PendingFcmTokensTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _deviceIdMeta =
      const VerificationMeta('deviceId');
  @override
  late final GeneratedColumn<String> deviceId = GeneratedColumn<String>(
      'device_id', aliasedName, false,
      type: DriftSqlType.string, requiredDuringInsert: true);
  static const VerificationMeta _tokenMeta = const VerificationMeta('token');
  @override
  late final GeneratedColumn<String> token = GeneratedColumn<String>(
      'token', aliasedName, false,
      type: DriftSqlType.string, requiredDuringInsert: true);
  static const VerificationMeta _attemptsMeta =
      const VerificationMeta('attempts');
  @override
  late final GeneratedColumn<int> attempts = GeneratedColumn<int>(
      'attempts', aliasedName, false,
      type: DriftSqlType.int,
      requiredDuringInsert: false,
      defaultValue: const Constant(0));
  static const VerificationMeta _lastAttemptMsMeta =
      const VerificationMeta('lastAttemptMs');
  @override
  late final GeneratedColumn<int> lastAttemptMs = GeneratedColumn<int>(
      'last_attempt_ms', aliasedName, false,
      type: DriftSqlType.int,
      requiredDuringInsert: false,
      defaultValue: const Constant(0));
  @override
  List<GeneratedColumn> get $columns =>
      [deviceId, token, attempts, lastAttemptMs];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'pending_fcm_tokens';
  @override
  VerificationContext validateIntegrity(Insertable<PendingFcmToken> instance,
      {bool isInserting = false}) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('device_id')) {
      context.handle(_deviceIdMeta,
          deviceId.isAcceptableOrUnknown(data['device_id']!, _deviceIdMeta));
    } else if (isInserting) {
      context.missing(_deviceIdMeta);
    }
    if (data.containsKey('token')) {
      context.handle(
          _tokenMeta, token.isAcceptableOrUnknown(data['token']!, _tokenMeta));
    } else if (isInserting) {
      context.missing(_tokenMeta);
    }
    if (data.containsKey('attempts')) {
      context.handle(_attemptsMeta,
          attempts.isAcceptableOrUnknown(data['attempts']!, _attemptsMeta));
    }
    if (data.containsKey('last_attempt_ms')) {
      context.handle(
          _lastAttemptMsMeta,
          lastAttemptMs.isAcceptableOrUnknown(
              data['last_attempt_ms']!, _lastAttemptMsMeta));
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {deviceId};
  @override
  PendingFcmToken map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return PendingFcmToken(
      deviceId: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}device_id'])!,
      token: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}token'])!,
      attempts: attachedDatabase.typeMapping
          .read(DriftSqlType.int, data['${effectivePrefix}attempts'])!,
      lastAttemptMs: attachedDatabase.typeMapping
          .read(DriftSqlType.int, data['${effectivePrefix}last_attempt_ms'])!,
    );
  }

  @override
  $PendingFcmTokensTable createAlias(String alias) {
    return $PendingFcmTokensTable(attachedDatabase, alias);
  }
}

class PendingFcmToken extends DataClass implements Insertable<PendingFcmToken> {
  final String deviceId;
  final String token;
  final int attempts;
  final int lastAttemptMs;
  const PendingFcmToken(
      {required this.deviceId,
      required this.token,
      required this.attempts,
      required this.lastAttemptMs});
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['device_id'] = Variable<String>(deviceId);
    map['token'] = Variable<String>(token);
    map['attempts'] = Variable<int>(attempts);
    map['last_attempt_ms'] = Variable<int>(lastAttemptMs);
    return map;
  }

  PendingFcmTokensCompanion toCompanion(bool nullToAbsent) {
    return PendingFcmTokensCompanion(
      deviceId: Value(deviceId),
      token: Value(token),
      attempts: Value(attempts),
      lastAttemptMs: Value(lastAttemptMs),
    );
  }

  factory PendingFcmToken.fromJson(Map<String, dynamic> json,
      {ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return PendingFcmToken(
      deviceId: serializer.fromJson<String>(json['deviceId']),
      token: serializer.fromJson<String>(json['token']),
      attempts: serializer.fromJson<int>(json['attempts']),
      lastAttemptMs: serializer.fromJson<int>(json['lastAttemptMs']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'deviceId': serializer.toJson<String>(deviceId),
      'token': serializer.toJson<String>(token),
      'attempts': serializer.toJson<int>(attempts),
      'lastAttemptMs': serializer.toJson<int>(lastAttemptMs),
    };
  }

  PendingFcmToken copyWith(
          {String? deviceId,
          String? token,
          int? attempts,
          int? lastAttemptMs}) =>
      PendingFcmToken(
        deviceId: deviceId ?? this.deviceId,
        token: token ?? this.token,
        attempts: attempts ?? this.attempts,
        lastAttemptMs: lastAttemptMs ?? this.lastAttemptMs,
      );
  PendingFcmToken copyWithCompanion(PendingFcmTokensCompanion data) {
    return PendingFcmToken(
      deviceId: data.deviceId.present ? data.deviceId.value : this.deviceId,
      token: data.token.present ? data.token.value : this.token,
      attempts: data.attempts.present ? data.attempts.value : this.attempts,
      lastAttemptMs: data.lastAttemptMs.present
          ? data.lastAttemptMs.value
          : this.lastAttemptMs,
    );
  }

  @override
  String toString() {
    return (StringBuffer('PendingFcmToken(')
          ..write('deviceId: $deviceId, ')
          ..write('token: $token, ')
          ..write('attempts: $attempts, ')
          ..write('lastAttemptMs: $lastAttemptMs')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(deviceId, token, attempts, lastAttemptMs);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is PendingFcmToken &&
          other.deviceId == this.deviceId &&
          other.token == this.token &&
          other.attempts == this.attempts &&
          other.lastAttemptMs == this.lastAttemptMs);
}

class PendingFcmTokensCompanion extends UpdateCompanion<PendingFcmToken> {
  final Value<String> deviceId;
  final Value<String> token;
  final Value<int> attempts;
  final Value<int> lastAttemptMs;
  final Value<int> rowid;
  const PendingFcmTokensCompanion({
    this.deviceId = const Value.absent(),
    this.token = const Value.absent(),
    this.attempts = const Value.absent(),
    this.lastAttemptMs = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  PendingFcmTokensCompanion.insert({
    required String deviceId,
    required String token,
    this.attempts = const Value.absent(),
    this.lastAttemptMs = const Value.absent(),
    this.rowid = const Value.absent(),
  })  : deviceId = Value(deviceId),
        token = Value(token);
  static Insertable<PendingFcmToken> custom({
    Expression<String>? deviceId,
    Expression<String>? token,
    Expression<int>? attempts,
    Expression<int>? lastAttemptMs,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (deviceId != null) 'device_id': deviceId,
      if (token != null) 'token': token,
      if (attempts != null) 'attempts': attempts,
      if (lastAttemptMs != null) 'last_attempt_ms': lastAttemptMs,
      if (rowid != null) 'rowid': rowid,
    });
  }

  PendingFcmTokensCompanion copyWith(
      {Value<String>? deviceId,
      Value<String>? token,
      Value<int>? attempts,
      Value<int>? lastAttemptMs,
      Value<int>? rowid}) {
    return PendingFcmTokensCompanion(
      deviceId: deviceId ?? this.deviceId,
      token: token ?? this.token,
      attempts: attempts ?? this.attempts,
      lastAttemptMs: lastAttemptMs ?? this.lastAttemptMs,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (deviceId.present) {
      map['device_id'] = Variable<String>(deviceId.value);
    }
    if (token.present) {
      map['token'] = Variable<String>(token.value);
    }
    if (attempts.present) {
      map['attempts'] = Variable<int>(attempts.value);
    }
    if (lastAttemptMs.present) {
      map['last_attempt_ms'] = Variable<int>(lastAttemptMs.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('PendingFcmTokensCompanion(')
          ..write('deviceId: $deviceId, ')
          ..write('token: $token, ')
          ..write('attempts: $attempts, ')
          ..write('lastAttemptMs: $lastAttemptMs, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $PendingAttemptsTable extends PendingAttempts
    with TableInfo<$PendingAttemptsTable, PendingAttempt> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $PendingAttemptsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _attemptIdMeta =
      const VerificationMeta('attemptId');
  @override
  late final GeneratedColumn<String> attemptId = GeneratedColumn<String>(
      'attempt_id', aliasedName, false,
      type: DriftSqlType.string, requiredDuringInsert: true);
  static const VerificationMeta _examIdMeta = const VerificationMeta('examId');
  @override
  late final GeneratedColumn<String> examId = GeneratedColumn<String>(
      'exam_id', aliasedName, false,
      type: DriftSqlType.string, requiredDuringInsert: true);
  static const VerificationMeta _pausedAtMsMeta =
      const VerificationMeta('pausedAtMs');
  @override
  late final GeneratedColumn<int> pausedAtMs = GeneratedColumn<int>(
      'paused_at_ms', aliasedName, false,
      type: DriftSqlType.int, requiredDuringInsert: true);
  @override
  List<GeneratedColumn> get $columns => [attemptId, examId, pausedAtMs];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'pending_attempts';
  @override
  VerificationContext validateIntegrity(Insertable<PendingAttempt> instance,
      {bool isInserting = false}) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('attempt_id')) {
      context.handle(_attemptIdMeta,
          attemptId.isAcceptableOrUnknown(data['attempt_id']!, _attemptIdMeta));
    } else if (isInserting) {
      context.missing(_attemptIdMeta);
    }
    if (data.containsKey('exam_id')) {
      context.handle(_examIdMeta,
          examId.isAcceptableOrUnknown(data['exam_id']!, _examIdMeta));
    } else if (isInserting) {
      context.missing(_examIdMeta);
    }
    if (data.containsKey('paused_at_ms')) {
      context.handle(
          _pausedAtMsMeta,
          pausedAtMs.isAcceptableOrUnknown(
              data['paused_at_ms']!, _pausedAtMsMeta));
    } else if (isInserting) {
      context.missing(_pausedAtMsMeta);
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {attemptId};
  @override
  PendingAttempt map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return PendingAttempt(
      attemptId: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}attempt_id'])!,
      examId: attachedDatabase.typeMapping
          .read(DriftSqlType.string, data['${effectivePrefix}exam_id'])!,
      pausedAtMs: attachedDatabase.typeMapping
          .read(DriftSqlType.int, data['${effectivePrefix}paused_at_ms'])!,
    );
  }

  @override
  $PendingAttemptsTable createAlias(String alias) {
    return $PendingAttemptsTable(attachedDatabase, alias);
  }
}

class PendingAttempt extends DataClass implements Insertable<PendingAttempt> {
  final String attemptId;
  final String examId;
  final int pausedAtMs;
  const PendingAttempt(
      {required this.attemptId,
      required this.examId,
      required this.pausedAtMs});
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['attempt_id'] = Variable<String>(attemptId);
    map['exam_id'] = Variable<String>(examId);
    map['paused_at_ms'] = Variable<int>(pausedAtMs);
    return map;
  }

  PendingAttemptsCompanion toCompanion(bool nullToAbsent) {
    return PendingAttemptsCompanion(
      attemptId: Value(attemptId),
      examId: Value(examId),
      pausedAtMs: Value(pausedAtMs),
    );
  }

  factory PendingAttempt.fromJson(Map<String, dynamic> json,
      {ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return PendingAttempt(
      attemptId: serializer.fromJson<String>(json['attemptId']),
      examId: serializer.fromJson<String>(json['examId']),
      pausedAtMs: serializer.fromJson<int>(json['pausedAtMs']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'attemptId': serializer.toJson<String>(attemptId),
      'examId': serializer.toJson<String>(examId),
      'pausedAtMs': serializer.toJson<int>(pausedAtMs),
    };
  }

  PendingAttempt copyWith(
          {String? attemptId, String? examId, int? pausedAtMs}) =>
      PendingAttempt(
        attemptId: attemptId ?? this.attemptId,
        examId: examId ?? this.examId,
        pausedAtMs: pausedAtMs ?? this.pausedAtMs,
      );
  PendingAttempt copyWithCompanion(PendingAttemptsCompanion data) {
    return PendingAttempt(
      attemptId: data.attemptId.present ? data.attemptId.value : this.attemptId,
      examId: data.examId.present ? data.examId.value : this.examId,
      pausedAtMs:
          data.pausedAtMs.present ? data.pausedAtMs.value : this.pausedAtMs,
    );
  }

  @override
  String toString() {
    return (StringBuffer('PendingAttempt(')
          ..write('attemptId: $attemptId, ')
          ..write('examId: $examId, ')
          ..write('pausedAtMs: $pausedAtMs')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(attemptId, examId, pausedAtMs);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is PendingAttempt &&
          other.attemptId == this.attemptId &&
          other.examId == this.examId &&
          other.pausedAtMs == this.pausedAtMs);
}

class PendingAttemptsCompanion extends UpdateCompanion<PendingAttempt> {
  final Value<String> attemptId;
  final Value<String> examId;
  final Value<int> pausedAtMs;
  final Value<int> rowid;
  const PendingAttemptsCompanion({
    this.attemptId = const Value.absent(),
    this.examId = const Value.absent(),
    this.pausedAtMs = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  PendingAttemptsCompanion.insert({
    required String attemptId,
    required String examId,
    required int pausedAtMs,
    this.rowid = const Value.absent(),
  })  : attemptId = Value(attemptId),
        examId = Value(examId),
        pausedAtMs = Value(pausedAtMs);
  static Insertable<PendingAttempt> custom({
    Expression<String>? attemptId,
    Expression<String>? examId,
    Expression<int>? pausedAtMs,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (attemptId != null) 'attempt_id': attemptId,
      if (examId != null) 'exam_id': examId,
      if (pausedAtMs != null) 'paused_at_ms': pausedAtMs,
      if (rowid != null) 'rowid': rowid,
    });
  }

  PendingAttemptsCompanion copyWith(
      {Value<String>? attemptId,
      Value<String>? examId,
      Value<int>? pausedAtMs,
      Value<int>? rowid}) {
    return PendingAttemptsCompanion(
      attemptId: attemptId ?? this.attemptId,
      examId: examId ?? this.examId,
      pausedAtMs: pausedAtMs ?? this.pausedAtMs,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (attemptId.present) {
      map['attempt_id'] = Variable<String>(attemptId.value);
    }
    if (examId.present) {
      map['exam_id'] = Variable<String>(examId.value);
    }
    if (pausedAtMs.present) {
      map['paused_at_ms'] = Variable<int>(pausedAtMs.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('PendingAttemptsCompanion(')
          ..write('attemptId: $attemptId, ')
          ..write('examId: $examId, ')
          ..write('pausedAtMs: $pausedAtMs, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

abstract class _$AppDatabase extends GeneratedDatabase {
  _$AppDatabase(QueryExecutor e) : super(e);
  $AppDatabaseManager get managers => $AppDatabaseManager(this);
  late final $CacheEntriesTable cacheEntries = $CacheEntriesTable(this);
  late final $InboxNotificationsTable inboxNotifications =
      $InboxNotificationsTable(this);
  late final $PendingFcmTokensTable pendingFcmTokens =
      $PendingFcmTokensTable(this);
  late final $PendingAttemptsTable pendingAttempts =
      $PendingAttemptsTable(this);
  @override
  Iterable<TableInfo<Table, Object?>> get allTables =>
      allSchemaEntities.whereType<TableInfo<Table, Object?>>();
  @override
  List<DatabaseSchemaEntity> get allSchemaEntities =>
      [cacheEntries, inboxNotifications, pendingFcmTokens, pendingAttempts];
}

typedef $$CacheEntriesTableCreateCompanionBuilder = CacheEntriesCompanion
    Function({
  required String key,
  required String jsonPayload,
  required int byteSize,
  required int fetchedAtMs,
  Value<int> rowid,
});
typedef $$CacheEntriesTableUpdateCompanionBuilder = CacheEntriesCompanion
    Function({
  Value<String> key,
  Value<String> jsonPayload,
  Value<int> byteSize,
  Value<int> fetchedAtMs,
  Value<int> rowid,
});

class $$CacheEntriesTableFilterComposer
    extends Composer<_$AppDatabase, $CacheEntriesTable> {
  $$CacheEntriesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get key => $composableBuilder(
      column: $table.key, builder: (column) => ColumnFilters(column));

  ColumnFilters<String> get jsonPayload => $composableBuilder(
      column: $table.jsonPayload, builder: (column) => ColumnFilters(column));

  ColumnFilters<int> get byteSize => $composableBuilder(
      column: $table.byteSize, builder: (column) => ColumnFilters(column));

  ColumnFilters<int> get fetchedAtMs => $composableBuilder(
      column: $table.fetchedAtMs, builder: (column) => ColumnFilters(column));
}

class $$CacheEntriesTableOrderingComposer
    extends Composer<_$AppDatabase, $CacheEntriesTable> {
  $$CacheEntriesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get key => $composableBuilder(
      column: $table.key, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<String> get jsonPayload => $composableBuilder(
      column: $table.jsonPayload, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<int> get byteSize => $composableBuilder(
      column: $table.byteSize, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<int> get fetchedAtMs => $composableBuilder(
      column: $table.fetchedAtMs, builder: (column) => ColumnOrderings(column));
}

class $$CacheEntriesTableAnnotationComposer
    extends Composer<_$AppDatabase, $CacheEntriesTable> {
  $$CacheEntriesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get key =>
      $composableBuilder(column: $table.key, builder: (column) => column);

  GeneratedColumn<String> get jsonPayload => $composableBuilder(
      column: $table.jsonPayload, builder: (column) => column);

  GeneratedColumn<int> get byteSize =>
      $composableBuilder(column: $table.byteSize, builder: (column) => column);

  GeneratedColumn<int> get fetchedAtMs => $composableBuilder(
      column: $table.fetchedAtMs, builder: (column) => column);
}

class $$CacheEntriesTableTableManager extends RootTableManager<
    _$AppDatabase,
    $CacheEntriesTable,
    CacheEntry,
    $$CacheEntriesTableFilterComposer,
    $$CacheEntriesTableOrderingComposer,
    $$CacheEntriesTableAnnotationComposer,
    $$CacheEntriesTableCreateCompanionBuilder,
    $$CacheEntriesTableUpdateCompanionBuilder,
    (CacheEntry, BaseReferences<_$AppDatabase, $CacheEntriesTable, CacheEntry>),
    CacheEntry,
    PrefetchHooks Function()> {
  $$CacheEntriesTableTableManager(_$AppDatabase db, $CacheEntriesTable table)
      : super(TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$CacheEntriesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$CacheEntriesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$CacheEntriesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback: ({
            Value<String> key = const Value.absent(),
            Value<String> jsonPayload = const Value.absent(),
            Value<int> byteSize = const Value.absent(),
            Value<int> fetchedAtMs = const Value.absent(),
            Value<int> rowid = const Value.absent(),
          }) =>
              CacheEntriesCompanion(
            key: key,
            jsonPayload: jsonPayload,
            byteSize: byteSize,
            fetchedAtMs: fetchedAtMs,
            rowid: rowid,
          ),
          createCompanionCallback: ({
            required String key,
            required String jsonPayload,
            required int byteSize,
            required int fetchedAtMs,
            Value<int> rowid = const Value.absent(),
          }) =>
              CacheEntriesCompanion.insert(
            key: key,
            jsonPayload: jsonPayload,
            byteSize: byteSize,
            fetchedAtMs: fetchedAtMs,
            rowid: rowid,
          ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ));
}

typedef $$CacheEntriesTableProcessedTableManager = ProcessedTableManager<
    _$AppDatabase,
    $CacheEntriesTable,
    CacheEntry,
    $$CacheEntriesTableFilterComposer,
    $$CacheEntriesTableOrderingComposer,
    $$CacheEntriesTableAnnotationComposer,
    $$CacheEntriesTableCreateCompanionBuilder,
    $$CacheEntriesTableUpdateCompanionBuilder,
    (CacheEntry, BaseReferences<_$AppDatabase, $CacheEntriesTable, CacheEntry>),
    CacheEntry,
    PrefetchHooks Function()>;
typedef $$InboxNotificationsTableCreateCompanionBuilder
    = InboxNotificationsCompanion Function({
  required String id,
  Value<String?> title,
  Value<String?> body,
  Value<String?> url,
  required int receivedAtMs,
  Value<int> read,
  Value<int> rowid,
});
typedef $$InboxNotificationsTableUpdateCompanionBuilder
    = InboxNotificationsCompanion Function({
  Value<String> id,
  Value<String?> title,
  Value<String?> body,
  Value<String?> url,
  Value<int> receivedAtMs,
  Value<int> read,
  Value<int> rowid,
});

class $$InboxNotificationsTableFilterComposer
    extends Composer<_$AppDatabase, $InboxNotificationsTable> {
  $$InboxNotificationsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
      column: $table.id, builder: (column) => ColumnFilters(column));

  ColumnFilters<String> get title => $composableBuilder(
      column: $table.title, builder: (column) => ColumnFilters(column));

  ColumnFilters<String> get body => $composableBuilder(
      column: $table.body, builder: (column) => ColumnFilters(column));

  ColumnFilters<String> get url => $composableBuilder(
      column: $table.url, builder: (column) => ColumnFilters(column));

  ColumnFilters<int> get receivedAtMs => $composableBuilder(
      column: $table.receivedAtMs, builder: (column) => ColumnFilters(column));

  ColumnFilters<int> get read => $composableBuilder(
      column: $table.read, builder: (column) => ColumnFilters(column));
}

class $$InboxNotificationsTableOrderingComposer
    extends Composer<_$AppDatabase, $InboxNotificationsTable> {
  $$InboxNotificationsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
      column: $table.id, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<String> get title => $composableBuilder(
      column: $table.title, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<String> get body => $composableBuilder(
      column: $table.body, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<String> get url => $composableBuilder(
      column: $table.url, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<int> get receivedAtMs => $composableBuilder(
      column: $table.receivedAtMs,
      builder: (column) => ColumnOrderings(column));

  ColumnOrderings<int> get read => $composableBuilder(
      column: $table.read, builder: (column) => ColumnOrderings(column));
}

class $$InboxNotificationsTableAnnotationComposer
    extends Composer<_$AppDatabase, $InboxNotificationsTable> {
  $$InboxNotificationsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get title =>
      $composableBuilder(column: $table.title, builder: (column) => column);

  GeneratedColumn<String> get body =>
      $composableBuilder(column: $table.body, builder: (column) => column);

  GeneratedColumn<String> get url =>
      $composableBuilder(column: $table.url, builder: (column) => column);

  GeneratedColumn<int> get receivedAtMs => $composableBuilder(
      column: $table.receivedAtMs, builder: (column) => column);

  GeneratedColumn<int> get read =>
      $composableBuilder(column: $table.read, builder: (column) => column);
}

class $$InboxNotificationsTableTableManager extends RootTableManager<
    _$AppDatabase,
    $InboxNotificationsTable,
    InboxNotification,
    $$InboxNotificationsTableFilterComposer,
    $$InboxNotificationsTableOrderingComposer,
    $$InboxNotificationsTableAnnotationComposer,
    $$InboxNotificationsTableCreateCompanionBuilder,
    $$InboxNotificationsTableUpdateCompanionBuilder,
    (
      InboxNotification,
      BaseReferences<_$AppDatabase, $InboxNotificationsTable, InboxNotification>
    ),
    InboxNotification,
    PrefetchHooks Function()> {
  $$InboxNotificationsTableTableManager(
      _$AppDatabase db, $InboxNotificationsTable table)
      : super(TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$InboxNotificationsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$InboxNotificationsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$InboxNotificationsTableAnnotationComposer(
                  $db: db, $table: table),
          updateCompanionCallback: ({
            Value<String> id = const Value.absent(),
            Value<String?> title = const Value.absent(),
            Value<String?> body = const Value.absent(),
            Value<String?> url = const Value.absent(),
            Value<int> receivedAtMs = const Value.absent(),
            Value<int> read = const Value.absent(),
            Value<int> rowid = const Value.absent(),
          }) =>
              InboxNotificationsCompanion(
            id: id,
            title: title,
            body: body,
            url: url,
            receivedAtMs: receivedAtMs,
            read: read,
            rowid: rowid,
          ),
          createCompanionCallback: ({
            required String id,
            Value<String?> title = const Value.absent(),
            Value<String?> body = const Value.absent(),
            Value<String?> url = const Value.absent(),
            required int receivedAtMs,
            Value<int> read = const Value.absent(),
            Value<int> rowid = const Value.absent(),
          }) =>
              InboxNotificationsCompanion.insert(
            id: id,
            title: title,
            body: body,
            url: url,
            receivedAtMs: receivedAtMs,
            read: read,
            rowid: rowid,
          ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ));
}

typedef $$InboxNotificationsTableProcessedTableManager = ProcessedTableManager<
    _$AppDatabase,
    $InboxNotificationsTable,
    InboxNotification,
    $$InboxNotificationsTableFilterComposer,
    $$InboxNotificationsTableOrderingComposer,
    $$InboxNotificationsTableAnnotationComposer,
    $$InboxNotificationsTableCreateCompanionBuilder,
    $$InboxNotificationsTableUpdateCompanionBuilder,
    (
      InboxNotification,
      BaseReferences<_$AppDatabase, $InboxNotificationsTable, InboxNotification>
    ),
    InboxNotification,
    PrefetchHooks Function()>;
typedef $$PendingFcmTokensTableCreateCompanionBuilder
    = PendingFcmTokensCompanion Function({
  required String deviceId,
  required String token,
  Value<int> attempts,
  Value<int> lastAttemptMs,
  Value<int> rowid,
});
typedef $$PendingFcmTokensTableUpdateCompanionBuilder
    = PendingFcmTokensCompanion Function({
  Value<String> deviceId,
  Value<String> token,
  Value<int> attempts,
  Value<int> lastAttemptMs,
  Value<int> rowid,
});

class $$PendingFcmTokensTableFilterComposer
    extends Composer<_$AppDatabase, $PendingFcmTokensTable> {
  $$PendingFcmTokensTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get deviceId => $composableBuilder(
      column: $table.deviceId, builder: (column) => ColumnFilters(column));

  ColumnFilters<String> get token => $composableBuilder(
      column: $table.token, builder: (column) => ColumnFilters(column));

  ColumnFilters<int> get attempts => $composableBuilder(
      column: $table.attempts, builder: (column) => ColumnFilters(column));

  ColumnFilters<int> get lastAttemptMs => $composableBuilder(
      column: $table.lastAttemptMs, builder: (column) => ColumnFilters(column));
}

class $$PendingFcmTokensTableOrderingComposer
    extends Composer<_$AppDatabase, $PendingFcmTokensTable> {
  $$PendingFcmTokensTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get deviceId => $composableBuilder(
      column: $table.deviceId, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<String> get token => $composableBuilder(
      column: $table.token, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<int> get attempts => $composableBuilder(
      column: $table.attempts, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<int> get lastAttemptMs => $composableBuilder(
      column: $table.lastAttemptMs,
      builder: (column) => ColumnOrderings(column));
}

class $$PendingFcmTokensTableAnnotationComposer
    extends Composer<_$AppDatabase, $PendingFcmTokensTable> {
  $$PendingFcmTokensTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get deviceId =>
      $composableBuilder(column: $table.deviceId, builder: (column) => column);

  GeneratedColumn<String> get token =>
      $composableBuilder(column: $table.token, builder: (column) => column);

  GeneratedColumn<int> get attempts =>
      $composableBuilder(column: $table.attempts, builder: (column) => column);

  GeneratedColumn<int> get lastAttemptMs => $composableBuilder(
      column: $table.lastAttemptMs, builder: (column) => column);
}

class $$PendingFcmTokensTableTableManager extends RootTableManager<
    _$AppDatabase,
    $PendingFcmTokensTable,
    PendingFcmToken,
    $$PendingFcmTokensTableFilterComposer,
    $$PendingFcmTokensTableOrderingComposer,
    $$PendingFcmTokensTableAnnotationComposer,
    $$PendingFcmTokensTableCreateCompanionBuilder,
    $$PendingFcmTokensTableUpdateCompanionBuilder,
    (
      PendingFcmToken,
      BaseReferences<_$AppDatabase, $PendingFcmTokensTable, PendingFcmToken>
    ),
    PendingFcmToken,
    PrefetchHooks Function()> {
  $$PendingFcmTokensTableTableManager(
      _$AppDatabase db, $PendingFcmTokensTable table)
      : super(TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$PendingFcmTokensTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$PendingFcmTokensTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$PendingFcmTokensTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback: ({
            Value<String> deviceId = const Value.absent(),
            Value<String> token = const Value.absent(),
            Value<int> attempts = const Value.absent(),
            Value<int> lastAttemptMs = const Value.absent(),
            Value<int> rowid = const Value.absent(),
          }) =>
              PendingFcmTokensCompanion(
            deviceId: deviceId,
            token: token,
            attempts: attempts,
            lastAttemptMs: lastAttemptMs,
            rowid: rowid,
          ),
          createCompanionCallback: ({
            required String deviceId,
            required String token,
            Value<int> attempts = const Value.absent(),
            Value<int> lastAttemptMs = const Value.absent(),
            Value<int> rowid = const Value.absent(),
          }) =>
              PendingFcmTokensCompanion.insert(
            deviceId: deviceId,
            token: token,
            attempts: attempts,
            lastAttemptMs: lastAttemptMs,
            rowid: rowid,
          ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ));
}

typedef $$PendingFcmTokensTableProcessedTableManager = ProcessedTableManager<
    _$AppDatabase,
    $PendingFcmTokensTable,
    PendingFcmToken,
    $$PendingFcmTokensTableFilterComposer,
    $$PendingFcmTokensTableOrderingComposer,
    $$PendingFcmTokensTableAnnotationComposer,
    $$PendingFcmTokensTableCreateCompanionBuilder,
    $$PendingFcmTokensTableUpdateCompanionBuilder,
    (
      PendingFcmToken,
      BaseReferences<_$AppDatabase, $PendingFcmTokensTable, PendingFcmToken>
    ),
    PendingFcmToken,
    PrefetchHooks Function()>;
typedef $$PendingAttemptsTableCreateCompanionBuilder = PendingAttemptsCompanion
    Function({
  required String attemptId,
  required String examId,
  required int pausedAtMs,
  Value<int> rowid,
});
typedef $$PendingAttemptsTableUpdateCompanionBuilder = PendingAttemptsCompanion
    Function({
  Value<String> attemptId,
  Value<String> examId,
  Value<int> pausedAtMs,
  Value<int> rowid,
});

class $$PendingAttemptsTableFilterComposer
    extends Composer<_$AppDatabase, $PendingAttemptsTable> {
  $$PendingAttemptsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get attemptId => $composableBuilder(
      column: $table.attemptId, builder: (column) => ColumnFilters(column));

  ColumnFilters<String> get examId => $composableBuilder(
      column: $table.examId, builder: (column) => ColumnFilters(column));

  ColumnFilters<int> get pausedAtMs => $composableBuilder(
      column: $table.pausedAtMs, builder: (column) => ColumnFilters(column));
}

class $$PendingAttemptsTableOrderingComposer
    extends Composer<_$AppDatabase, $PendingAttemptsTable> {
  $$PendingAttemptsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get attemptId => $composableBuilder(
      column: $table.attemptId, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<String> get examId => $composableBuilder(
      column: $table.examId, builder: (column) => ColumnOrderings(column));

  ColumnOrderings<int> get pausedAtMs => $composableBuilder(
      column: $table.pausedAtMs, builder: (column) => ColumnOrderings(column));
}

class $$PendingAttemptsTableAnnotationComposer
    extends Composer<_$AppDatabase, $PendingAttemptsTable> {
  $$PendingAttemptsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get attemptId =>
      $composableBuilder(column: $table.attemptId, builder: (column) => column);

  GeneratedColumn<String> get examId =>
      $composableBuilder(column: $table.examId, builder: (column) => column);

  GeneratedColumn<int> get pausedAtMs => $composableBuilder(
      column: $table.pausedAtMs, builder: (column) => column);
}

class $$PendingAttemptsTableTableManager extends RootTableManager<
    _$AppDatabase,
    $PendingAttemptsTable,
    PendingAttempt,
    $$PendingAttemptsTableFilterComposer,
    $$PendingAttemptsTableOrderingComposer,
    $$PendingAttemptsTableAnnotationComposer,
    $$PendingAttemptsTableCreateCompanionBuilder,
    $$PendingAttemptsTableUpdateCompanionBuilder,
    (
      PendingAttempt,
      BaseReferences<_$AppDatabase, $PendingAttemptsTable, PendingAttempt>
    ),
    PendingAttempt,
    PrefetchHooks Function()> {
  $$PendingAttemptsTableTableManager(
      _$AppDatabase db, $PendingAttemptsTable table)
      : super(TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$PendingAttemptsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$PendingAttemptsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$PendingAttemptsTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback: ({
            Value<String> attemptId = const Value.absent(),
            Value<String> examId = const Value.absent(),
            Value<int> pausedAtMs = const Value.absent(),
            Value<int> rowid = const Value.absent(),
          }) =>
              PendingAttemptsCompanion(
            attemptId: attemptId,
            examId: examId,
            pausedAtMs: pausedAtMs,
            rowid: rowid,
          ),
          createCompanionCallback: ({
            required String attemptId,
            required String examId,
            required int pausedAtMs,
            Value<int> rowid = const Value.absent(),
          }) =>
              PendingAttemptsCompanion.insert(
            attemptId: attemptId,
            examId: examId,
            pausedAtMs: pausedAtMs,
            rowid: rowid,
          ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ));
}

typedef $$PendingAttemptsTableProcessedTableManager = ProcessedTableManager<
    _$AppDatabase,
    $PendingAttemptsTable,
    PendingAttempt,
    $$PendingAttemptsTableFilterComposer,
    $$PendingAttemptsTableOrderingComposer,
    $$PendingAttemptsTableAnnotationComposer,
    $$PendingAttemptsTableCreateCompanionBuilder,
    $$PendingAttemptsTableUpdateCompanionBuilder,
    (
      PendingAttempt,
      BaseReferences<_$AppDatabase, $PendingAttemptsTable, PendingAttempt>
    ),
    PendingAttempt,
    PrefetchHooks Function()>;

class $AppDatabaseManager {
  final _$AppDatabase _db;
  $AppDatabaseManager(this._db);
  $$CacheEntriesTableTableManager get cacheEntries =>
      $$CacheEntriesTableTableManager(_db, _db.cacheEntries);
  $$InboxNotificationsTableTableManager get inboxNotifications =>
      $$InboxNotificationsTableTableManager(_db, _db.inboxNotifications);
  $$PendingFcmTokensTableTableManager get pendingFcmTokens =>
      $$PendingFcmTokensTableTableManager(_db, _db.pendingFcmTokens);
  $$PendingAttemptsTableTableManager get pendingAttempts =>
      $$PendingAttemptsTableTableManager(_db, _db.pendingAttempts);
}
