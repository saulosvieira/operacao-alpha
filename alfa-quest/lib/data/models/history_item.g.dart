// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'history_item.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

_HistoryItem _$HistoryItemFromJson(Map<String, dynamic> json) => _HistoryItem(
      attemptId: json['attemptId'] as String,
      examId: json['examId'] as String,
      examTitle: json['examTitle'] as String,
      status: $enumDecodeNullable(_$AttemptStatusEnumMap, json['status']) ??
          AttemptStatus.finished,
      startedAt: DateTime.parse(json['startedAt'] as String),
      finishedAt: json['finishedAt'] == null
          ? null
          : DateTime.parse(json['finishedAt'] as String),
      accuracyPercentage: _nullableDoubleFromJson(json['accuracyPercentage']),
    );

Map<String, dynamic> _$HistoryItemToJson(_HistoryItem instance) =>
    <String, dynamic>{
      'attemptId': instance.attemptId,
      'examId': instance.examId,
      'examTitle': instance.examTitle,
      'status': _$AttemptStatusEnumMap[instance.status]!,
      'startedAt': instance.startedAt.toIso8601String(),
      'finishedAt': instance.finishedAt?.toIso8601String(),
      'accuracyPercentage': instance.accuracyPercentage,
    };

const _$AttemptStatusEnumMap = {
  AttemptStatus.inProgress: 'in_progress',
  AttemptStatus.finished: 'finished',
};
