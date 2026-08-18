import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:uuid/uuid.dart';

class SecureStorage {
  static const _kSanctumToken = 'sanctum_token';
  static const _kDeviceId = 'device_id';
  static const _kRememberMe = 'remember_me';

  final FlutterSecureStorage _storage;

  SecureStorage()
      : _storage = const FlutterSecureStorage(
          iOptions: IOSOptions(
            accessibility: KeychainAccessibility.first_unlock_this_device,
            synchronizable: false,
          ),
          aOptions: AndroidOptions(
            encryptedSharedPreferences: true,
          ),
        );

  // Token
  Future<String?> readToken() => _storage.read(key: _kSanctumToken);
  Future<void> writeToken(String token) =>
      _storage.write(key: _kSanctumToken, value: token);
  Future<void> deleteToken() => _storage.delete(key: _kSanctumToken);

  // Device ID
  Future<String> getDeviceId() async {
    var id = await _storage.read(key: _kDeviceId);
    if (id == null) {
      id = const Uuid().v4();
      await _storage.write(key: _kDeviceId, value: id);
    }
    return id;
  }

  // Remember Me
  Future<bool> getRememberMe() async {
    final value = await _storage.read(key: _kRememberMe);
    return value == 'true';
  }

  Future<void> setRememberMe(bool value) =>
      _storage.write(key: _kRememberMe, value: value.toString());

  // Clear all
  Future<void> deleteAll() => _storage.deleteAll();
}
