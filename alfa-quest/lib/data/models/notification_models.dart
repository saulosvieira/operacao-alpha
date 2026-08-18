/// Notificação remota recebida via FCM
class RemoteNotification {
  final String? title;
  final String? body;
  final String? url;
  final Map<String, String>? extras;

  const RemoteNotification({this.title, this.body, this.url, this.extras});
}

/// Representação de uma notificação no inbox local.
/// Usa os mesmos campos da tabela drift InboxNotifications,
/// mas com DateTime ao invés de int para receivedAt.
class AppInboxNotification {
  final String id;
  final String? title;
  final String? body;
  final String? url;
  final DateTime receivedAt;
  final bool read;

  const AppInboxNotification({
    required this.id,
    this.title,
    this.body,
    this.url,
    required this.receivedAt,
    this.read = false,
  });
}
