// GENERATED CODE - DO NOT MODIFY BY HAND
// coverage:ignore-file
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'history_item.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

// dart format off
T _$identity<T>(T value) => value;

/// @nodoc
mixin _$HistoryItem {
  @JsonKey(name: 'attemptId')
  String get attemptId;
  @JsonKey(name: 'examId')
  String get examId;
  @JsonKey(name: 'examTitle')
  String get examTitle;
  AttemptStatus get status;
  @JsonKey(name: 'startedAt')
  DateTime get startedAt;
  @JsonKey(name: 'finishedAt')
  DateTime? get finishedAt;
  @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
  double? get accuracyPercentage;

  /// Create a copy of HistoryItem
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $HistoryItemCopyWith<HistoryItem> get copyWith =>
      _$HistoryItemCopyWithImpl<HistoryItem>(this as HistoryItem, _$identity);

  /// Serializes this HistoryItem to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is HistoryItem &&
            (identical(other.attemptId, attemptId) ||
                other.attemptId == attemptId) &&
            (identical(other.examId, examId) || other.examId == examId) &&
            (identical(other.examTitle, examTitle) ||
                other.examTitle == examTitle) &&
            (identical(other.status, status) || other.status == status) &&
            (identical(other.startedAt, startedAt) ||
                other.startedAt == startedAt) &&
            (identical(other.finishedAt, finishedAt) ||
                other.finishedAt == finishedAt) &&
            (identical(other.accuracyPercentage, accuracyPercentage) ||
                other.accuracyPercentage == accuracyPercentage));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, attemptId, examId, examTitle,
      status, startedAt, finishedAt, accuracyPercentage);

  @override
  String toString() {
    return 'HistoryItem(attemptId: $attemptId, examId: $examId, examTitle: $examTitle, status: $status, startedAt: $startedAt, finishedAt: $finishedAt, accuracyPercentage: $accuracyPercentage)';
  }
}

/// @nodoc
abstract mixin class $HistoryItemCopyWith<$Res> {
  factory $HistoryItemCopyWith(
          HistoryItem value, $Res Function(HistoryItem) _then) =
      _$HistoryItemCopyWithImpl;
  @useResult
  $Res call(
      {@JsonKey(name: 'attemptId') String attemptId,
      @JsonKey(name: 'examId') String examId,
      @JsonKey(name: 'examTitle') String examTitle,
      AttemptStatus status,
      @JsonKey(name: 'startedAt') DateTime startedAt,
      @JsonKey(name: 'finishedAt') DateTime? finishedAt,
      @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
      double? accuracyPercentage});
}

/// @nodoc
class _$HistoryItemCopyWithImpl<$Res> implements $HistoryItemCopyWith<$Res> {
  _$HistoryItemCopyWithImpl(this._self, this._then);

  final HistoryItem _self;
  final $Res Function(HistoryItem) _then;

  /// Create a copy of HistoryItem
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? attemptId = null,
    Object? examId = null,
    Object? examTitle = null,
    Object? status = null,
    Object? startedAt = null,
    Object? finishedAt = freezed,
    Object? accuracyPercentage = freezed,
  }) {
    return _then(_self.copyWith(
      attemptId: null == attemptId
          ? _self.attemptId
          : attemptId // ignore: cast_nullable_to_non_nullable
              as String,
      examId: null == examId
          ? _self.examId
          : examId // ignore: cast_nullable_to_non_nullable
              as String,
      examTitle: null == examTitle
          ? _self.examTitle
          : examTitle // ignore: cast_nullable_to_non_nullable
              as String,
      status: null == status
          ? _self.status
          : status // ignore: cast_nullable_to_non_nullable
              as AttemptStatus,
      startedAt: null == startedAt
          ? _self.startedAt
          : startedAt // ignore: cast_nullable_to_non_nullable
              as DateTime,
      finishedAt: freezed == finishedAt
          ? _self.finishedAt
          : finishedAt // ignore: cast_nullable_to_non_nullable
              as DateTime?,
      accuracyPercentage: freezed == accuracyPercentage
          ? _self.accuracyPercentage
          : accuracyPercentage // ignore: cast_nullable_to_non_nullable
              as double?,
    ));
  }
}

/// Adds pattern-matching-related methods to [HistoryItem].
extension HistoryItemPatterns on HistoryItem {
  /// A variant of `map` that fallback to returning `orElse`.
  ///
  /// It is equivalent to doing:
  /// ```dart
  /// switch (sealedClass) {
  ///   case final Subclass value:
  ///     return ...;
  ///   case _:
  ///     return orElse();
  /// }
  /// ```

  @optionalTypeArgs
  TResult maybeMap<TResult extends Object?>(
    TResult Function(_HistoryItem value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _HistoryItem() when $default != null:
        return $default(_that);
      case _:
        return orElse();
    }
  }

  /// A `switch`-like method, using callbacks.
  ///
  /// Callbacks receives the raw object, upcasted.
  /// It is equivalent to doing:
  /// ```dart
  /// switch (sealedClass) {
  ///   case final Subclass value:
  ///     return ...;
  ///   case final Subclass2 value:
  ///     return ...;
  /// }
  /// ```

  @optionalTypeArgs
  TResult map<TResult extends Object?>(
    TResult Function(_HistoryItem value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _HistoryItem():
        return $default(_that);
      case _:
        throw StateError('Unexpected subclass');
    }
  }

  /// A variant of `map` that fallback to returning `null`.
  ///
  /// It is equivalent to doing:
  /// ```dart
  /// switch (sealedClass) {
  ///   case final Subclass value:
  ///     return ...;
  ///   case _:
  ///     return null;
  /// }
  /// ```

  @optionalTypeArgs
  TResult? mapOrNull<TResult extends Object?>(
    TResult? Function(_HistoryItem value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _HistoryItem() when $default != null:
        return $default(_that);
      case _:
        return null;
    }
  }

  /// A variant of `when` that fallback to an `orElse` callback.
  ///
  /// It is equivalent to doing:
  /// ```dart
  /// switch (sealedClass) {
  ///   case Subclass(:final field):
  ///     return ...;
  ///   case _:
  ///     return orElse();
  /// }
  /// ```

  @optionalTypeArgs
  TResult maybeWhen<TResult extends Object?>(
    TResult Function(
            @JsonKey(name: 'attemptId') String attemptId,
            @JsonKey(name: 'examId') String examId,
            @JsonKey(name: 'examTitle') String examTitle,
            AttemptStatus status,
            @JsonKey(name: 'startedAt') DateTime startedAt,
            @JsonKey(name: 'finishedAt') DateTime? finishedAt,
            @JsonKey(
                name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
            double? accuracyPercentage)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _HistoryItem() when $default != null:
        return $default(
            _that.attemptId,
            _that.examId,
            _that.examTitle,
            _that.status,
            _that.startedAt,
            _that.finishedAt,
            _that.accuracyPercentage);
      case _:
        return orElse();
    }
  }

  /// A `switch`-like method, using callbacks.
  ///
  /// As opposed to `map`, this offers destructuring.
  /// It is equivalent to doing:
  /// ```dart
  /// switch (sealedClass) {
  ///   case Subclass(:final field):
  ///     return ...;
  ///   case Subclass2(:final field2):
  ///     return ...;
  /// }
  /// ```

  @optionalTypeArgs
  TResult when<TResult extends Object?>(
    TResult Function(
            @JsonKey(name: 'attemptId') String attemptId,
            @JsonKey(name: 'examId') String examId,
            @JsonKey(name: 'examTitle') String examTitle,
            AttemptStatus status,
            @JsonKey(name: 'startedAt') DateTime startedAt,
            @JsonKey(name: 'finishedAt') DateTime? finishedAt,
            @JsonKey(
                name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
            double? accuracyPercentage)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _HistoryItem():
        return $default(
            _that.attemptId,
            _that.examId,
            _that.examTitle,
            _that.status,
            _that.startedAt,
            _that.finishedAt,
            _that.accuracyPercentage);
      case _:
        throw StateError('Unexpected subclass');
    }
  }

  /// A variant of `when` that fallback to returning `null`
  ///
  /// It is equivalent to doing:
  /// ```dart
  /// switch (sealedClass) {
  ///   case Subclass(:final field):
  ///     return ...;
  ///   case _:
  ///     return null;
  /// }
  /// ```

  @optionalTypeArgs
  TResult? whenOrNull<TResult extends Object?>(
    TResult? Function(
            @JsonKey(name: 'attemptId') String attemptId,
            @JsonKey(name: 'examId') String examId,
            @JsonKey(name: 'examTitle') String examTitle,
            AttemptStatus status,
            @JsonKey(name: 'startedAt') DateTime startedAt,
            @JsonKey(name: 'finishedAt') DateTime? finishedAt,
            @JsonKey(
                name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
            double? accuracyPercentage)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _HistoryItem() when $default != null:
        return $default(
            _that.attemptId,
            _that.examId,
            _that.examTitle,
            _that.status,
            _that.startedAt,
            _that.finishedAt,
            _that.accuracyPercentage);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _HistoryItem implements HistoryItem {
  const _HistoryItem(
      {@JsonKey(name: 'attemptId') required this.attemptId,
      @JsonKey(name: 'examId') required this.examId,
      @JsonKey(name: 'examTitle') required this.examTitle,
      this.status = AttemptStatus.finished,
      @JsonKey(name: 'startedAt') required this.startedAt,
      @JsonKey(name: 'finishedAt') this.finishedAt,
      @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
      this.accuracyPercentage});
  factory _HistoryItem.fromJson(Map<String, dynamic> json) =>
      _$HistoryItemFromJson(json);

  @override
  @JsonKey(name: 'attemptId')
  final String attemptId;
  @override
  @JsonKey(name: 'examId')
  final String examId;
  @override
  @JsonKey(name: 'examTitle')
  final String examTitle;
  @override
  @JsonKey()
  final AttemptStatus status;
  @override
  @JsonKey(name: 'startedAt')
  final DateTime startedAt;
  @override
  @JsonKey(name: 'finishedAt')
  final DateTime? finishedAt;
  @override
  @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
  final double? accuracyPercentage;

  /// Create a copy of HistoryItem
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$HistoryItemCopyWith<_HistoryItem> get copyWith =>
      __$HistoryItemCopyWithImpl<_HistoryItem>(this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$HistoryItemToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _HistoryItem &&
            (identical(other.attemptId, attemptId) ||
                other.attemptId == attemptId) &&
            (identical(other.examId, examId) || other.examId == examId) &&
            (identical(other.examTitle, examTitle) ||
                other.examTitle == examTitle) &&
            (identical(other.status, status) || other.status == status) &&
            (identical(other.startedAt, startedAt) ||
                other.startedAt == startedAt) &&
            (identical(other.finishedAt, finishedAt) ||
                other.finishedAt == finishedAt) &&
            (identical(other.accuracyPercentage, accuracyPercentage) ||
                other.accuracyPercentage == accuracyPercentage));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, attemptId, examId, examTitle,
      status, startedAt, finishedAt, accuracyPercentage);

  @override
  String toString() {
    return 'HistoryItem(attemptId: $attemptId, examId: $examId, examTitle: $examTitle, status: $status, startedAt: $startedAt, finishedAt: $finishedAt, accuracyPercentage: $accuracyPercentage)';
  }
}

/// @nodoc
abstract mixin class _$HistoryItemCopyWith<$Res>
    implements $HistoryItemCopyWith<$Res> {
  factory _$HistoryItemCopyWith(
          _HistoryItem value, $Res Function(_HistoryItem) _then) =
      __$HistoryItemCopyWithImpl;
  @override
  @useResult
  $Res call(
      {@JsonKey(name: 'attemptId') String attemptId,
      @JsonKey(name: 'examId') String examId,
      @JsonKey(name: 'examTitle') String examTitle,
      AttemptStatus status,
      @JsonKey(name: 'startedAt') DateTime startedAt,
      @JsonKey(name: 'finishedAt') DateTime? finishedAt,
      @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
      double? accuracyPercentage});
}

/// @nodoc
class __$HistoryItemCopyWithImpl<$Res> implements _$HistoryItemCopyWith<$Res> {
  __$HistoryItemCopyWithImpl(this._self, this._then);

  final _HistoryItem _self;
  final $Res Function(_HistoryItem) _then;

  /// Create a copy of HistoryItem
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? attemptId = null,
    Object? examId = null,
    Object? examTitle = null,
    Object? status = null,
    Object? startedAt = null,
    Object? finishedAt = freezed,
    Object? accuracyPercentage = freezed,
  }) {
    return _then(_HistoryItem(
      attemptId: null == attemptId
          ? _self.attemptId
          : attemptId // ignore: cast_nullable_to_non_nullable
              as String,
      examId: null == examId
          ? _self.examId
          : examId // ignore: cast_nullable_to_non_nullable
              as String,
      examTitle: null == examTitle
          ? _self.examTitle
          : examTitle // ignore: cast_nullable_to_non_nullable
              as String,
      status: null == status
          ? _self.status
          : status // ignore: cast_nullable_to_non_nullable
              as AttemptStatus,
      startedAt: null == startedAt
          ? _self.startedAt
          : startedAt // ignore: cast_nullable_to_non_nullable
              as DateTime,
      finishedAt: freezed == finishedAt
          ? _self.finishedAt
          : finishedAt // ignore: cast_nullable_to_non_nullable
              as DateTime?,
      accuracyPercentage: freezed == accuracyPercentage
          ? _self.accuracyPercentage
          : accuracyPercentage // ignore: cast_nullable_to_non_nullable
              as double?,
    ));
  }
}

// dart format on
