// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'history_item.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

_HistoryItem _$HistoryItemFromJson(Map<String, dynamic> json) => _HistoryItem(
      attemptId: json['attemptId'] as String?,
      examId: json['examId'] as String,
      examTitle: json['examTitle'] as String,
      careerName: json['careerName'] as String?,
      status: $enumDecodeNullable(_$AttemptStatusEnumMap, json['status']) ??
          AttemptStatus.finished,
      completedAt: json['completedAt'] == null
          ? null
          : DateTime.parse(json['completedAt'] as String),
      accuracyPercentage: _nullableDoubleFromJson(json['accuracyPercentage']),
      correctAnswers: _nullableIntFromJson(json['correctAnswers']),
      totalQuestions: _nullableIntFromJson(json['totalQuestions']),
      timeSpentMinutes: _nullableIntFromJson(json['timeSpentMinutes']),
    );

Map<String, dynamic> _$HistoryItemToJson(_HistoryItem instance) =>
    <String, dynamic>{
      'attemptId': instance.attemptId,
      'examId': instance.examId,
      'examTitle': instance.examTitle,
      'careerName': instance.careerName,
      'status': _$AttemptStatusEnumMap[instance.status]!,
      'completedAt': instance.completedAt?.toIso8601String(),
      'accuracyPercentage': instance.accuracyPercentage,
      'correctAnswers': instance.correctAnswers,
      'totalQuestions': instance.totalQuestions,
      'timeSpentMinutes': instance.timeSpentMinutes,
    };

const _$AttemptStatusEnumMap = {
  AttemptStatus.inProgress: 'in_progress',
  AttemptStatus.finished: 'finished',
};
