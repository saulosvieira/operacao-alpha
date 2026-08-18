// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'performance.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

_PerformanceStatistics _$PerformanceStatisticsFromJson(
        Map<String, dynamic> json) =>
    _PerformanceStatistics(
      totalExamsCompleted: _intFromJson(json['totalExamsCompleted']),
      accuracyPercentage: _doubleFromJson(json['accuracyPercentage']),
      totalQuestionsAnswered:
          _nullableIntFromJson(json['totalQuestionsAnswered']),
      totalCorrectAnswers: _nullableIntFromJson(json['totalCorrectAnswers']),
    );

Map<String, dynamic> _$PerformanceStatisticsToJson(
        _PerformanceStatistics instance) =>
    <String, dynamic>{
      'totalExamsCompleted': instance.totalExamsCompleted,
      'accuracyPercentage': instance.accuracyPercentage,
      'totalQuestionsAnswered': instance.totalQuestionsAnswered,
      'totalCorrectAnswers': instance.totalCorrectAnswers,
    };
