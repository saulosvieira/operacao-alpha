// GENERATED CODE - DO NOT MODIFY BY HAND
// coverage:ignore-file
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'ranking.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

// dart format off
T _$identity<T>(T value) => value;

/// @nodoc
mixin _$RankingEntry {
  @JsonKey(fromJson: _intFromJson)
  int get position;
  @JsonKey(name: 'userId', fromJson: _intFromJson)
  int get userId;
  @JsonKey(name: 'userName')
  String get userName;
  @JsonKey(fromJson: _intFromJson)
  int get score;
  @JsonKey(name: 'isCurrentUser')
  bool get isCurrentUser;

  /// Create a copy of RankingEntry
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $RankingEntryCopyWith<RankingEntry> get copyWith =>
      _$RankingEntryCopyWithImpl<RankingEntry>(
          this as RankingEntry, _$identity);

  /// Serializes this RankingEntry to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is RankingEntry &&
            (identical(other.position, position) ||
                other.position == position) &&
            (identical(other.userId, userId) || other.userId == userId) &&
            (identical(other.userName, userName) ||
                other.userName == userName) &&
            (identical(other.score, score) || other.score == score) &&
            (identical(other.isCurrentUser, isCurrentUser) ||
                other.isCurrentUser == isCurrentUser));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(
      runtimeType, position, userId, userName, score, isCurrentUser);

  @override
  String toString() {
    return 'RankingEntry(position: $position, userId: $userId, userName: $userName, score: $score, isCurrentUser: $isCurrentUser)';
  }
}

/// @nodoc
abstract mixin class $RankingEntryCopyWith<$Res> {
  factory $RankingEntryCopyWith(
          RankingEntry value, $Res Function(RankingEntry) _then) =
      _$RankingEntryCopyWithImpl;
  @useResult
  $Res call(
      {@JsonKey(fromJson: _intFromJson) int position,
      @JsonKey(name: 'userId', fromJson: _intFromJson) int userId,
      @JsonKey(name: 'userName') String userName,
      @JsonKey(fromJson: _intFromJson) int score,
      @JsonKey(name: 'isCurrentUser') bool isCurrentUser});
}

/// @nodoc
class _$RankingEntryCopyWithImpl<$Res> implements $RankingEntryCopyWith<$Res> {
  _$RankingEntryCopyWithImpl(this._self, this._then);

  final RankingEntry _self;
  final $Res Function(RankingEntry) _then;

  /// Create a copy of RankingEntry
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? position = null,
    Object? userId = null,
    Object? userName = null,
    Object? score = null,
    Object? isCurrentUser = null,
  }) {
    return _then(_self.copyWith(
      position: null == position
          ? _self.position
          : position // ignore: cast_nullable_to_non_nullable
              as int,
      userId: null == userId
          ? _self.userId
          : userId // ignore: cast_nullable_to_non_nullable
              as int,
      userName: null == userName
          ? _self.userName
          : userName // ignore: cast_nullable_to_non_nullable
              as String,
      score: null == score
          ? _self.score
          : score // ignore: cast_nullable_to_non_nullable
              as int,
      isCurrentUser: null == isCurrentUser
          ? _self.isCurrentUser
          : isCurrentUser // ignore: cast_nullable_to_non_nullable
              as bool,
    ));
  }
}

/// Adds pattern-matching-related methods to [RankingEntry].
extension RankingEntryPatterns on RankingEntry {
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
    TResult Function(_RankingEntry value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _RankingEntry() when $default != null:
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
    TResult Function(_RankingEntry value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _RankingEntry():
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
    TResult? Function(_RankingEntry value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _RankingEntry() when $default != null:
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
            @JsonKey(fromJson: _intFromJson) int position,
            @JsonKey(name: 'userId', fromJson: _intFromJson) int userId,
            @JsonKey(name: 'userName') String userName,
            @JsonKey(fromJson: _intFromJson) int score,
            @JsonKey(name: 'isCurrentUser') bool isCurrentUser)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _RankingEntry() when $default != null:
        return $default(_that.position, _that.userId, _that.userName,
            _that.score, _that.isCurrentUser);
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
            @JsonKey(fromJson: _intFromJson) int position,
            @JsonKey(name: 'userId', fromJson: _intFromJson) int userId,
            @JsonKey(name: 'userName') String userName,
            @JsonKey(fromJson: _intFromJson) int score,
            @JsonKey(name: 'isCurrentUser') bool isCurrentUser)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _RankingEntry():
        return $default(_that.position, _that.userId, _that.userName,
            _that.score, _that.isCurrentUser);
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
            @JsonKey(fromJson: _intFromJson) int position,
            @JsonKey(name: 'userId', fromJson: _intFromJson) int userId,
            @JsonKey(name: 'userName') String userName,
            @JsonKey(fromJson: _intFromJson) int score,
            @JsonKey(name: 'isCurrentUser') bool isCurrentUser)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _RankingEntry() when $default != null:
        return $default(_that.position, _that.userId, _that.userName,
            _that.score, _that.isCurrentUser);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _RankingEntry implements RankingEntry {
  const _RankingEntry(
      {@JsonKey(fromJson: _intFromJson) required this.position,
      @JsonKey(name: 'userId', fromJson: _intFromJson) required this.userId,
      @JsonKey(name: 'userName') required this.userName,
      @JsonKey(fromJson: _intFromJson) required this.score,
      @JsonKey(name: 'isCurrentUser') this.isCurrentUser = false});
  factory _RankingEntry.fromJson(Map<String, dynamic> json) =>
      _$RankingEntryFromJson(json);

  @override
  @JsonKey(fromJson: _intFromJson)
  final int position;
  @override
  @JsonKey(name: 'userId', fromJson: _intFromJson)
  final int userId;
  @override
  @JsonKey(name: 'userName')
  final String userName;
  @override
  @JsonKey(fromJson: _intFromJson)
  final int score;
  @override
  @JsonKey(name: 'isCurrentUser')
  final bool isCurrentUser;

  /// Create a copy of RankingEntry
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$RankingEntryCopyWith<_RankingEntry> get copyWith =>
      __$RankingEntryCopyWithImpl<_RankingEntry>(this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$RankingEntryToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _RankingEntry &&
            (identical(other.position, position) ||
                other.position == position) &&
            (identical(other.userId, userId) || other.userId == userId) &&
            (identical(other.userName, userName) ||
                other.userName == userName) &&
            (identical(other.score, score) || other.score == score) &&
            (identical(other.isCurrentUser, isCurrentUser) ||
                other.isCurrentUser == isCurrentUser));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(
      runtimeType, position, userId, userName, score, isCurrentUser);

  @override
  String toString() {
    return 'RankingEntry(position: $position, userId: $userId, userName: $userName, score: $score, isCurrentUser: $isCurrentUser)';
  }
}

/// @nodoc
abstract mixin class _$RankingEntryCopyWith<$Res>
    implements $RankingEntryCopyWith<$Res> {
  factory _$RankingEntryCopyWith(
          _RankingEntry value, $Res Function(_RankingEntry) _then) =
      __$RankingEntryCopyWithImpl;
  @override
  @useResult
  $Res call(
      {@JsonKey(fromJson: _intFromJson) int position,
      @JsonKey(name: 'userId', fromJson: _intFromJson) int userId,
      @JsonKey(name: 'userName') String userName,
      @JsonKey(fromJson: _intFromJson) int score,
      @JsonKey(name: 'isCurrentUser') bool isCurrentUser});
}

/// @nodoc
class __$RankingEntryCopyWithImpl<$Res>
    implements _$RankingEntryCopyWith<$Res> {
  __$RankingEntryCopyWithImpl(this._self, this._then);

  final _RankingEntry _self;
  final $Res Function(_RankingEntry) _then;

  /// Create a copy of RankingEntry
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? position = null,
    Object? userId = null,
    Object? userName = null,
    Object? score = null,
    Object? isCurrentUser = null,
  }) {
    return _then(_RankingEntry(
      position: null == position
          ? _self.position
          : position // ignore: cast_nullable_to_non_nullable
              as int,
      userId: null == userId
          ? _self.userId
          : userId // ignore: cast_nullable_to_non_nullable
              as int,
      userName: null == userName
          ? _self.userName
          : userName // ignore: cast_nullable_to_non_nullable
              as String,
      score: null == score
          ? _self.score
          : score // ignore: cast_nullable_to_non_nullable
              as int,
      isCurrentUser: null == isCurrentUser
          ? _self.isCurrentUser
          : isCurrentUser // ignore: cast_nullable_to_non_nullable
              as bool,
    ));
  }
}

/// @nodoc
mixin _$MyRankingPosition {
  @JsonKey(fromJson: _nullableIntFromJson)
  int? get position;
  @JsonKey(fromJson: _intFromJson)
  int get score;

  /// Create a copy of MyRankingPosition
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $MyRankingPositionCopyWith<MyRankingPosition> get copyWith =>
      _$MyRankingPositionCopyWithImpl<MyRankingPosition>(
          this as MyRankingPosition, _$identity);

  /// Serializes this MyRankingPosition to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is MyRankingPosition &&
            (identical(other.position, position) ||
                other.position == position) &&
            (identical(other.score, score) || other.score == score));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, position, score);

  @override
  String toString() {
    return 'MyRankingPosition(position: $position, score: $score)';
  }
}

/// @nodoc
abstract mixin class $MyRankingPositionCopyWith<$Res> {
  factory $MyRankingPositionCopyWith(
          MyRankingPosition value, $Res Function(MyRankingPosition) _then) =
      _$MyRankingPositionCopyWithImpl;
  @useResult
  $Res call(
      {@JsonKey(fromJson: _nullableIntFromJson) int? position,
      @JsonKey(fromJson: _intFromJson) int score});
}

/// @nodoc
class _$MyRankingPositionCopyWithImpl<$Res>
    implements $MyRankingPositionCopyWith<$Res> {
  _$MyRankingPositionCopyWithImpl(this._self, this._then);

  final MyRankingPosition _self;
  final $Res Function(MyRankingPosition) _then;

  /// Create a copy of MyRankingPosition
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? position = freezed,
    Object? score = null,
  }) {
    return _then(_self.copyWith(
      position: freezed == position
          ? _self.position
          : position // ignore: cast_nullable_to_non_nullable
              as int?,
      score: null == score
          ? _self.score
          : score // ignore: cast_nullable_to_non_nullable
              as int,
    ));
  }
}

/// Adds pattern-matching-related methods to [MyRankingPosition].
extension MyRankingPositionPatterns on MyRankingPosition {
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
    TResult Function(_MyRankingPosition value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _MyRankingPosition() when $default != null:
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
    TResult Function(_MyRankingPosition value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _MyRankingPosition():
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
    TResult? Function(_MyRankingPosition value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _MyRankingPosition() when $default != null:
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
    TResult Function(@JsonKey(fromJson: _nullableIntFromJson) int? position,
            @JsonKey(fromJson: _intFromJson) int score)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _MyRankingPosition() when $default != null:
        return $default(_that.position, _that.score);
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
    TResult Function(@JsonKey(fromJson: _nullableIntFromJson) int? position,
            @JsonKey(fromJson: _intFromJson) int score)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _MyRankingPosition():
        return $default(_that.position, _that.score);
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
    TResult? Function(@JsonKey(fromJson: _nullableIntFromJson) int? position,
            @JsonKey(fromJson: _intFromJson) int score)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _MyRankingPosition() when $default != null:
        return $default(_that.position, _that.score);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _MyRankingPosition implements MyRankingPosition {
  const _MyRankingPosition(
      {@JsonKey(fromJson: _nullableIntFromJson) this.position,
      @JsonKey(fromJson: _intFromJson) required this.score});
  factory _MyRankingPosition.fromJson(Map<String, dynamic> json) =>
      _$MyRankingPositionFromJson(json);

  @override
  @JsonKey(fromJson: _nullableIntFromJson)
  final int? position;
  @override
  @JsonKey(fromJson: _intFromJson)
  final int score;

  /// Create a copy of MyRankingPosition
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$MyRankingPositionCopyWith<_MyRankingPosition> get copyWith =>
      __$MyRankingPositionCopyWithImpl<_MyRankingPosition>(this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$MyRankingPositionToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _MyRankingPosition &&
            (identical(other.position, position) ||
                other.position == position) &&
            (identical(other.score, score) || other.score == score));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, position, score);

  @override
  String toString() {
    return 'MyRankingPosition(position: $position, score: $score)';
  }
}

/// @nodoc
abstract mixin class _$MyRankingPositionCopyWith<$Res>
    implements $MyRankingPositionCopyWith<$Res> {
  factory _$MyRankingPositionCopyWith(
          _MyRankingPosition value, $Res Function(_MyRankingPosition) _then) =
      __$MyRankingPositionCopyWithImpl;
  @override
  @useResult
  $Res call(
      {@JsonKey(fromJson: _nullableIntFromJson) int? position,
      @JsonKey(fromJson: _intFromJson) int score});
}

/// @nodoc
class __$MyRankingPositionCopyWithImpl<$Res>
    implements _$MyRankingPositionCopyWith<$Res> {
  __$MyRankingPositionCopyWithImpl(this._self, this._then);

  final _MyRankingPosition _self;
  final $Res Function(_MyRankingPosition) _then;

  /// Create a copy of MyRankingPosition
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? position = freezed,
    Object? score = null,
  }) {
    return _then(_MyRankingPosition(
      position: freezed == position
          ? _self.position
          : position // ignore: cast_nullable_to_non_nullable
              as int?,
      score: null == score
          ? _self.score
          : score // ignore: cast_nullable_to_non_nullable
              as int,
    ));
  }
}

// dart format on
