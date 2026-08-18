import 'dart:async';

import 'package:dio/dio.dart';

import '../core/errors/app_exception.dart';
import '../core/network/api_client.dart';
import '../data/models/user.dart';
import 'session_manager.dart';

enum AuthState {
  loading,
  authenticated,
  unauthenticated,
}

class AuthStateData {
  final AuthState state;
  final User? user;

  const AuthStateData({required this.state, this.user});

  const AuthStateData.loading()
      : state = AuthState.loading,
        user = null;

  const AuthStateData.authenticated([this.user])
      : state = AuthState.authenticated;

  const AuthStateData.unauthenticated()
      : state = AuthState.unauthenticated,
        user = null;
}

class AuthManager {
  final ApiClient _apiClient;
  final SessionManager _sessionManager;

  final _stateController = StreamController<AuthStateData>.broadcast();
  AuthStateData _currentState = const AuthStateData.loading();

  AuthManager({
    required ApiClient apiClient,
    required SessionManager sessionManager,
  })  : _apiClient = apiClient,
        _sessionManager = sessionManager;

  Stream<AuthStateData> get stateStream => _stateController.stream;
  AuthStateData get currentState => _currentState;

  void _emit(AuthStateData state) {
    _currentState = state;
    _stateController.add(state);
  }

  /// Bootstrap: check existing token validity
  Future<AuthStateData> bootstrap() async {
    _emit(const AuthStateData.loading());

    final token = await _sessionManager.getToken();
    if (token == null) {
      _emit(const AuthStateData.unauthenticated());
      return _currentState;
    }

    try {
      final response = await _apiClient.get<Map<String, dynamic>>(
        '/api/me',
        options: Options(receiveTimeout: const Duration(seconds: 30)),
      );
      final data = response.data!;
      // API pode retornar {"user": {...}} ou {...} diretamente
      final userJson = data.containsKey('user')
          ? data['user'] as Map<String, dynamic>
          : data;
      final user = User.fromJson(userJson);
      _emit(AuthStateData.authenticated(user));
    } on UnauthenticatedException {
      await _sessionManager.clearToken();
      _emit(const AuthStateData.unauthenticated());
    } on AppException {
      // Timeout or network error: go to dashboard in offline mode
      _emit(const AuthStateData.authenticated(null));
    }

    return _currentState;
  }

  Future<User> login({
    required String email,
    required String password,
    required bool rememberMe,
  }) async {
    _emit(const AuthStateData.loading());

    try {
      final response = await _apiClient.post<Map<String, dynamic>>(
        '/api/login',
        data: {'email': email, 'password': password},
      );

      final data = response.data!;
      final token = data['token'] as String;
      final user = User.fromJson(data['user'] as Map<String, dynamic>);

      await _sessionManager.saveToken(
        token,
        persistAcrossLaunches: rememberMe,
      );

      _emit(AuthStateData.authenticated(user));
      return user;
    } on AppException {
      _emit(const AuthStateData.unauthenticated());
      rethrow;
    }
  }

  Future<User> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
  }) async {
    _emit(const AuthStateData.loading());

    try {
      final response = await _apiClient.post<Map<String, dynamic>>(
        '/api/register',
        data: {
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
        },
        timeout: const Duration(seconds: 15),
      );

      final data = response.data!;
      final token = data['token'] as String;
      final user = User.fromJson(data['user'] as Map<String, dynamic>);

      await _sessionManager.saveToken(token, persistAcrossLaunches: true);

      _emit(AuthStateData.authenticated(user));
      return user;
    } on AppException {
      _emit(const AuthStateData.unauthenticated());
      rethrow;
    }
  }

  Future<void> logout() async {
    try {
      await _apiClient.post('/api/logout');
    } catch (_) {
      // Ignore network errors during logout
    }
    await _sessionManager.clearAll();
    _emit(const AuthStateData.unauthenticated());
  }

  void dispose() {
    _stateController.close();
  }
}
