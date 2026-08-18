// GENERATED CODE - DO NOT MODIFY BY HAND
// coverage:ignore-file
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'plan.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

// dart format off
T _$identity<T>(T value) => value;

/// @nodoc
mixin _$Plan {
  String get id;
  String get name;
  String get description;
  @JsonKey(fromJson: _doubleFromJson)
  double get price;
  @JsonKey(name: 'durationDays', fromJson: _intFromJson)
  int get durationDays;
  List<String> get features;

  /// Create a copy of Plan
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $PlanCopyWith<Plan> get copyWith =>
      _$PlanCopyWithImpl<Plan>(this as Plan, _$identity);

  /// Serializes this Plan to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is Plan &&
            (identical(other.id, id) || other.id == id) &&
            (identical(other.name, name) || other.name == name) &&
            (identical(other.description, description) ||
                other.description == description) &&
            (identical(other.price, price) || other.price == price) &&
            (identical(other.durationDays, durationDays) ||
                other.durationDays == durationDays) &&
            const DeepCollectionEquality().equals(other.features, features));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, id, name, description, price,
      durationDays, const DeepCollectionEquality().hash(features));

  @override
  String toString() {
    return 'Plan(id: $id, name: $name, description: $description, price: $price, durationDays: $durationDays, features: $features)';
  }
}

/// @nodoc
abstract mixin class $PlanCopyWith<$Res> {
  factory $PlanCopyWith(Plan value, $Res Function(Plan) _then) =
      _$PlanCopyWithImpl;
  @useResult
  $Res call(
      {String id,
      String name,
      String description,
      @JsonKey(fromJson: _doubleFromJson) double price,
      @JsonKey(name: 'durationDays', fromJson: _intFromJson) int durationDays,
      List<String> features});
}

/// @nodoc
class _$PlanCopyWithImpl<$Res> implements $PlanCopyWith<$Res> {
  _$PlanCopyWithImpl(this._self, this._then);

  final Plan _self;
  final $Res Function(Plan) _then;

  /// Create a copy of Plan
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? id = null,
    Object? name = null,
    Object? description = null,
    Object? price = null,
    Object? durationDays = null,
    Object? features = null,
  }) {
    return _then(_self.copyWith(
      id: null == id
          ? _self.id
          : id // ignore: cast_nullable_to_non_nullable
              as String,
      name: null == name
          ? _self.name
          : name // ignore: cast_nullable_to_non_nullable
              as String,
      description: null == description
          ? _self.description
          : description // ignore: cast_nullable_to_non_nullable
              as String,
      price: null == price
          ? _self.price
          : price // ignore: cast_nullable_to_non_nullable
              as double,
      durationDays: null == durationDays
          ? _self.durationDays
          : durationDays // ignore: cast_nullable_to_non_nullable
              as int,
      features: null == features
          ? _self.features
          : features // ignore: cast_nullable_to_non_nullable
              as List<String>,
    ));
  }
}

/// Adds pattern-matching-related methods to [Plan].
extension PlanPatterns on Plan {
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
    TResult Function(_Plan value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _Plan() when $default != null:
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
    TResult Function(_Plan value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _Plan():
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
    TResult? Function(_Plan value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _Plan() when $default != null:
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
            String name,
            String description,
            @JsonKey(fromJson: _doubleFromJson) double price,
            @JsonKey(name: 'durationDays', fromJson: _intFromJson)
            int durationDays,
            List<String> features)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _Plan() when $default != null:
        return $default(_that.id, _that.name, _that.description, _that.price,
            _that.durationDays, _that.features);
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
            String name,
            String description,
            @JsonKey(fromJson: _doubleFromJson) double price,
            @JsonKey(name: 'durationDays', fromJson: _intFromJson)
            int durationDays,
            List<String> features)
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _Plan():
        return $default(_that.id, _that.name, _that.description, _that.price,
            _that.durationDays, _that.features);
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
            String name,
            String description,
            @JsonKey(fromJson: _doubleFromJson) double price,
            @JsonKey(name: 'durationDays', fromJson: _intFromJson)
            int durationDays,
            List<String> features)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _Plan() when $default != null:
        return $default(_that.id, _that.name, _that.description, _that.price,
            _that.durationDays, _that.features);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _Plan implements Plan {
  const _Plan(
      {required this.id,
      required this.name,
      required this.description,
      @JsonKey(fromJson: _doubleFromJson) required this.price,
      @JsonKey(name: 'durationDays', fromJson: _intFromJson)
      required this.durationDays,
      final List<String> features = const []})
      : _features = features;
  factory _Plan.fromJson(Map<String, dynamic> json) => _$PlanFromJson(json);

  @override
  final String id;
  @override
  final String name;
  @override
  final String description;
  @override
  @JsonKey(fromJson: _doubleFromJson)
  final double price;
  @override
  @JsonKey(name: 'durationDays', fromJson: _intFromJson)
  final int durationDays;
  final List<String> _features;
  @override
  @JsonKey()
  List<String> get features {
    if (_features is EqualUnmodifiableListView) return _features;
    // ignore: implicit_dynamic_type
    return EqualUnmodifiableListView(_features);
  }

  /// Create a copy of Plan
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$PlanCopyWith<_Plan> get copyWith =>
      __$PlanCopyWithImpl<_Plan>(this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$PlanToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _Plan &&
            (identical(other.id, id) || other.id == id) &&
            (identical(other.name, name) || other.name == name) &&
            (identical(other.description, description) ||
                other.description == description) &&
            (identical(other.price, price) || other.price == price) &&
            (identical(other.durationDays, durationDays) ||
                other.durationDays == durationDays) &&
            const DeepCollectionEquality().equals(other._features, _features));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, id, name, description, price,
      durationDays, const DeepCollectionEquality().hash(_features));

  @override
  String toString() {
    return 'Plan(id: $id, name: $name, description: $description, price: $price, durationDays: $durationDays, features: $features)';
  }
}

/// @nodoc
abstract mixin class _$PlanCopyWith<$Res> implements $PlanCopyWith<$Res> {
  factory _$PlanCopyWith(_Plan value, $Res Function(_Plan) _then) =
      __$PlanCopyWithImpl;
  @override
  @useResult
  $Res call(
      {String id,
      String name,
      String description,
      @JsonKey(fromJson: _doubleFromJson) double price,
      @JsonKey(name: 'durationDays', fromJson: _intFromJson) int durationDays,
      List<String> features});
}

/// @nodoc
class __$PlanCopyWithImpl<$Res> implements _$PlanCopyWith<$Res> {
  __$PlanCopyWithImpl(this._self, this._then);

  final _Plan _self;
  final $Res Function(_Plan) _then;

  /// Create a copy of Plan
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? id = null,
    Object? name = null,
    Object? description = null,
    Object? price = null,
    Object? durationDays = null,
    Object? features = null,
  }) {
    return _then(_Plan(
      id: null == id
          ? _self.id
          : id // ignore: cast_nullable_to_non_nullable
              as String,
      name: null == name
          ? _self.name
          : name // ignore: cast_nullable_to_non_nullable
              as String,
      description: null == description
          ? _self.description
          : description // ignore: cast_nullable_to_non_nullable
              as String,
      price: null == price
          ? _self.price
          : price // ignore: cast_nullable_to_non_nullable
              as double,
      durationDays: null == durationDays
          ? _self.durationDays
          : durationDays // ignore: cast_nullable_to_non_nullable
              as int,
      features: null == features
          ? _self._features
          : features // ignore: cast_nullable_to_non_nullable
              as List<String>,
    ));
  }
}

/// @nodoc
mixin _$CheckoutResponse {
  @JsonKey(name: 'checkoutUrl')
  String get checkoutUrl;

  /// Create a copy of CheckoutResponse
  /// with the given fields replaced by the non-null parameter values.
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  $CheckoutResponseCopyWith<CheckoutResponse> get copyWith =>
      _$CheckoutResponseCopyWithImpl<CheckoutResponse>(
          this as CheckoutResponse, _$identity);

  /// Serializes this CheckoutResponse to a JSON map.
  Map<String, dynamic> toJson();

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is CheckoutResponse &&
            (identical(other.checkoutUrl, checkoutUrl) ||
                other.checkoutUrl == checkoutUrl));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, checkoutUrl);

  @override
  String toString() {
    return 'CheckoutResponse(checkoutUrl: $checkoutUrl)';
  }
}

/// @nodoc
abstract mixin class $CheckoutResponseCopyWith<$Res> {
  factory $CheckoutResponseCopyWith(
          CheckoutResponse value, $Res Function(CheckoutResponse) _then) =
      _$CheckoutResponseCopyWithImpl;
  @useResult
  $Res call({@JsonKey(name: 'checkoutUrl') String checkoutUrl});
}

/// @nodoc
class _$CheckoutResponseCopyWithImpl<$Res>
    implements $CheckoutResponseCopyWith<$Res> {
  _$CheckoutResponseCopyWithImpl(this._self, this._then);

  final CheckoutResponse _self;
  final $Res Function(CheckoutResponse) _then;

  /// Create a copy of CheckoutResponse
  /// with the given fields replaced by the non-null parameter values.
  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? checkoutUrl = null,
  }) {
    return _then(_self.copyWith(
      checkoutUrl: null == checkoutUrl
          ? _self.checkoutUrl
          : checkoutUrl // ignore: cast_nullable_to_non_nullable
              as String,
    ));
  }
}

/// Adds pattern-matching-related methods to [CheckoutResponse].
extension CheckoutResponsePatterns on CheckoutResponse {
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
    TResult Function(_CheckoutResponse value)? $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _CheckoutResponse() when $default != null:
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
    TResult Function(_CheckoutResponse value) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _CheckoutResponse():
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
    TResult? Function(_CheckoutResponse value)? $default,
  ) {
    final _that = this;
    switch (_that) {
      case _CheckoutResponse() when $default != null:
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
    TResult Function(@JsonKey(name: 'checkoutUrl') String checkoutUrl)?
        $default, {
    required TResult orElse(),
  }) {
    final _that = this;
    switch (_that) {
      case _CheckoutResponse() when $default != null:
        return $default(_that.checkoutUrl);
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
    TResult Function(@JsonKey(name: 'checkoutUrl') String checkoutUrl) $default,
  ) {
    final _that = this;
    switch (_that) {
      case _CheckoutResponse():
        return $default(_that.checkoutUrl);
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
    TResult? Function(@JsonKey(name: 'checkoutUrl') String checkoutUrl)?
        $default,
  ) {
    final _that = this;
    switch (_that) {
      case _CheckoutResponse() when $default != null:
        return $default(_that.checkoutUrl);
      case _:
        return null;
    }
  }
}

/// @nodoc
@JsonSerializable()
class _CheckoutResponse implements CheckoutResponse {
  const _CheckoutResponse(
      {@JsonKey(name: 'checkoutUrl') required this.checkoutUrl});
  factory _CheckoutResponse.fromJson(Map<String, dynamic> json) =>
      _$CheckoutResponseFromJson(json);

  @override
  @JsonKey(name: 'checkoutUrl')
  final String checkoutUrl;

  /// Create a copy of CheckoutResponse
  /// with the given fields replaced by the non-null parameter values.
  @override
  @JsonKey(includeFromJson: false, includeToJson: false)
  @pragma('vm:prefer-inline')
  _$CheckoutResponseCopyWith<_CheckoutResponse> get copyWith =>
      __$CheckoutResponseCopyWithImpl<_CheckoutResponse>(this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$CheckoutResponseToJson(
      this,
    );
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _CheckoutResponse &&
            (identical(other.checkoutUrl, checkoutUrl) ||
                other.checkoutUrl == checkoutUrl));
  }

  @JsonKey(includeFromJson: false, includeToJson: false)
  @override
  int get hashCode => Object.hash(runtimeType, checkoutUrl);

  @override
  String toString() {
    return 'CheckoutResponse(checkoutUrl: $checkoutUrl)';
  }
}

/// @nodoc
abstract mixin class _$CheckoutResponseCopyWith<$Res>
    implements $CheckoutResponseCopyWith<$Res> {
  factory _$CheckoutResponseCopyWith(
          _CheckoutResponse value, $Res Function(_CheckoutResponse) _then) =
      __$CheckoutResponseCopyWithImpl;
  @override
  @useResult
  $Res call({@JsonKey(name: 'checkoutUrl') String checkoutUrl});
}

/// @nodoc
class __$CheckoutResponseCopyWithImpl<$Res>
    implements _$CheckoutResponseCopyWith<$Res> {
  __$CheckoutResponseCopyWithImpl(this._self, this._then);

  final _CheckoutResponse _self;
  final $Res Function(_CheckoutResponse) _then;

  /// Create a copy of CheckoutResponse
  /// with the given fields replaced by the non-null parameter values.
  @override
  @pragma('vm:prefer-inline')
  $Res call({
    Object? checkoutUrl = null,
  }) {
    return _then(_CheckoutResponse(
      checkoutUrl: null == checkoutUrl
          ? _self.checkoutUrl
          : checkoutUrl // ignore: cast_nullable_to_non_nullable
              as String,
    ));
  }
}

// dart format on
