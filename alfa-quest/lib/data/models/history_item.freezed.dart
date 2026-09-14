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
// O backend de histórico (HistoryResource) não fornece attemptId;
// fica opcional para permitir navegação apenas quando disponível.
  @JsonKey(name: 'attemptId')
  String? get attemptId;
  @JsonKey(name: 'examId')
  String get examId;
  @JsonKey(name: 'examTitle')
  String get examTitle;
  @JsonKey(name: 'careerName')
  String? get careerName;
  AttemptStatus get status;
  @JsonKey(name: 'completedAt')
  DateTime? get completedAt;
  @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
  double? get accuracyPercentage;
  @JsonKey(name: 'correctAnswers', fromJson: _nullableIntFromJson)
  int? get correctAnswers;
  @JsonKey(name: 'totalQuestions', fromJson: _nullableIntFromJson)
  int? get totalQuestions;
  @JsonKey(name: 'timeSpentMinutes', fromJson: _nullableIntFromJson)
  int? get timeSpentMinutes;

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
            (identical(other.careerName, careerName) ||
                other.careerName == careerName) &&
            (identical(other.status, status) || other.status == status) &&
            (identical(other.completedAt, completedAt) ||
                other.completedAt == completedAt) &&
            (identical(other.accuracyPercentage, accuracyPercentage) ||
                other.accuracyPercentage == accuracyPercentage) &&
            (identical(other.correctAnswers, correctAnswers) ||
                other.correctAnswers == correctAnswers) &&
            (identical(other.totalQuestions, totalQuestions) ||
                other.totalQuestions == totalQuestions) &&
            (identical(other.timeSpentMinutes, timeSpentMinutes) ||
                other.timeSpentMinutes == timeSpentMinutes));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(
      runtimeType,
      attemptId,
      examId,
      examTitle,
      careerName,
      status,
      completedAt,
      accuracyPercentage,
      correctAnswers,
      totalQuestions,
      timeSpentMinutes);

  @override
  String toString() {
    return 'HistoryItem(attemptId: $attemptId, examId: $examId, examTitle: $examTitle, careerName: $careerName, status: $status, completedAt: $completedAt, accuracyPercentage: $accuracyPercentage, correctAnswers: $correctAnswers, totalQuestions: $totalQuestions, timeSpentMinutes: $timeSpentMinutes)';
  }
}

/// @nodoc
abstract mixin class $HistoryItemCopyWith<$Res> {
  factory $HistoryItemCopyWith(
          HistoryItem value, $Res Function(HistoryItem) _then) =
      _$HistoryItemCopyWithImpl;
  @useResult
  $Res call(
      {@JsonKey(name: 'attemptId') String? attemptId,
      @JsonKey(name: 'examId') String examId,
      @JsonKey(name: 'examTitle') String examTitle,
      @JsonKey(name: 'careerName') String? careerName,
      AttemptStatus status,
      @JsonKey(name: 'completedAt') DateTime? completedAt,
      @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
      double? accuracyPercentage,
      @JsonKey(name: 'correctAnswers', fromJson: _nullableIntFromJson)
      int? correctAnswers,
      @JsonKey(name: 'totalQuestions', fromJson: _nullableIntFromJson)
      int? totalQuestions,
      @JsonKey(name: 'timeSpentMinutes', fromJson: _nullableIntFromJson)
      int? timeSpentMinutes});
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
    Object? attemptId = freezed,
    Object? examId = null,
    Object? examTitle = null,
    Object? careerName = freezed,
    Object? status = null,
    Object? completedAt = freezed,
    Object? accuracyPercentage = freezed,
    Object? correctAnswers = freezed,
    Object? totalQuestions = freezed,
    Object? timeSpentMinutes = freezed,
  }) {
    return _then(_self.copyWith(
      attemptId: freezed == attemptId
          ? _self.attemptId
          : attemptId // ignore: cast_nullable_to_non_nullable
              as String?,
      examId: null == examId
          ? _self.examId
          : examId // ignore: cast_nullable_to_non_nullable
              as String,
      examTitle: null == examTitle
          ? _self.examTitle
          : examTitle // ignore: cast_nullable_to_non_nullable
              as String,
      careerName: freezed == careerName
          ? _self.careerName
          : careerName // ignore: cast_nullable_to_non_nullable
              as String?,
      status: null == status
          ? _self.status
          : status // ignore: cast_nullable_to_non_nullable
              as AttemptStatus,
      completedAt: freezed == completedAt
          ? _self.completedAt
          : completedAt // ignore: cast_nullable_to_non_nullable
              as DateTime?,
      accuracyPercentage: freezed == accuracyPercentage
          ? _self.accuracyPercentage
          : accuracyPercentage // ignore: cast_nullable_to_non_nullable
              as double?,
      correctAnswers: freezed == correctAnswers
          ? _self.correctAnswers
          : correctAnswers // ignore: cast_nullable_to_non_nullable
              as int?,
      totalQuestions: freezed == totalQuestions
          ? _self.totalQuestions
          : totalQuestions // ignore: cast_nullable_to_non_nullable
              as int?,
      timeSpentMinutes: freezed == timeSpentMinutes
          ? _self.timeSpentMinutes
          : timeSpentMinutes // ignore: cast_nullable_to_non_nullable
              as int?,
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
            @JsonKey(name: 'attemptId') String? attemptId,
            @JsonKey(name: 'examId') String examId,
            @JsonKey(name: 'examTitle') String examTitle,
            @JsonKey(name: 'careerName') String? careerName,
            AttemptStatus status,
            @JsonKey(name: 'completedAt') DateTime? completedAt,
            @JsonKey(
                name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
            double? accuracyPercentage,
            @JsonKey(name: 'correctAnswers', fromJson: _nullableIntFromJson)
            int? correctAnswers,
            @JsonKey(name: 'totalQuestions', fromJson: _nullableIntFromJson)
            int? totalQuestions,
            @JsonKey(name: 'timeSpentMinutes', fromJson: _nullableIntFromJson)
            int? timeSpentMinutes)?
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
            _that.careerName,
            _that.status,
            _that.completedAt,
            _that.accuracyPercentage,
            _that.correctAnswers,
            _that.totalQuestions,
            _that.timeSpentMinutes);
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
            @JsonKey(name: 'attemptId') String? attemptId,
            @JsonKey(name: 'examId') String examId,
            @JsonKey(name: 'examTitle') String examTitle,
            @JsonKey(name: 'careerName') String? careerName,
            AttemptStatus status,
            @JsonKey(name: 'completedAt') DateTime? completedAt,
            @JsonKey(
                name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
            double? accuracyPercentage,
            @JsonKey(name: 'correctAnswers', fromJson: _nullableIntFromJson)
            int? correctAnswers,
            @JsonKey(name: 'totalQuestions', fromJson: _nullableIntFromJson)
            int? totalQuestions,
            @JsonKey(name: 'timeSpentMinutes', fromJson: _nullableIntFromJson)
            int? timeSpentMinutes)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _HistoryItem():
        return $default(
            _that.attemptId,
            _that.examId,
            _that.examTitle,
            _that.careerName,
            _that.status,
            _that.completedAt,
            _that.accuracyPercentage,
            _that.correctAnswers,
            _that.totalQuestions,
            _that.timeSpentMinutes);
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
            @JsonKey(name: 'attemptId') String? attemptId,
            @JsonKey(name: 'examId') String examId,
            @JsonKey(name: 'examTitle') String examTitle,
            @JsonKey(name: 'careerName') String? careerName,
            AttemptStatus status,
            @JsonKey(name: 'completedAt') DateTime? completedAt,
            @JsonKey(
                name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
            double? accuracyPercentage,
            @JsonKey(name: 'correctAnswers', fromJson: _nullableIntFromJson)
            int? correctAnswers,
            @JsonKey(name: 'totalQuestions', fromJson: _nullableIntFromJson)
            int? totalQuestions,
            @JsonKey(name: 'timeSpentMinutes', fromJson: _nullableIntFromJson)
            int? timeSpentMinutes)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _HistoryItem() when $default != null:
        return $default(
            _that.attemptId,
            _that.examId,
            _that.examTitle,
            _that.careerName,
            _that.status,
            _that.completedAt,
            _that.accuracyPercentage,
            _that.correctAnswers,
            _that.totalQuestions,
            _that.timeSpentMinutes);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _HistoryItem implements HistoryItem {
  const _HistoryItem(
      {@JsonKey(name: 'attemptId') this.attemptId,
      @JsonKey(name: 'examId') required this.examId,
      @JsonKey(name: 'examTitle') required this.examTitle,
      @JsonKey(name: 'careerName') this.careerName,
      this.status = AttemptStatus.finished,
      @JsonKey(name: 'completedAt') this.completedAt,
      @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
      this.accuracyPercentage,
      @JsonKey(name: 'correctAnswers', fromJson: _nullableIntFromJson)
      this.correctAnswers,
      @JsonKey(name: 'totalQuestions', fromJson: _nullableIntFromJson)
      this.totalQuestions,
      @JsonKey(name: 'timeSpentMinutes', fromJson: _nullableIntFromJson)
      this.timeSpentMinutes});
  factory _HistoryItem.fromJson(Map<String, dynamic> json) =>
      _$HistoryItemFromJson(json);

// O backend de histórico (HistoryResource) não fornece attemptId;
// fica opcional para permitir navegação apenas quando disponível.
  @override
  @JsonKey(name: 'attemptId')
  final String? attemptId;
  @override
  @JsonKey(name: 'examId')
  final String examId;
  @override
  @JsonKey(name: 'examTitle')
  final String examTitle;
  @override
  @JsonKey(name: 'careerName')
  final String? careerName;
  @override
  @JsonKey()
  final AttemptStatus status;
  @override
  @JsonKey(name: 'completedAt')
  final DateTime? completedAt;
  @override
  @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
  final double? accuracyPercentage;
  @override
  @JsonKey(name: 'correctAnswers', fromJson: _nullableIntFromJson)
  final int? correctAnswers;
  @override
  @JsonKey(name: 'totalQuestions', fromJson: _nullableIntFromJson)
  final int? totalQuestions;
  @override
  @JsonKey(name: 'timeSpentMinutes', fromJson: _nullableIntFromJson)
  final int? timeSpentMinutes;

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
            (identical(other.careerName, careerName) ||
                other.careerName == careerName) &&
            (identical(other.status, status) || other.status == status) &&
            (identical(other.completedAt, completedAt) ||
                other.completedAt == completedAt) &&
            (identical(other.accuracyPercentage, accuracyPercentage) ||
                other.accuracyPercentage == accuracyPercentage) &&
            (identical(other.correctAnswers, correctAnswers) ||
                other.correctAnswers == correctAnswers) &&
            (identical(other.totalQuestions, totalQuestions) ||
                other.totalQuestions == totalQuestions) &&
            (identical(other.timeSpentMinutes, timeSpentMinutes) ||
                other.timeSpentMinutes == timeSpentMinutes));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(
      runtimeType,
      attemptId,
      examId,
      examTitle,
      careerName,
      status,
      completedAt,
      accuracyPercentage,
      correctAnswers,
      totalQuestions,
      timeSpentMinutes);

  @override
  String toString() {
    return 'HistoryItem(attemptId: $attemptId, examId: $examId, examTitle: $examTitle, careerName: $careerName, status: $status, completedAt: $completedAt, accuracyPercentage: $accuracyPercentage, correctAnswers: $correctAnswers, totalQuestions: $totalQuestions, timeSpentMinutes: $timeSpentMinutes)';
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
      {@JsonKey(name: 'attemptId') String? attemptId,
      @JsonKey(name: 'examId') String examId,
      @JsonKey(name: 'examTitle') String examTitle,
      @JsonKey(name: 'careerName') String? careerName,
      AttemptStatus status,
      @JsonKey(name: 'completedAt') DateTime? completedAt,
      @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson)
      double? accuracyPercentage,
      @JsonKey(name: 'correctAnswers', fromJson: _nullableIntFromJson)
      int? correctAnswers,
      @JsonKey(name: 'totalQuestions', fromJson: _nullableIntFromJson)
      int? totalQuestions,
      @JsonKey(name: 'timeSpentMinutes', fromJson: _nullableIntFromJson)
      int? timeSpentMinutes});
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
    Object? attemptId = freezed,
    Object? examId = null,
    Object? examTitle = null,
    Object? careerName = freezed,
    Object? status = null,
    Object? completedAt = freezed,
    Object? accuracyPercentage = freezed,
    Object? correctAnswers = freezed,
    Object? totalQuestions = freezed,
    Object? timeSpentMinutes = freezed,
  }) {
    return _then(_HistoryItem(
      attemptId: freezed == attemptId
          ? _self.attemptId
          : attemptId // ignore: cast_nullable_to_non_nullable
              as String?,
      examId: null == examId
          ? _self.examId
          : examId // ignore: cast_nullable_to_non_nullable
              as String,
      examTitle: null == examTitle
          ? _self.examTitle
          : examTitle // ignore: cast_nullable_to_non_nullable
              as String,
      careerName: freezed == careerName
          ? _self.careerName
          : careerName // ignore: cast_nullable_to_non_nullable
              as String?,
      status: null == status
          ? _self.status
          : status // ignore: cast_nullable_to_non_nullable
              as AttemptStatus,
      completedAt: freezed == completedAt
          ? _self.completedAt
          : completedAt // ignore: cast_nullable_to_non_nullable
              as DateTime?,
      accuracyPercentage: freezed == accuracyPercentage
          ? _self.accuracyPercentage
          : accuracyPercentage // ignore: cast_nullable_to_non_nullable
              as double?,
      correctAnswers: freezed == correctAnswers
          ? _self.correctAnswers
          : correctAnswers // ignore: cast_nullable_to_non_nullable
              as int?,
      totalQuestions: freezed == totalQuestions
          ? _self.totalQuestions
          : totalQuestions // ignore: cast_nullable_to_non_nullable
              as int?,
      timeSpentMinutes: freezed == timeSpentMinutes
          ? _self.timeSpentMinutes
          : timeSpentMinutes // ignore: cast_nullable_to_non_nullable
              as int?,
    ));
  }
}

// dart format on
