import 'package:freezed_annotation/freezed_annotation.dart';

part 'exam.freezed.dart';
part 'exam.g.dart';

@JsonEnum()
enum AttemptStatus {
  @JsonValue('in_progress')
  inProgress,
  finished,
}

@freezed
abstract class PreviousAttemptSummary with _$PreviousAttemptSummary {
  const factory PreviousAttemptSummary({
    @JsonKey(name: 'attemptId') required String attemptId,
    required AttemptStatus status,
    @JsonKey(name: 'startedAt') required DateTime startedAt,
    @JsonKey(name: 'finishedAt') DateTime? finishedAt,
    @JsonKey(name: 'accuracyPercentage') double? accuracyPercentage,
  }) = _PreviousAttemptSummary;

  factory PreviousAttemptSummary.fromJson(Map<String, dynamic> json) =>
      _$PreviousAttemptSummaryFromJson(json);
}

@freezed
abstract class Exam with _$Exam {
  const factory Exam({
    required String id,
    required String title,
    String? description,
    @JsonKey(name: 'numQuestions') required int numQuestions,
    @JsonKey(name: 'durationMin') required int durationMin,
    @JsonKey(name: 'isFree') required bool isFree,
    @JsonKey(name: 'careerId', fromJson: _careerIdFromJson) int? careerId,
    @JsonKey(name: 'lastAttempt') PreviousAttemptSummary? lastAttempt,
    @Default(true) bool? active,
    String? feedbackMode,
  }) = _Exam;

  factory Exam.fromJson(Map<String, dynamic> json) => _$ExamFromJson(json);
}

/// careerId vem como String da API
int? _careerIdFromJson(dynamic value) {
  if (value == null) return null;
  if (value is int) return value;
  if (value is String) return int.tryParse(value);
  return null;
}

@freezed
abstract class StartAttemptResponse with _$StartAttemptResponse {
  const factory StartAttemptResponse({
    @JsonKey(name: 'attemptId') required String attemptId,
    @JsonKey(name: 'examId') required String examId,
  }) = _StartAttemptResponse;

  factory StartAttemptResponse.fromJson(Map<String, dynamic> json) =>
      _$StartAttemptResponseFromJson(json);
}
