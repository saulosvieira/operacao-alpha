// GENERATED CODE - DO NOT MODIFY BY HAND
// coverage:ignore-file
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'performance.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

// dart format off
T _$identity<T>(T value) => value;

/// @nodoc
mixin _$PerformanceStatistics {
  @JsonKey(name: 'totalExamsCompleted', fromJson: _intFromJson)
  int get totalExamsCompleted;
  @JsonKey(name: 'accuracyPercentage', fromJson: _doubleFromJson)
  double get accuracyPercentage;
  @JsonKey(name: 'totalQuestionsAnswered', fromJson: _nullableIntFromJson)
  int? get totalQuestionsAnswered;
  @JsonKey(name: 'totalCorrectAnswers', fromJson: _nullableIntFromJson)
  int? get totalCorrectAnswers;

  /// Create a copy of PerformanceStatistics
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $PerformanceStatisticsCopyWith<PerformanceStatistics> get copyWith =>
      _$PerformanceStatisticsCopyWithImpl<PerformanceStatistics>(
          this as PerformanceStatistics, _$identity);

  /// Serializes this PerformanceStatistics to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is PerformanceStatistics &&
            (identical(other.totalExamsCompleted, totalExamsCompleted) ||
                other.totalExamsCompleted == totalExamsCompleted) &&
            (identical(other.accuracyPercentage, accuracyPercentage) ||
                other.accuracyPercentage == accuracyPercentage) &&
            (identical(other.totalQuestionsAnswered, totalQuestionsAnswered) ||
                other.totalQuestionsAnswered == totalQuestionsAnswered) &&
            (identical(other.totalCorrectAnswers, totalCorrectAnswers) ||
                other.totalCorrectAnswers == totalCorrectAnswers));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, totalExamsCompleted,
      accuracyPercentage, totalQuestionsAnswered, totalCorrectAnswers);

  @override
  String toString() {
    return 'PerformanceStatistics(totalExamsCompleted: $totalExamsCompleted, accuracyPercentage: $accuracyPercentage, totalQuestionsAnswered: $totalQuestionsAnswered, totalCorrectAnswers: $totalCorrectAnswers)';
  }
}

/// @nodoc
abstract mixin class $PerformanceStatisticsCopyWith<$Res> {
  factory $PerformanceStatisticsCopyWith(PerformanceStatistics value,
          $Res Function(PerformanceStatistics) _then) =
      _$PerformanceStatisticsCopyWithImpl;
  @useResult
  $Res call(
      {@JsonKey(name: 'totalExamsCompleted', fromJson: _intFromJson)
      int totalExamsCompleted,
      @JsonKey(name: 'accuracyPercentage', fromJson: _doubleFromJson)
      double accuracyPercentage,
      @JsonKey(name: 'totalQuestionsAnswered', fromJson: _nullableIntFromJson)
      int? totalQuestionsAnswered,
      @JsonKey(name: 'totalCorrectAnswers', fromJson: _nullableIntFromJson)
      int? totalCorrectAnswers});
}

/// @nodoc
class _$PerformanceStatisticsCopyWithImpl<$Res>
    implements $PerformanceStatisticsCopyWith<$Res> {
  _$PerformanceStatisticsCopyWithImpl(this._self, this._then);

  final PerformanceStatistics _self;
  final $Res Function(PerformanceStatistics) _then;

  /// Create a copy of PerformanceStatistics
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? totalExamsCompleted = null,
    Object? accuracyPercentage = null,
    Object? totalQuestionsAnswered = freezed,
    Object? totalCorrectAnswers = freezed,
  }) {
    return _then(_self.copyWith(
      totalExamsCompleted: null == totalExamsCompleted
          ? _self.totalExamsCompleted
          : totalExamsCompleted // ignore: cast_nullable_to_non_nullable
              as int,
      accuracyPercentage: null == accuracyPercentage
          ? _self.accuracyPercentage
          : accuracyPercentage // ignore: cast_nullable_to_non_nullable
              as double,
      totalQuestionsAnswered: freezed == totalQuestionsAnswered
          ? _self.totalQuestionsAnswered
          : totalQuestionsAnswered // ignore: cast_nullable_to_non_nullable
              as int?,
      totalCorrectAnswers: freezed == totalCorrectAnswers
          ? _self.totalCorrectAnswers
          : totalCorrectAnswers // ignore: cast_nullable_to_non_nullable
              as int?,
    ));
  }
}

/// Adds pattern-matching-related methods to [PerformanceStatistics].
extension PerformanceStatisticsPatterns on PerformanceStatistics {
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
    TResult Function(_PerformanceStatistics value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _PerformanceStatistics() when $default != null:
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
    TResult Function(_PerformanceStatistics value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _PerformanceStatistics():
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
    TResult? Function(_PerformanceStatistics value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _PerformanceStatistics() when $default != null:
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
            @JsonKey(name: 'totalExamsCompleted', fromJson: _intFromJson)
            int totalExamsCompleted,
            @JsonKey(name: 'accuracyPercentage', fromJson: _doubleFromJson)
            double accuracyPercentage,
            @JsonKey(
                name: 'totalQuestionsAnswered', fromJson: _nullableIntFromJson)
            int? totalQuestionsAnswered,
            @JsonKey(
                name: 'totalCorrectAnswers', fromJson: _nullableIntFromJson)
            int? totalCorrectAnswers)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _PerformanceStatistics() when $default != null:
        return $default(_that.totalExamsCompleted, _that.accuracyPercentage,
            _that.totalQuestionsAnswered, _that.totalCorrectAnswers);
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
            @JsonKey(name: 'totalExamsCompleted', fromJson: _intFromJson)
            int totalExamsCompleted,
            @JsonKey(name: 'accuracyPercentage', fromJson: _doubleFromJson)
            double accuracyPercentage,
            @JsonKey(
                name: 'totalQuestionsAnswered', fromJson: _nullableIntFromJson)
            int? totalQuestionsAnswered,
            @JsonKey(
                name: 'totalCorrectAnswers', fromJson: _nullableIntFromJson)
            int? totalCorrectAnswers)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _PerformanceStatistics():
        return $default(_that.totalExamsCompleted, _that.accuracyPercentage,
            _that.totalQuestionsAnswered, _that.totalCorrectAnswers);
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
            @JsonKey(name: 'totalExamsCompleted', fromJson: _intFromJson)
            int totalExamsCompleted,
            @JsonKey(name: 'accuracyPercentage', fromJson: _doubleFromJson)
            double accuracyPercentage,
            @JsonKey(
                name: 'totalQuestionsAnswered', fromJson: _nullableIntFromJson)
            int? totalQuestionsAnswered,
            @JsonKey(
                name: 'totalCorrectAnswers', fromJson: _nullableIntFromJson)
            int? totalCorrectAnswers)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _PerformanceStatistics() when $default != null:
        return $default(_that.totalExamsCompleted, _that.accuracyPercentage,
            _that.totalQuestionsAnswered, _that.totalCorrectAnswers);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _PerformanceStatistics implements PerformanceStatistics {
  const _PerformanceStatistics(
      {@JsonKey(name: 'totalExamsCompleted', fromJson: _intFromJson)
      required this.totalExamsCompleted,
      @JsonKey(name: 'accuracyPercentage', fromJson: _doubleFromJson)
      required this.accuracyPercentage,
      @JsonKey(name: 'totalQuestionsAnswered', fromJson: _nullableIntFromJson)
      this.totalQuestionsAnswered,
      @JsonKey(name: 'totalCorrectAnswers', fromJson: _nullableIntFromJson)
      this.totalCorrectAnswers});
  factory _PerformanceStatistics.fromJson(Map<String, dynamic> json) =>
      _$PerformanceStatisticsFromJson(json);

  @override
  @JsonKey(name: 'totalExamsCompleted', fromJson: _intFromJson)
  final int totalExamsCompleted;
  @override
  @JsonKey(name: 'accuracyPercentage', fromJson: _doubleFromJson)
  final double accuracyPercentage;
  @override
  @JsonKey(name: 'totalQuestionsAnswered', fromJson: _nullableIntFromJson)
  final int? totalQuestionsAnswered;
  @override
  @JsonKey(name: 'totalCorrectAnswers', fromJson: _nullableIntFromJson)
  final int? totalCorrectAnswers;

  /// Create a copy of PerformanceStatistics
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$PerformanceStatisticsCopyWith<_PerformanceStatistics> get copyWith =>
      __$PerformanceStatisticsCopyWithImpl<_PerformanceStatistics>(
          this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$PerformanceStatisticsToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _PerformanceStatistics &&
            (identical(other.totalExamsCompleted, totalExamsCompleted) ||
                other.totalExamsCompleted == totalExamsCompleted) &&
            (identical(other.accuracyPercentage, accuracyPercentage) ||
                other.accuracyPercentage == accuracyPercentage) &&
            (identical(other.totalQuestionsAnswered, totalQuestionsAnswered) ||
                other.totalQuestionsAnswered == totalQuestionsAnswered) &&
            (identical(other.totalCorrectAnswers, totalCorrectAnswers) ||
                other.totalCorrectAnswers == totalCorrectAnswers));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, totalExamsCompleted,
      accuracyPercentage, totalQuestionsAnswered, totalCorrectAnswers);

  @override
  String toString() {
    return 'PerformanceStatistics(totalExamsCompleted: $totalExamsCompleted, accuracyPercentage: $accuracyPercentage, totalQuestionsAnswered: $totalQuestionsAnswered, totalCorrectAnswers: $totalCorrectAnswers)';
  }
}

/// @nodoc
abstract mixin class _$PerformanceStatisticsCopyWith<$Res>
    implements $PerformanceStatisticsCopyWith<$Res> {
  factory _$PerformanceStatisticsCopyWith(_PerformanceStatistics value,
          $Res Function(_PerformanceStatistics) _then) =
      __$PerformanceStatisticsCopyWithImpl;
  @override
  @useResult
  $Res call(
      {@JsonKey(name: 'totalExamsCompleted', fromJson: _intFromJson)
      int totalExamsCompleted,
      @JsonKey(name: 'accuracyPercentage', fromJson: _doubleFromJson)
      double accuracyPercentage,
      @JsonKey(name: 'totalQuestionsAnswered', fromJson: _nullableIntFromJson)
      int? totalQuestionsAnswered,
      @JsonKey(name: 'totalCorrectAnswers', fromJson: _nullableIntFromJson)
      int? totalCorrectAnswers});
}

/// @nodoc
class __$PerformanceStatisticsCopyWithImpl<$Res>
    implements _$PerformanceStatisticsCopyWith<$Res> {
  __$PerformanceStatisticsCopyWithImpl(this._self, this._then);

  final _PerformanceStatistics _self;
  final $Res Function(_PerformanceStatistics) _then;

  /// Create a copy of PerformanceStatistics
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? totalExamsCompleted = null,
    Object? accuracyPercentage = null,
    Object? totalQuestionsAnswered = freezed,
    Object? totalCorrectAnswers = freezed,
  }) {
    return _then(_PerformanceStatistics(
      totalExamsCompleted: null == totalExamsCompleted
          ? _self.totalExamsCompleted
          : totalExamsCompleted // ignore: cast_nullable_to_non_nullable
              as int,
      accuracyPercentage: null == accuracyPercentage
          ? _self.accuracyPercentage
          : accuracyPercentage // ignore: cast_nullable_to_non_nullable
              as double,
      totalQuestionsAnswered: freezed == totalQuestionsAnswered
          ? _self.totalQuestionsAnswered
          : totalQuestionsAnswered // ignore: cast_nullable_to_non_nullable
              as int?,
      totalCorrectAnswers: freezed == totalCorrectAnswers
          ? _self.totalCorrectAnswers
          : totalCorrectAnswers // ignore: cast_nullable_to_non_nullable
              as int?,
    ));
  }
}

// dart format on
