import '../core/storage/secure_storage.dart';
import '../core/cache/cache_manager.dart';

const kPwaTokenKey = 'auth_token';

class SessionManager {
  final SecureStorage _secureStorage;
  final CacheManager _cacheManager;
  String? _inMemoryToken;

  SessionManager({
    required SecureStorage secureStorage,
    required CacheManager cacheManager,
  })  : _secureStorage = secureStorage,
        _cacheManager = cacheManager;

  Future<String?> getToken() async {
    if (_inMemoryToken != null) return _inMemoryToken;

    final rememberMe = await _secureStorage.getRememberMe();
    if (!rememberMe) return null;

    return _secureStorage.readToken();
  }

  Future<void> saveToken(
    String token, {
    required bool persistAcrossLaunches,
  }) async {
    _inMemoryToken = token;
    await _secureStorage.setRememberMe(persistAcrossLaunches);

    if (persistAcrossLaunches) {
      await _secureStorage.writeToken(token);
    } else {
      await _secureStorage.deleteToken();
    }
  }

  Future<void> clearToken() async {
    _inMemoryToken = null;
    await _secureStorage.deleteToken();
  }

  Future<void> clearAll() async {
    _inMemoryToken = null;
    await _secureStorage.deleteAll();
    await _cacheManager.clearAll();
  }

  /// JavaScript to inject token into WebView localStorage
  String getTokenInjectionJs(String token) {
    return "window.localStorage.setItem('$kPwaTokenKey', '$token');";
  }

  /// JavaScript to inject full session (token + user) into WebView localStorage
  String getSessionInjectionJs(String token, {Map<String, dynamic>? userJson}) {
    final buffer = StringBuffer();
    buffer.writeln("window.localStorage.setItem('$kPwaTokenKey', '$token');");
    if (userJson != null) {
      // Escape JSON for JS string
      final userStr = userJson.toString()
          .replaceAll('\\', '\\\\')
          .replaceAll("'", "\\'");
      buffer.writeln("window.localStorage.setItem('operacao-alfa-user', JSON.stringify($userJson));");
    }
    return buffer.toString();
  }
}
