import 'package:freezed_annotation/freezed_annotation.dart';

part 'performance.freezed.dart';
part 'performance.g.dart';

@freezed
abstract class PerformanceStatistics with _$PerformanceStatistics {
  const factory PerformanceStatistics({
    @JsonKey(name: 'totalExamsCompleted', fromJson: _intFromJson) required int totalExamsCompleted,
    @JsonKey(name: 'accuracyPercentage', fromJson: _doubleFromJson) required double accuracyPercentage,
    @JsonKey(name: 'totalQuestionsAnswered', fromJson: _nullableIntFromJson) int? totalQuestionsAnswered,
    @JsonKey(name: 'totalCorrectAnswers', fromJson: _nullableIntFromJson) int? totalCorrectAnswers,
  }) = _PerformanceStatistics;

  factory PerformanceStatistics.fromJson(Map<String, dynamic> json) =>
      _$PerformanceStatisticsFromJson(json);
}

int _intFromJson(dynamic value) {
  if (value is int) return value;
  if (value is String) return int.tryParse(value) ?? 0;
  if (value is double) return value.toInt();
  return 0;
}

double _doubleFromJson(dynamic value) {
  if (value is double) return value;
  if (value is int) return value.toDouble();
  if (value is String) return double.tryParse(value) ?? 0.0;
  return 0.0;
}

int? _nullableIntFromJson(dynamic value) {
  if (value == null) return null;
  return _intFromJson(value);
}
