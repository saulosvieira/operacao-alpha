import 'package:freezed_annotation/freezed_annotation.dart';
import 'exam.dart';

part 'history_item.freezed.dart';
part 'history_item.g.dart';

@freezed
abstract class HistoryItem with _$HistoryItem {
  const factory HistoryItem({
    @JsonKey(name: 'attemptId') required String attemptId,
    @JsonKey(name: 'examId') required String examId,
    @JsonKey(name: 'examTitle') required String examTitle,
    @Default(AttemptStatus.finished) AttemptStatus status,
    @JsonKey(name: 'startedAt') required DateTime startedAt,
    @JsonKey(name: 'finishedAt') DateTime? finishedAt,
    @JsonKey(name: 'accuracyPercentage', fromJson: _nullableDoubleFromJson) double? accuracyPercentage,
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
