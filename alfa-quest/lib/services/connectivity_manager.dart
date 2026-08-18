import 'dart:async';

import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:flutter/foundation.dart';

enum ConnectivityStatus { online, offline }

class ConnectivityManager {
  final Connectivity _connectivity;
  final _statusNotifier =
      ValueNotifier<ConnectivityStatus>(ConnectivityStatus.online);
  final _statusController = StreamController<ConnectivityStatus>.broadcast();
  StreamSubscription<List<ConnectivityResult>>? _subscription;

  ConnectivityManager() : _connectivity = Connectivity();

  ValueListenable<ConnectivityStatus> get status => _statusNotifier;
  Stream<ConnectivityStatus> get changes => _statusController.stream;

  Future<void> init() async {
    final results = await _connectivity.checkConnectivity();
    _updateStatus(results);

    _subscription = _connectivity.onConnectivityChanged.listen(_updateStatus);
  }

  void _updateStatus(List<ConnectivityResult> results) {
    final newStatus = results.contains(ConnectivityResult.none)
        ? ConnectivityStatus.offline
        : ConnectivityStatus.online;

    if (_statusNotifier.value != newStatus) {
      _statusNotifier.value = newStatus;
      _statusController.add(newStatus);
    }
  }

  void dispose() {
    _subscription?.cancel();
    _statusNotifier.dispose();
    _statusController.close();
  }
}
