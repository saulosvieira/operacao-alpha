// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'plan.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

_Plan _$PlanFromJson(Map<String, dynamic> json) => _Plan(
      id: json['id'] as String,
      name: json['name'] as String,
      description: json['description'] as String,
      price: _doubleFromJson(json['price']),
      durationDays: _intFromJson(json['durationDays']),
      features: (json['features'] as List<dynamic>?)
              ?.map((e) => e as String)
              .toList() ??
          const [],
    );

Map<String, dynamic> _$PlanToJson(_Plan instance) => <String, dynamic>{
      'id': instance.id,
      'name': instance.name,
      'description': instance.description,
      'price': instance.price,
      'durationDays': instance.durationDays,
      'features': instance.features,
    };

_CheckoutResponse _$CheckoutResponseFromJson(Map<String, dynamic> json) =>
    _CheckoutResponse(
      checkoutUrl: json['checkoutUrl'] as String,
    );

Map<String, dynamic> _$CheckoutResponseToJson(_CheckoutResponse instance) =>
    <String, dynamic>{
      'checkoutUrl': instance.checkoutUrl,
    };
