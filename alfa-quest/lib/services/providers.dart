import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../core/cache/cache_manager.dart';
import '../core/cache/invalidation_policy.dart';
import '../core/network/api_client.dart';
import '../core/network/auth_interceptor.dart';
import '../core/network/unauthorized_interceptor.dart';
import '../core/network/version_interceptor.dart';
import '../core/storage/database.dart';
import '../core/storage/secure_storage.dart';
import 'auth_manager.dart';
import 'connectivity_manager.dart';
import 'session_manager.dart';

// Core
final secureStorageProvider = Provider<SecureStorage>((ref) {
  return SecureStorage();
});

final databaseProvider = Provider<AppDatabase>((ref) {
  return AppDatabase();
});

final cacheManagerProvider = Provider<CacheManager>((ref) {
  return CacheManager(ref.watch(databaseProvider));
});

final invalidationPolicyProvider = Provider<InvalidationPolicy>((ref) {
  return InvalidationPolicy(ref.watch(cacheManagerProvider));
});

// Session
final sessionManagerProvider = Provider<SessionManager>((ref) {
  return SessionManager(
    secureStorage: ref.watch(secureStorageProvider),
    cacheManager: ref.watch(cacheManagerProvider),
  );
});

// Network — uses a late reference to authManager to avoid circular dep
final apiClientProvider = Provider<ApiClient>((ref) {
  final sessionManager = ref.watch(sessionManagerProvider);

  return ApiClient(
    interceptors: [
      AuthInterceptor(tokenReader: () => sessionManager.getToken()),
      UnauthorizedInterceptor(
        onUnauthorized: () async {
          await sessionManager.clearAll();
        },
      ),
      VersionInterceptor(),
    ],
  );
});

// Auth
final authManagerProvider = Provider<AuthManager>((ref) {
  return AuthManager(
    apiClient: ref.watch(apiClientProvider),
    sessionManager: ref.watch(sessionManagerProvider),
  );
});

final authStateProvider = StreamProvider<AuthStateData>((ref) {
  final authManager = ref.watch(authManagerProvider);
  return authManager.stateStream;
});

// Connectivity
final connectivityManagerProvider = Provider<ConnectivityManager>((ref) {
  final manager = ConnectivityManager();
  ref.onDispose(() => manager.dispose());
  return manager;
});
