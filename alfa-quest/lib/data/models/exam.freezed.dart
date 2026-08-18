// GENERATED CODE - DO NOT MODIFY BY HAND
// coverage:ignore-file
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'exam.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

// dart format off
T _$identity<T>(T value) => value;

/// @nodoc
mixin _$PreviousAttemptSummary {
  @JsonKey(name: 'attemptId')
  String get attemptId;
  AttemptStatus get status;
  @JsonKey(name: 'startedAt')
  DateTime get startedAt;
  @JsonKey(name: 'finishedAt')
  DateTime? get finishedAt;
  @JsonKey(name: 'accuracyPercentage')
  double? get accuracyPercentage;

  /// Create a copy of PreviousAttemptSummary
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $PreviousAttemptSummaryCopyWith<PreviousAttemptSummary> get copyWith =>
      _$PreviousAttemptSummaryCopyWithImpl<PreviousAttemptSummary>(
          this as PreviousAttemptSummary, _$identity);

  /// Serializes this PreviousAttemptSummary to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is PreviousAttemptSummary &&
            (identical(other.attemptId, attemptId) ||
                other.attemptId == attemptId) &&
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
  int get hashCode => Object.hash(runtimeType, attemptId, status, startedAt,
      finishedAt, accuracyPercentage);

  @override
  String toString() {
    return 'PreviousAttemptSummary(attemptId: $attemptId, status: $status, startedAt: $startedAt, finishedAt: $finishedAt, accuracyPercentage: $accuracyPercentage)';
  }
}

/// @nodoc
abstract mixin class $PreviousAttemptSummaryCopyWith<$Res> {
  factory $PreviousAttemptSummaryCopyWith(PreviousAttemptSummary value,
          $Res Function(PreviousAttemptSummary) _then) =
      _$PreviousAttemptSummaryCopyWithImpl;
  @useResult
  $Res call(
      {@JsonKey(name: 'attemptId') String attemptId,
      AttemptStatus status,
      @JsonKey(name: 'startedAt') DateTime startedAt,
      @JsonKey(name: 'finishedAt') DateTime? finishedAt,
      @JsonKey(name: 'accuracyPercentage') double? accuracyPercentage});
}

/// @nodoc
class _$PreviousAttemptSummaryCopyWithImpl<$Res>
    implements $PreviousAttemptSummaryCopyWith<$Res> {
  _$PreviousAttemptSummaryCopyWithImpl(this._self, this._then);

  final PreviousAttemptSummary _self;
  final $Res Function(PreviousAttemptSummary) _then;

  /// Create a copy of PreviousAttemptSummary
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? attemptId = null,
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

/// Adds pattern-matching-related methods to [PreviousAttemptSummary].
extension PreviousAttemptSummaryPatterns on PreviousAttemptSummary {
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
    TResult Function(_PreviousAttemptSummary value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _PreviousAttemptSummary() when $default != null:
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
    TResult Function(_PreviousAttemptSummary value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _PreviousAttemptSummary():
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
    TResult? Function(_PreviousAttemptSummary value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _PreviousAttemptSummary() when $default != null:
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
            AttemptStatus status,
            @JsonKey(name: 'startedAt') DateTime startedAt,
            @JsonKey(name: 'finishedAt') DateTime? finishedAt,
            @JsonKey(name: 'accuracyPercentage') double? accuracyPercentage)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _PreviousAttemptSummary() when $default != null:
        return $default(_that.attemptId, _that.status, _that.startedAt,
            _that.finishedAt, _that.accuracyPercentage);
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
            AttemptStatus status,
            @JsonKey(name: 'startedAt') DateTime startedAt,
            @JsonKey(name: 'finishedAt') DateTime? finishedAt,
            @JsonKey(name: 'accuracyPercentage') double? accuracyPercentage)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _PreviousAttemptSummary():
        return $default(_that.attemptId, _that.status, _that.startedAt,
            _that.finishedAt, _that.accuracyPercentage);
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
            AttemptStatus status,
            @JsonKey(name: 'startedAt') DateTime startedAt,
            @JsonKey(name: 'finishedAt') DateTime? finishedAt,
            @JsonKey(name: 'accuracyPercentage') double? accuracyPercentage)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _PreviousAttemptSummary() when $default != null:
        return $default(_that.attemptId, _that.status, _that.startedAt,
            _that.finishedAt, _that.accuracyPercentage);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _PreviousAttemptSummary implements PreviousAttemptSummary {
  const _PreviousAttemptSummary(
      {@JsonKey(name: 'attemptId') required this.attemptId,
      required this.status,
      @JsonKey(name: 'startedAt') required this.startedAt,
      @JsonKey(name: 'finishedAt') this.finishedAt,
      @JsonKey(name: 'accuracyPercentage') this.accuracyPercentage});
  factory _PreviousAttemptSummary.fromJson(Map<String, dynamic> json) =>
      _$PreviousAttemptSummaryFromJson(json);

  @override
  @JsonKey(name: 'attemptId')
  final String attemptId;
  @override
  final AttemptStatus status;
  @override
  @JsonKey(name: 'startedAt')
  final DateTime startedAt;
  @override
  @JsonKey(name: 'finishedAt')
  final DateTime? finishedAt;
  @override
  @JsonKey(name: 'accuracyPercentage')
  final double? accuracyPercentage;

  /// Create a copy of PreviousAttemptSummary
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$PreviousAttemptSummaryCopyWith<_PreviousAttemptSummary> get copyWith =>
      __$PreviousAttemptSummaryCopyWithImpl<_PreviousAttemptSummary>(
          this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$PreviousAttemptSummaryToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _PreviousAttemptSummary &&
            (identical(other.attemptId, attemptId) ||
                other.attemptId == attemptId) &&
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
  int get hashCode => Object.hash(runtimeType, attemptId, status, startedAt,
      finishedAt, accuracyPercentage);

  @override
  String toString() {
    return 'PreviousAttemptSummary(attemptId: $attemptId, status: $status, startedAt: $startedAt, finishedAt: $finishedAt, accuracyPercentage: $accuracyPercentage)';
  }
}

/// @nodoc
abstract mixin class _$PreviousAttemptSummaryCopyWith<$Res>
    implements $PreviousAttemptSummaryCopyWith<$Res> {
  factory _$PreviousAttemptSummaryCopyWith(_PreviousAttemptSummary value,
          $Res Function(_PreviousAttemptSummary) _then) =
      __$PreviousAttemptSummaryCopyWithImpl;
  @override
  @useResult
  $Res call(
      {@JsonKey(name: 'attemptId') String attemptId,
      AttemptStatus status,
      @JsonKey(name: 'startedAt') DateTime startedAt,
      @JsonKey(name: 'finishedAt') DateTime? finishedAt,
      @JsonKey(name: 'accuracyPercentage') double? accuracyPercentage});
}

/// @nodoc
class __$PreviousAttemptSummaryCopyWithImpl<$Res>
    implements _$PreviousAttemptSummaryCopyWith<$Res> {
  __$PreviousAttemptSummaryCopyWithImpl(this._self, this._then);

  final _PreviousAttemptSummary _self;
  final $Res Function(_PreviousAttemptSummary) _then;

  /// Create a copy of PreviousAttemptSummary
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? attemptId = null,
    Object? status = null,
    Object? startedAt = null,
    Object? finishedAt = freezed,
    Object? accuracyPercentage = freezed,
  }) {
    return _then(_PreviousAttemptSummary(
      attemptId: null == attemptId
          ? _self.attemptId
          : attemptId // ignore: cast_nullable_to_non_nullable
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

/// @nodoc
mixin _$Exam {
  String get id;
  String get title;
  String? get description;
  @JsonKey(name: 'numQuestions')
  int get numQuestions;
  @JsonKey(name: 'durationMin')
  int get durationMin;
  @JsonKey(name: 'isFree')
  bool get isFree;
  @JsonKey(name: 'careerId', fromJson: _careerIdFromJson)
  int? get careerId;
  @JsonKey(name: 'lastAttempt')
  PreviousAttemptSummary? get lastAttempt;
  bool? get active;
  String? get feedbackMode;

  /// Create a copy of Exam
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $ExamCopyWith<Exam> get copyWith =>
      _$ExamCopyWithImpl<Exam>(this as Exam, _$identity);

  /// Serializes this Exam to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is Exam &&
            (identical(other.id, id) || other.id == id) &&
            (identical(other.title, title) || other.title == title) &&
            (identical(other.description, description) ||
                other.description == description) &&
            (identical(other.numQuestions, numQuestions) ||
                other.numQuestions == numQuestions) &&
            (identical(other.durationMin, durationMin) ||
                other.durationMin == durationMin) &&
            (identical(other.isFree, isFree) || other.isFree == isFree) &&
            (identical(other.careerId, careerId) ||
                other.careerId == careerId) &&
            (identical(other.lastAttempt, lastAttempt) ||
                other.lastAttempt == lastAttempt) &&
            (identical(other.active, active) || other.active == active) &&
            (identical(other.feedbackMode, feedbackMode) ||
                other.feedbackMode == feedbackMode));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(
      runtimeType,
      id,
      title,
      description,
      numQuestions,
      durationMin,
      isFree,
      careerId,
      lastAttempt,
      active,
      feedbackMode);

  @override
  String toString() {
    return 'Exam(id: $id, title: $title, description: $description, numQuestions: $numQuestions, durationMin: $durationMin, isFree: $isFree, careerId: $careerId, lastAttempt: $lastAttempt, active: $active, feedbackMode: $feedbackMode)';
  }
}

/// @nodoc
abstract mixin class $ExamCopyWith<$Res> {
  factory $ExamCopyWith(Exam value, $Res Function(Exam) _then) =
      _$ExamCopyWithImpl;
  @useResult
  $Res call(
      {String id,
      String title,
      String? description,
      @JsonKey(name: 'numQuestions') int numQuestions,
      @JsonKey(name: 'durationMin') int durationMin,
      @JsonKey(name: 'isFree') bool isFree,
      @JsonKey(name: 'careerId', fromJson: _careerIdFromJson) int? careerId,
      @JsonKey(name: 'lastAttempt') PreviousAttemptSummary? lastAttempt,
      bool? active,
      String? feedbackMode});

  $PreviousAttemptSummaryCopyWith<$Res>? get lastAttempt;
}

/// @nodoc
class _$ExamCopyWithImpl<$Res> implements $ExamCopyWith<$Res> {
  _$ExamCopyWithImpl(this._self, this._then);

  final Exam _self;
  final $Res Function(Exam) _then;

  /// Create a copy of Exam
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? id = null,
    Object? title = null,
    Object? description = freezed,
    Object? numQuestions = null,
    Object? durationMin = null,
    Object? isFree = null,
    Object? careerId = freezed,
    Object? lastAttempt = freezed,
    Object? active = freezed,
    Object? feedbackMode = freezed,
  }) {
    return _then(_self.copyWith(
      id: null == id
          ? _self.id
          : id // ignore: cast_nullable_to_non_nullable
              as String,
      title: null == title
          ? _self.title
          : title // ignore: cast_nullable_to_non_nullable
              as String,
      description: freezed == description
          ? _self.description
          : description // ignore: cast_nullable_to_non_nullable
              as String?,
      numQuestions: null == numQuestions
          ? _self.numQuestions
          : numQuestions // ignore: cast_nullable_to_non_nullable
              as int,
      durationMin: null == durationMin
          ? _self.durationMin
          : durationMin // ignore: cast_nullable_to_non_nullable
              as int,
      isFree: null == isFree
          ? _self.isFree
          : isFree // ignore: cast_nullable_to_non_nullable
              as bool,
      careerId: freezed == careerId
          ? _self.careerId
          : careerId // ignore: cast_nullable_to_non_nullable
              as int?,
      lastAttempt: freezed == lastAttempt
          ? _self.lastAttempt
          : lastAttempt // ignore: cast_nullable_to_non_nullable
              as PreviousAttemptSummary?,
      active: freezed == active
          ? _self.active
          : active // ignore: cast_nullable_to_non_nullable
              as bool?,
      feedbackMode: freezed == feedbackMode
          ? _self.feedbackMode
          : feedbackMode // ignore: cast_nullable_to_non_nullable
              as String?,
    ));
  }

  /// Create a copy of Exam
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $PreviousAttemptSummaryCopyWith<$Res>? get lastAttempt {
    if (_self.lastAttempt == null) {
      return null;
    }

    return $PreviousAttemptSummaryCopyWith<$Res>(_self.lastAttempt!, (value) {
      return _then(_self.copyWith(lastAttempt: value));
    });
  }
}

/// Adds pattern-matching-related methods to [Exam].
extension ExamPatterns on Exam {
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
    TResult Function(_Exam value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _Exam() when $default != null:
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
    TResult Function(_Exam value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _Exam():
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
    TResult? Function(_Exam value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _Exam() when $default != null:
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
            String id,
            String title,
            String? description,
            @JsonKey(name: 'numQuestions') int numQuestions,
            @JsonKey(name: 'durationMin') int durationMin,
            @JsonKey(name: 'isFree') bool isFree,
            @JsonKey(name: 'careerId', fromJson: _careerIdFromJson)
            int? careerId,
            @JsonKey(name: 'lastAttempt') PreviousAttemptSummary? lastAttempt,
            bool? active,
            String? feedbackMode)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _Exam() when $default != null:
        return $default(
            _that.id,
            _that.title,
            _that.description,
            _that.numQuestions,
            _that.durationMin,
            _that.isFree,
            _that.careerId,
            _that.lastAttempt,
            _that.active,
            _that.feedbackMode);
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
            String id,
            String title,
            String? description,
            @JsonKey(name: 'numQuestions') int numQuestions,
            @JsonKey(name: 'durationMin') int durationMin,
            @JsonKey(name: 'isFree') bool isFree,
            @JsonKey(name: 'careerId', fromJson: _careerIdFromJson)
            int? careerId,
            @JsonKey(name: 'lastAttempt') PreviousAttemptSummary? lastAttempt,
            bool? active,
            String? feedbackMode)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _Exam():
        return $default(
            _that.id,
            _that.title,
            _that.description,
            _that.numQuestions,
            _that.durationMin,
            _that.isFree,
            _that.careerId,
            _that.lastAttempt,
            _that.active,
            _that.feedbackMode);
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
            String id,
            String title,
            String? description,
            @JsonKey(name: 'numQuestions') int numQuestions,
            @JsonKey(name: 'durationMin') int durationMin,
            @JsonKey(name: 'isFree') bool isFree,
            @JsonKey(name: 'careerId', fromJson: _careerIdFromJson)
            int? careerId,
            @JsonKey(name: 'lastAttempt') PreviousAttemptSummary? lastAttempt,
            bool? active,
            String? feedbackMode)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _Exam() when $default != null:
        return $default(
            _that.id,
            _that.title,
            _that.description,
            _that.numQuestions,
            _that.durationMin,
            _that.isFree,
            _that.careerId,
            _that.lastAttempt,
            _that.active,
            _that.feedbackMode);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _Exam implements Exam {
  const _Exam(
      {required this.id,
      required this.title,
      this.description,
      @JsonKey(name: 'numQuestions') required this.numQuestions,
      @JsonKey(name: 'durationMin') required this.durationMin,
      @JsonKey(name: 'isFree') required this.isFree,
      @JsonKey(name: 'careerId', fromJson: _careerIdFromJson) this.careerId,
      @JsonKey(name: 'lastAttempt') this.lastAttempt,
      this.active = true,
      this.feedbackMode});
  factory _Exam.fromJson(Map<String, dynamic> json) => _$ExamFromJson(json);

  @override
  final String id;
  @override
  final String title;
  @override
  final String? description;
  @override
  @JsonKey(name: 'numQuestions')
  final int numQuestions;
  @override
  @JsonKey(name: 'durationMin')
  final int durationMin;
  @override
  @JsonKey(name: 'isFree')
  final bool isFree;
  @override
  @JsonKey(name: 'careerId', fromJson: _careerIdFromJson)
  final int? careerId;
  @override
  @JsonKey(name: 'lastAttempt')
  final PreviousAttemptSummary? lastAttempt;
  @override
  @JsonKey()
  final bool? active;
  @override
  final String? feedbackMode;

  /// Create a copy of Exam
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$ExamCopyWith<_Exam> get copyWith =>
      __$ExamCopyWithImpl<_Exam>(this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$ExamToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _Exam &&
            (identical(other.id, id) || other.id == id) &&
            (identical(other.title, title) || other.title == title) &&
            (identical(other.description, description) ||
                other.description == description) &&
            (identical(other.numQuestions, numQuestions) ||
                other.numQuestions == numQuestions) &&
            (identical(other.durationMin, durationMin) ||
                other.durationMin == durationMin) &&
            (identical(other.isFree, isFree) || other.isFree == isFree) &&
            (identical(other.careerId, careerId) ||
                other.careerId == careerId) &&
            (identical(other.lastAttempt, lastAttempt) ||
                other.lastAttempt == lastAttempt) &&
            (identical(other.active, active) || other.active == active) &&
            (identical(other.feedbackMode, feedbackMode) ||
                other.feedbackMode == feedbackMode));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(
      runtimeType,
      id,
      title,
      description,
      numQuestions,
      durationMin,
      isFree,
      careerId,
      lastAttempt,
      active,
      feedbackMode);

  @override
  String toString() {
    return 'Exam(id: $id, title: $title, description: $description, numQuestions: $numQuestions, durationMin: $durationMin, isFree: $isFree, careerId: $careerId, lastAttempt: $lastAttempt, active: $active, feedbackMode: $feedbackMode)';
  }
}

/// @nodoc
abstract mixin class _$ExamCopyWith<$Res> implements $ExamCopyWith<$Res> {
  factory _$ExamCopyWith(_Exam value, $Res Function(_Exam) _then) =
      __$ExamCopyWithImpl;
  @override
  @useResult
  $Res call(
      {String id,
      String title,
      String? description,
      @JsonKey(name: 'numQuestions') int numQuestions,
      @JsonKey(name: 'durationMin') int durationMin,
      @JsonKey(name: 'isFree') bool isFree,
      @JsonKey(name: 'careerId', fromJson: _careerIdFromJson) int? careerId,
      @JsonKey(name: 'lastAttempt') PreviousAttemptSummary? lastAttempt,
      bool? active,
      String? feedbackMode});

  @override
  $PreviousAttemptSummaryCopyWith<$Res>? get lastAttempt;
}

/// @nodoc
class __$ExamCopyWithImpl<$Res> implements _$ExamCopyWith<$Res> {
  __$ExamCopyWithImpl(this._self, this._then);

  final _Exam _self;
  final $Res Function(_Exam) _then;

  /// Create a copy of Exam
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? id = null,
    Object? title = null,
    Object? description = freezed,
    Object? numQuestions = null,
    Object? durationMin = null,
    Object? isFree = null,
    Object? careerId = freezed,
    Object? lastAttempt = freezed,
    Object? active = freezed,
    Object? feedbackMode = freezed,
  }) {
    return _then(_Exam(
      id: null == id
          ? _self.id
          : id // ignore: cast_nullable_to_non_nullable
              as String,
      title: null == title
          ? _self.title
          : title // ignore: cast_nullable_to_non_nullable
              as String,
      description: freezed == description
          ? _self.description
          : description // ignore: cast_nullable_to_non_nullable
              as String?,
      numQuestions: null == numQuestions
          ? _self.numQuestions
          : numQuestions // ignore: cast_nullable_to_non_nullable
              as int,
      durationMin: null == durationMin
          ? _self.durationMin
          : durationMin // ignore: cast_nullable_to_non_nullable
              as int,
      isFree: null == isFree
          ? _self.isFree
          : isFree // ignore: cast_nullable_to_non_nullable
              as bool,
      careerId: freezed == careerId
          ? _self.careerId
          : careerId // ignore: cast_nullable_to_non_nullable
              as int?,
      lastAttempt: freezed == lastAttempt
          ? _self.lastAttempt
          : lastAttempt // ignore: cast_nullable_to_non_nullable
              as PreviousAttemptSummary?,
      active: freezed == active
          ? _self.active
          : active // ignore: cast_nullable_to_non_nullable
              as bool?,
      feedbackMode: freezed == feedbackMode
          ? _self.feedbackMode
          : feedbackMode // ignore: cast_nullable_to_non_nullable
              as String?,
    ));
  }

  /// Create a copy of Exam
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $PreviousAttemptSummaryCopyWith<$Res>? get lastAttempt {
    if (_self.lastAttempt == null) {
      return null;
    }

    return $PreviousAttemptSummaryCopyWith<$Res>(_self.lastAttempt!, (value) {
      return _then(_self.copyWith(lastAttempt: value));
    });
  }
}

/// @nodoc
mixin _$StartAttemptResponse {
  @JsonKey(name: 'attemptId')
  String get attemptId;
  @JsonKey(name: 'examId')
  String get examId;

  /// Create a copy of StartAttemptResponse
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $StartAttemptResponseCopyWith<StartAttemptResponse> get copyWith =>
      _$StartAttemptResponseCopyWithImpl<StartAttemptResponse>(
          this as StartAttemptResponse, _$identity);

  /// Serializes this StartAttemptResponse to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is StartAttemptResponse &&
            (identical(other.attemptId, attemptId) ||
                other.attemptId == attemptId) &&
            (identical(other.examId, examId) || other.examId == examId));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, attemptId, examId);

  @override
  String toString() {
    return 'StartAttemptResponse(attemptId: $attemptId, examId: $examId)';
  }
}

/// @nodoc
abstract mixin class $StartAttemptResponseCopyWith<$Res> {
  factory $StartAttemptResponseCopyWith(StartAttemptResponse value,
          $Res Function(StartAttemptResponse) _then) =
      _$StartAttemptResponseCopyWithImpl;
  @useResult
  $Res call(
      {@JsonKey(name: 'attemptId') String attemptId,
      @JsonKey(name: 'examId') String examId});
}

/// @nodoc
class _$StartAttemptResponseCopyWithImpl<$Res>
    implements $StartAttemptResponseCopyWith<$Res> {
  _$StartAttemptResponseCopyWithImpl(this._self, this._then);

  final StartAttemptResponse _self;
  final $Res Function(StartAttemptResponse) _then;

  /// Create a copy of StartAttemptResponse
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? attemptId = null,
    Object? examId = null,
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
    ));
  }
}

/// Adds pattern-matching-related methods to [StartAttemptResponse].
extension StartAttemptResponsePatterns on StartAttemptResponse {
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
    TResult Function(_StartAttemptResponse value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _StartAttemptResponse() when $default != null:
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
    TResult Function(_StartAttemptResponse value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _StartAttemptResponse():
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
    TResult? Function(_StartAttemptResponse value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _StartAttemptResponse() when $default != null:
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
    TResult Function(@JsonKey(name: 'attemptId') String attemptId,
            @JsonKey(name: 'examId') String examId)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _StartAttemptResponse() when $default != null:
        return $default(_that.attemptId, _that.examId);
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
    TResult Function(@JsonKey(name: 'attemptId') String attemptId,
            @JsonKey(name: 'examId') String examId)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _StartAttemptResponse():
        return $default(_that.attemptId, _that.examId);
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
    TResult? Function(@JsonKey(name: 'attemptId') String attemptId,
            @JsonKey(name: 'examId') String examId)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _StartAttemptResponse() when $default != null:
        return $default(_that.attemptId, _that.examId);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _StartAttemptResponse implements StartAttemptResponse {
  const _StartAttemptResponse(
      {@JsonKey(name: 'attemptId') required this.attemptId,
      @JsonKey(name: 'examId') required this.examId});
  factory _StartAttemptResponse.fromJson(Map<String, dynamic> json) =>
      _$StartAttemptResponseFromJson(json);

  @override
  @JsonKey(name: 'attemptId')
  final String attemptId;
  @override
  @JsonKey(name: 'examId')
  final String examId;

  /// Create a copy of StartAttemptResponse
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$StartAttemptResponseCopyWith<_StartAttemptResponse> get copyWith =>
      __$StartAttemptResponseCopyWithImpl<_StartAttemptResponse>(
          this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$StartAttemptResponseToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _StartAttemptResponse &&
            (identical(other.attemptId, attemptId) ||
                other.attemptId == attemptId) &&
            (identical(other.examId, examId) || other.examId == examId));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, attemptId, examId);

  @override
  String toString() {
    return 'StartAttemptResponse(attemptId: $attemptId, examId: $examId)';
  }
}

/// @nodoc
abstract mixin class _$StartAttemptResponseCopyWith<$Res>
    implements $StartAttemptResponseCopyWith<$Res> {
  factory _$StartAttemptResponseCopyWith(_StartAttemptResponse value,
          $Res Function(_StartAttemptResponse) _then) =
      __$StartAttemptResponseCopyWithImpl;
  @override
  @useResult
  $Res call(
      {@JsonKey(name: 'attemptId') String attemptId,
      @JsonKey(name: 'examId') String examId});
}

/// @nodoc
class __$StartAttemptResponseCopyWithImpl<$Res>
    implements _$StartAttemptResponseCopyWith<$Res> {
  __$StartAttemptResponseCopyWithImpl(this._self, this._then);

  final _StartAttemptResponse _self;
  final $Res Function(_StartAttemptResponse) _then;

  /// Create a copy of StartAttemptResponse
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? attemptId = null,
    Object? examId = null,
  }) {
    return _then(_StartAttemptResponse(
      attemptId: null == attemptId
          ? _self.attemptId
          : attemptId // ignore: cast_nullable_to_non_nullable
              as String,
      examId: null == examId
          ? _self.examId
          : examId // ignore: cast_nullable_to_non_nullable
              as String,
    ));
  }
}

// dart format on
