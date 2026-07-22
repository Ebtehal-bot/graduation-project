import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/session_manager.dart';
import '../../../core/network/token_storage.dart';
import '../../../data/models/user_model.dart';
import '../../../data/repositories/auth_repository.dart';

enum AuthStatus { unknown, authenticated, unauthenticated }

class AuthState {
  final AuthStatus status;
  final String? token;
  final String? userId;
  final String? userName;
  final UserModel? user;

  const AuthState({
    this.status = AuthStatus.unknown,
    this.token,
    this.userId,
    this.userName,
    this.user,
  });

  AuthState copyWith({
    AuthStatus? status,
    String? token,
    String? userId,
    String? userName,
    UserModel? user,
  }) {
    return AuthState(
      status: status ?? this.status,
      token: token ?? this.token,
      userId: userId ?? this.userId,
      userName: userName ?? this.userName,
      user: user ?? this.user,
    );
  }

  bool get isAuthenticated => status == AuthStatus.authenticated;
}

class AuthNotifier extends StateNotifier<AuthState> {
  final TokenStorage _tokenStorage;
  final AuthRepository _authRepository;

  AuthNotifier(this._tokenStorage, this._authRepository)
      : super(const AuthState());

  Future<void> checkAuthStatus() async {
    try {
      final token = await _tokenStorage.getToken();
      final userId = await _tokenStorage.getUserId();
      final userName = await _tokenStorage.getUserName();

      if (token != null && token.isNotEmpty) {
        state = AuthState(
          status: AuthStatus.authenticated,
          token: token,
          userId: userId,
          userName: userName,
        );
        await fetchProfile();
      } else {
        state = const AuthState(status: AuthStatus.unauthenticated);
      }
    } catch (e) {
      state = const AuthState(status: AuthStatus.unauthenticated);
    }
  }

  Future<bool> loginWithCredentials({
    required String email,
    required String password,
  }) async {
    try {
      final response = await _authRepository.login(
        email: email,
        password: password,
      );
      final token = response['token'] as String? ?? '';
      final userData = response['user'];
      final user = UserModel.fromJson(
          (userData is Map<String, dynamic>) ? userData : {});

      await _tokenStorage.saveToken(token);
      await _tokenStorage.saveUserId(user.id.toString());
      await _tokenStorage.saveUserName(user.name);

      state = AuthState(
        status: AuthStatus.authenticated,
        token: token,
        userId: user.id.toString(),
        userName: user.name,
        user: user,
      );
      return true;
    } catch (e) {
      rethrow;
    }
  }

  Future<void> login({
    required String token,
    required String userId,
    required String userName,
    UserModel? user,
  }) async {
    await _tokenStorage.saveToken(token);
    await _tokenStorage.saveUserId(userId);
    await _tokenStorage.saveUserName(userName);

    state = AuthState(
      status: AuthStatus.authenticated,
      token: token,
      userId: userId,
      userName: userName,
      user: user,
    );
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
    String? address,
  }) async {
    try {
      final response = await _authRepository.register(
        name: name,
        email: email,
        password: password,
        passwordConfirmation: passwordConfirmation,
        phone: phone,
        address: address,
      );
      final token = response['token'] as String? ?? '';
      final userData = response['user'];
      final user = UserModel.fromJson(
          (userData is Map<String, dynamic>) ? userData : {});

      await _tokenStorage.saveToken(token);
      await _tokenStorage.saveUserId(user.id.toString());
      await _tokenStorage.saveUserName(user.name);

      state = AuthState(
        status: AuthStatus.authenticated,
        token: token,
        userId: user.id.toString(),
        userName: user.name,
        user: user,
      );
      return true;
    } catch (e) {
      rethrow;
    }
  }

  Future<void> setUser(UserModel user) async {
    state = state.copyWith(
      user: user,
      userName: user.name,
      userId: user.id.toString(),
    );
    await _tokenStorage.saveUserId(user.id.toString());
    await _tokenStorage.saveUserName(user.name);
  }

  Future<void> fetchProfile() async {
    try {
      final profileData = await _authRepository.getProfile();
      final user = UserModel.fromJson(profileData);
      state = state.copyWith(user: user, userName: user.name);
      await _tokenStorage.saveUserName(user.name);
    } catch (_) {}
  }

  Future<void> logout() async {
    try {
      await _authRepository.logout();
    } catch (_) {}
    await _tokenStorage.clearAll();
    state = const AuthState(status: AuthStatus.unauthenticated);
  }
}

final authProvider =
    StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  final notifier = AuthNotifier(
    ref.watch(tokenStorageProvider),
    ref.watch(authRepositoryProvider),
  );

  ref.listen<bool>(sessionExpiredProvider, (prev, next) {
    if (next == true && prev == false) {
      notifier.logout();
    }
  });

  return notifier;
});
