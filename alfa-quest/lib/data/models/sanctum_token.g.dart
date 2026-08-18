// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'sanctum_token.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

_SanctumToken _$SanctumTokenFromJson(Map<String, dynamic> json) =>
    _SanctumToken(
      value: json['value'] as String,
      obtainedAt: DateTime.parse(json['obtained_at'] as String),
      persistAcrossLaunches: json['persist_across_launches'] as bool? ?? false,
    );

Map<String, dynamic> _$SanctumTokenToJson(_SanctumToken instance) =>
    <String, dynamic>{
      'value': instance.value,
      'obtained_at': instance.obtainedAt.toIso8601String(),
      'persist_across_launches': instance.persistAcrossLaunches,
    };
