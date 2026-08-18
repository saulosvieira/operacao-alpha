import 'dart:async';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:uuid/uuid.dart';
import '../core/network/api_client.dart';
import '../core/storage/secure_storage.dart';
import '../data/models/notification_models.dart';
import '../data/repositories/notifications_repository.dart';
import '../routing/deep_link_handler.dart';

class FcmService {
  final FirebaseMessaging _messaging;
  final ApiClient _apiClient;
  final SecureStorage _secureStorage;
  final NotificationsRepository _notificationsRepo;
  final DeepLinkHandler _deepLinkHandler;

  FcmService({
    required ApiClient apiClient,
    required SecureStorage secureStorage,
    required NotificationsRepository notificationsRepo,
    required DeepLinkHandler deepLinkHandler,
  })  : _messaging = FirebaseMessaging.instance,
        _apiClient = apiClient,
        _secureStorage = secureStorage,
        _notificationsRepo = notificationsRepo,
        _deepLinkHandler = deepLinkHandler;

  Future<bool> ensurePermission() async {
    final settings = await _messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
    );
    return settings.authorizationStatus == AuthorizationStatus.authorized;
  }

  Future<String?> getToken() => _messaging.getToken();

  Stream<String> get tokenRefresh => _messaging.onTokenRefresh;

  Future<void> subscribeOnBackend(String fcmToken) async {
    final deviceId = await _secureStorage.getDeviceId();
    try {
      await _apiClient.post('/api/notifications/fcm/subscribe', data: {
        'token': fcmToken,
        'device_id': deviceId,
      });
    } catch (_) {
      // Will retry on next app start
    }
  }

  Future<void> unsubscribeOnBackend() async {
    final deviceId = await _secureStorage.getDeviceId();
    try {
      await _apiClient.post('/api/notifications/fcm/unsubscribe', data: {
        'device_id': deviceId,
      });
    } catch (_) {}
  }

  void setupForegroundHandler() {
    FirebaseMessaging.onMessage.listen((message) {
      _handleMessage(message, foreground: true);
    });

    FirebaseMessaging.onMessageOpenedApp.listen((message) {
      _handleMessage(message, foreground: false);
    });
  }

  Future<RemoteMessage?> getInitialMessage() {
    return _messaging.getInitialMessage();
  }

  void _handleMessage(RemoteMessage message, {required bool foreground}) {
    final title = message.notification?.title ?? (message.data['title'] as String?);
    final body = message.notification?.body ?? (message.data['body'] as String?);
    final url = message.data['url'] as String?;

    // Skip if both title and body are empty
    if ((title == null || title.isEmpty) && (body == null || body.isEmpty)) return;

    // Save to inbox
    final notification = AppInboxNotification(
      id: const Uuid().v4(),
      title: title,
      body: body,
      url: url,
      receivedAt: DateTime.now(),
    );
    _notificationsRepo.insert(notification);
  }

  String? resolveNotificationRoute(RemoteMessage message) {
    final url = message.data['url'] as String?;
    if (url == null) return null;
    final uri = Uri.tryParse(url);
    if (uri == null) return null;
    return _deepLinkHandler.resolve(uri);
  }
}
