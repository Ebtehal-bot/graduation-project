import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class TokenStorage {
  final FlutterSecureStorage _storage;

  TokenStorage(this._storage);

  static const _tokenKey = 'auth_token';
  static const _userIdKey = 'user_id';
  static const _userNameKey = 'user_name';
  static const _userEmailKey = 'user_email';

  Future<String?> getToken() => _storage.read(key: _tokenKey);
  Future<void> saveToken(String token) =>
      _storage.write(key: _tokenKey, value: token);
  Future<void> deleteToken() => _storage.delete(key: _tokenKey);

  Future<String?> getUserId() => _storage.read(key: _userIdKey);
  Future<void> saveUserId(String id) =>
      _storage.write(key: _userIdKey, value: id);
  Future<void> deleteUserId() => _storage.delete(key: _userIdKey);

  Future<String?> getUserName() => _storage.read(key: _userNameKey);
  Future<void> saveUserName(String name) =>
      _storage.write(key: _userNameKey, value: name);
  Future<void> deleteUserName() => _storage.delete(key: _userNameKey);

  Future<String?> getUserEmail() => _storage.read(key: _userEmailKey);
  Future<void> saveUserEmail(String email) =>
      _storage.write(key: _userEmailKey, value: email);
  Future<void> deleteUserEmail() => _storage.delete(key: _userEmailKey);

  Future<void> saveUserData({
    required String token,
    required String userId,
    required String userName,
    String? email,
  }) async {
    await saveToken(token);
    await saveUserId(userId);
    await saveUserName(userName);
    if (email != null) {
      await saveUserEmail(email);
    }
  }

  Future<void> clearAll() async {
    await _storage.deleteAll();
  }
}

final tokenStorageProvider = Provider<TokenStorage>((ref) {
  return TokenStorage(const FlutterSecureStorage());
});
