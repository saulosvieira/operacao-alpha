// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'exam.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

_PreviousAttemptSummary _$PreviousAttemptSummaryFromJson(
        Map<String, dynamic> json) =>
    _PreviousAttemptSummary(
      attemptId: json['attemptId'] as String,
      status: $enumDecode(_$AttemptStatusEnumMap, json['status']),
      startedAt: DateTime.parse(json['startedAt'] as String),
      finishedAt: json['finishedAt'] == null
          ? null
          : DateTime.parse(json['finishedAt'] as String),
      accuracyPercentage: (json['accuracyPercentage'] as num?)?.toDouble(),
    );

Map<String, dynamic> _$PreviousAttemptSummaryToJson(
        _PreviousAttemptSummary instance) =>
    <String, dynamic>{
      'attemptId': instance.attemptId,
      'status': _$AttemptStatusEnumMap[instance.status]!,
      'startedAt': instance.startedAt.toIso8601String(),
      'finishedAt': instance.finishedAt?.toIso8601String(),
      'accuracyPercentage': instance.accuracyPercentage,
    };

const _$AttemptStatusEnumMap = {
  AttemptStatus.inProgress: 'in_progress',
  AttemptStatus.finished: 'finished',
};

_Exam _$ExamFromJson(Map<String, dynamic> json) => _Exam(
      id: json['id'] as String,
      title: json['title'] as String,
      description: json['description'] as String?,
      numQuestions: (json['numQuestions'] as num).toInt(),
      durationMin: (json['durationMin'] as num).toInt(),
      isFree: json['isFree'] as bool,
      careerId: _careerIdFromJson(json['careerId']),
      lastAttempt: json['lastAttempt'] == null
          ? null
          : PreviousAttemptSummary.fromJson(
              json['lastAttempt'] as Map<String, dynamic>),
      active: json['active'] as bool? ?? true,
      feedbackMode: json['feedbackMode'] as String?,
    );

Map<String, dynamic> _$ExamToJson(_Exam instance) => <String, dynamic>{
      'id': instance.id,
      'title': instance.title,
      'description': instance.description,
      'numQuestions': instance.numQuestions,
      'durationMin': instance.durationMin,
      'isFree': instance.isFree,
      'careerId': instance.careerId,
      'lastAttempt': instance.lastAttempt,
      'active': instance.active,
      'feedbackMode': instance.feedbackMode,
    };

_StartAttemptResponse _$StartAttemptResponseFromJson(
        Map<String, dynamic> json) =>
    _StartAttemptResponse(
      attemptId: json['attemptId'] as String,
      examId: json['examId'] as String,
    );

Map<String, dynamic> _$StartAttemptResponseToJson(
        _StartAttemptResponse instance) =>
    <String, dynamic>{
      'attemptId': instance.attemptId,
      'examId': instance.examId,
    };
