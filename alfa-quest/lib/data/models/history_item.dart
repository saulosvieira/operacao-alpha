import 'package:freezed_annotation/freezed_annotation.dart';
import 'exam.dart';

part 'history_item.freezed.dart';
part 'history_item.g.dart';

@freezed
abstract class HistoryItem with _$HistoryItem {
  const factory HistoryItem({
    // O backend de histórico (HistoryResource) não fornece attemptId;
    // fica opcional para permitir navegação apenas quando disponível.
    @JsonKey(name: 'attemptId') String? attemptId,
    @JsonKey(name: 'examId') required String examId,
    @JsonKey(name: 'examTitle') required String examTitle,
    @JsonKey(name: 'careerName') String? careerName,
    @Default(AttemptStatus.finished) AttemptStatus status,
    @JsonKey(name: 'completedAt') DateTime? completedAt,
    @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson) double? accuracyPercentage,
    @JsonKey(name: 'correctAnswers', fromJson: _nullableIntFromJson) int? correctAnswers,
    @JsonKey(name: 'totalQuestions', fromJson: _nullableIntFromJson) int? totalQuestions,
    @JsonKey(name: 'timeSpentMinutes', fromJson: _nullableIntFromJson) int? timeSpentMinutes,
  }) = _HistoryItem;

  factory HistoryItem.fromJson(Map<String, dynamic> json) =>
      _$HistoryItemFromJson(json);
}

double? _nullableDoubleFromJson(dynamic value) {
  if (value == null) return null;
  if (value is double) return value;
  if (value is int) return value.toDouble();
  if (value is String) return double.tryParse(value);
  return null;
}

int? _nullableIntFromJson(dynamic value) {
  if (value == null) return null;
  if (value is int) return value;
  if (value is double) return value.toInt();
  if (value is String) return int.tryParse(value);
  return null;
}
