// GENERATED CODE - DO NOT MODIFY BY HAND
// coverage:ignore-file
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'sanctum_token.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

// dart format off
T _$identity<T>(T value) => value;

/// @nodoc
mixin _$SanctumToken {
  String get value;
  @JsonKey(name: 'obtained_at')
  DateTime get obtainedAt;
  @JsonKey(name: 'persist_across_launches')
  bool get persistAcrossLaunches;

  /// Create a copy of SanctumToken
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $SanctumTokenCopyWith<SanctumToken> get copyWith =>
      _$SanctumTokenCopyWithImpl<SanctumToken>(
          this as SanctumToken, _$identity);

  /// Serializes this SanctumToken to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is SanctumToken &&
            (identical(other.value, value) || other.value == value) &&
            (identical(other.obtainedAt, obtainedAt) ||
                other.obtainedAt == obtainedAt) &&
            (identical(other.persistAcrossLaunches, persistAcrossLaunches) ||
                other.persistAcrossLaunches == persistAcrossLaunches));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode =>
      Object.hash(runtimeType, value, obtainedAt, persistAcrossLaunches);

  @override
  String toString() {
    return 'SanctumToken(value: $value, obtainedAt: $obtainedAt, persistAcrossLaunches: $persistAcrossLaunches)';
  }
}

/// @nodoc
abstract mixin class $SanctumTokenCopyWith<$Res> {
  factory $SanctumTokenCopyWith(
          SanctumToken value, $Res Function(SanctumToken) _then) =
      _$SanctumTokenCopyWithImpl;
  @useResult
  $Res call(
      {String value,
      @JsonKey(name: 'obtained_at') DateTime obtainedAt,
      @JsonKey(name: 'persist_across_launches') bool persistAcrossLaunches});
}

/// @nodoc
class _$SanctumTokenCopyWithImpl<$Res> implements $SanctumTokenCopyWith<$Res> {
  _$SanctumTokenCopyWithImpl(this._self, this._then);

  final SanctumToken _self;
  final $Res Function(SanctumToken) _then;

  /// Create a copy of SanctumToken
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? value = null,
    Object? obtainedAt = null,
    Object? persistAcrossLaunches = null,
  }) {
    return _then(_self.copyWith(
      value: null == value
          ? _self.value
          : value // ignore: cast_nullable_to_non_nullable
              as String,
      obtainedAt: null == obtainedAt
          ? _self.obtainedAt
          : obtainedAt // ignore: cast_nullable_to_non_nullable
              as DateTime,
      persistAcrossLaunches: null == persistAcrossLaunches
          ? _self.persistAcrossLaunches
          : persistAcrossLaunches // ignore: cast_nullable_to_non_nullable
              as bool,
    ));
  }
}

/// Adds pattern-matching-related methods to [SanctumToken].
extension SanctumTokenPatterns on SanctumToken {
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
    TResult Function(_SanctumToken value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _SanctumToken() when $default != null:
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
    TResult Function(_SanctumToken value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _SanctumToken():
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
    TResult? Function(_SanctumToken value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _SanctumToken() when $default != null:
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
            String value,
            @JsonKey(name: 'obtained_at') DateTime obtainedAt,
            @JsonKey(name: 'persist_across_launches')
            bool persistAcrossLaunches)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _SanctumToken() when $default != null:
        return $default(
            _that.value, _that.obtainedAt, _that.persistAcrossLaunches);
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
            String value,
            @JsonKey(name: 'obtained_at') DateTime obtainedAt,
            @JsonKey(name: 'persist_across_launches')
            bool persistAcrossLaunches)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _SanctumToken():
        return $default(
            _that.value, _that.obtainedAt, _that.persistAcrossLaunches);
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
            String value,
            @JsonKey(name: 'obtained_at') DateTime obtainedAt,
            @JsonKey(name: 'persist_across_launches')
            bool persistAcrossLaunches)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _SanctumToken() when $default != null:
        return $default(
            _that.value, _that.obtainedAt, _that.persistAcrossLaunches);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _SanctumToken implements SanctumToken {
  const _SanctumToken(
      {required this.value,
      @JsonKey(name: 'obtained_at') required this.obtainedAt,
      @JsonKey(name: 'persist_across_launches')
      this.persistAcrossLaunches = false});
  factory _SanctumToken.fromJson(Map<String, dynamic> json) =>
      _$SanctumTokenFromJson(json);

  @override
  final String value;
  @override
  @JsonKey(name: 'obtained_at')
  final DateTime obtainedAt;
  @override
  @JsonKey(name: 'persist_across_launches')
  final bool persistAcrossLaunches;

  /// Create a copy of SanctumToken
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$SanctumTokenCopyWith<_SanctumToken> get copyWith =>
      __$SanctumTokenCopyWithImpl<_SanctumToken>(this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$SanctumTokenToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _SanctumToken &&
            (identical(other.value, value) || other.value == value) &&
            (identical(other.obtainedAt, obtainedAt) ||
                other.obtainedAt == obtainedAt) &&
            (identical(other.persistAcrossLaunches, persistAcrossLaunches) ||
                other.persistAcrossLaunches == persistAcrossLaunches));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode =>
      Object.hash(runtimeType, value, obtainedAt, persistAcrossLaunches);

  @override
  String toString() {
    return 'SanctumToken(value: $value, obtainedAt: $obtainedAt, persistAcrossLaunches: $persistAcrossLaunches)';
  }
}

/// @nodoc
abstract mixin class _$SanctumTokenCopyWith<$Res>
    implements $SanctumTokenCopyWith<$Res> {
  factory _$SanctumTokenCopyWith(
          _SanctumToken value, $Res Function(_SanctumToken) _then) =
      __$SanctumTokenCopyWithImpl;
  @override
  @useResult
  $Res call(
      {String value,
      @JsonKey(name: 'obtained_at') DateTime obtainedAt,
      @JsonKey(name: 'persist_across_launches') bool persistAcrossLaunches});
}

/// @nodoc
class __$SanctumTokenCopyWithImpl<$Res>
    implements _$SanctumTokenCopyWith<$Res> {
  __$SanctumTokenCopyWithImpl(this._self, this._then);

  final _SanctumToken _self;
  final $Res Function(_SanctumToken) _then;

  /// Create a copy of SanctumToken
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? value = null,
    Object? obtainedAt = null,
    Object? persistAcrossLaunches = null,
  }) {
    return _then(_SanctumToken(
      value: null == value
          ? _self.value
          : value // ignore: cast_nullable_to_non_nullable
              as String,
      obtainedAt: null == obtainedAt
          ? _self.obtainedAt
          : obtainedAt // ignore: cast_nullable_to_non_nullable
              as DateTime,
      persistAcrossLaunches: null == persistAcrossLaunches
          ? _self.persistAcrossLaunches
          : persistAcrossLaunches // ignore: cast_nullable_to_non_nullable
              as bool,
    ));
  }
}

// dart format on
