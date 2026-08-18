import 'dart:convert';

class ExamFinishedEvent {
  final String examId;
  final String attemptId;

  const ExamFinishedEvent({required this.examId, required this.attemptId});
}

class WebViewBridge {
  static const channelName = 'OperacaoAlfaApp';

  final void Function(ExamFinishedEvent event)? onExamFinished;
  final void Function()? onRequestExit;

  WebViewBridge({this.onExamFinished, this.onRequestExit});

  /// Parse message from JavascriptChannel
  void handleMessage(String message) {
    try {
      final data = jsonDecode(message) as Map<String, dynamic>;
      final type = data['type'] as String?;

      switch (type) {
        case 'examFinished':
          final examId = data['examId'] as String? ?? '';
          final attemptId = data['attemptId'] as String? ?? '';
          if (examId.isNotEmpty && attemptId.isNotEmpty) {
            onExamFinished?.call(
              ExamFinishedEvent(examId: examId, attemptId: attemptId),
            );
          }
        case 'requestExit':
          onRequestExit?.call();
        default:
          // Unknown message type, ignore
          break;
      }
    } catch (_) {
      // Malformed JSON, ignore
    }
  }
}
