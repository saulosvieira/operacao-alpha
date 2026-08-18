import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../../../core/config/app_config.dart';
import '../../../core/cache/invalidation_policy.dart';
import '../../../core/utils/url_patterns.dart';
import '../../../services/providers.dart';
import '../../../services/webview_bridge.dart';
import '../../../services/connectivity_manager.dart';
import '../../../routing/route_paths.dart';

class WebViewSimuladoScreen extends ConsumerStatefulWidget {
  final String examId;
  final String attemptId;

  const WebViewSimuladoScreen({
    super.key,
    required this.examId,
    required this.attemptId,
  });

  @override
  ConsumerState<WebViewSimuladoScreen> createState() =>
      _WebViewSimuladoScreenState();
}

class _WebViewSimuladoScreenState extends ConsumerState<WebViewSimuladoScreen> {
  WebViewController? _controller;
  late final WebViewBridge _bridge;
  bool _isLoading = true;
  bool _hasError = false;
  bool _isOffline = false;
  bool _tokenInjected = false;
  String? _targetUrl;

  @override
  void initState() {
    super.initState();
    _bridge = WebViewBridge(
      onExamFinished: _onExamFinished,
      onRequestExit: _onRequestExit,
    );
    _initController();
    _listenConnectivity();
  }

  void _listenConnectivity() {
    final connectivity = ref.read(connectivityManagerProvider);
    connectivity.status.addListener(_onConnectivityChanged);
  }

  void _onConnectivityChanged() {
    final connectivity = ref.read(connectivityManagerProvider);
    final offline = connectivity.status.value == ConnectivityStatus.offline;
    if (mounted && offline != _isOffline) {
      setState(() => _isOffline = offline);
    }
  }

  void _initController() {
    final sessionManager = ref.read(sessionManagerProvider);
    final authManager = ref.read(authManagerProvider);

    _targetUrl =
        '${AppConfig.apiBaseUrl}/simulado/${widget.examId}/tentativa/${widget.attemptId}';

    final ctrl = WebViewController();
    _controller = ctrl;

    ctrl.setJavaScriptMode(JavaScriptMode.unrestricted);
    ctrl.setNavigationDelegate(NavigationDelegate(
      onPageStarted: (url) async {
        if (mounted) setState(() { _isLoading = true; _hasError = false; });

        // Inject token BEFORE the page's JavaScript runs
        // onPageStarted fires when navigation starts - origin is set but JS hasn't executed yet
        if (!_tokenInjected) {
          _tokenInjected = true;
          final token = await sessionManager.getToken();
          if (token != null) {
            final user = authManager.currentState.user;
            final userJson = user != null
                ? '{"id":"${user.id}","name":"${user.name}","email":"${user.email}","role":"${user.role ?? 'user'}","subscriptionStatus":"${user.subscriptionStatus.name}"}'
                : '{"id":"0","name":"User","email":"user@app","role":"user","subscriptionStatus":"trial"}';

            debugPrint('[WebView] Injecting session before React loads...');
            await ctrl.runJavaScript('''
              localStorage.setItem('auth_token', '$token');
              localStorage.setItem('operacao-alfa-user', '$userJson');
            ''');
          }
        }
      },
      onPageFinished: (url) async {
        debugPrint('[WebView] Page finished: $url');
        if (mounted) setState(() => _isLoading = false);

        final uri = Uri.tryParse(url);
        if (uri == null) return;

        // Inject URL change observer to catch React Router client-side navigation
        if (_tokenInjected) {
          await ctrl.runJavaScript('''
            (function() {
              if (window._appNavObserverSet) return;
              window._appNavObserverSet = true;
              
              // Override pushState/replaceState to detect React Router navigation
              var origPush = history.pushState;
              var origReplace = history.replaceState;
              
              function checkPath(path) {
                var isSimulado = /^\\/simulado\\/[^/]+\\/(tentativa|executar)\\/[^/]+/.test(path);
                var isResultado = /^\\/simulado\\/[^/]+\\/resultado\\/[^/]+/.test(path);
                if (!isSimulado && !isResultado && path !== '/') {
                  // Notify Flutter to exit WebView
                  if (window.${WebViewBridge.channelName}) {
                    ${WebViewBridge.channelName}.postMessage(JSON.stringify({type: "requestExit"}));
                  }
                }
              }
              
              history.pushState = function() {
                origPush.apply(this, arguments);
                checkPath(window.location.pathname);
              };
              history.replaceState = function() {
                origReplace.apply(this, arguments);
                checkPath(window.location.pathname);
              };
            })();
          ''');
        }

        // Detect resultado URL -> exam finished, go back to Flutter
        if (isResultadoPath(uri.path)) {
          _onExamFinished(ExamFinishedEvent(
              examId: widget.examId, attemptId: widget.attemptId));
          return;
        }

        // If navigated away from simulado flow (tentativa/resultado), return to Flutter
        if (isHostFromDomain(uri) &&
            !isTentativaPath(uri.path) &&
            !isResultadoPath(uri.path) &&
            _tokenInjected) {
          debugPrint('[WebView] Left simulado flow, returning to app: ${uri.path}');
          if (mounted) context.go(RoutePaths.simulados);
        }
      },
      onWebResourceError: (error) {
        debugPrint('[WebView] Error: ${error.description} (${error.errorCode}) isForMainFrame=${error.isForMainFrame}');
        if (error.isForMainFrame == true && mounted) {
          setState(() { _hasError = true; _isLoading = false; });
        }
      },
      onNavigationRequest: (request) {
        final uri = Uri.parse(request.url);
        if (!isHostFromDomain(uri) && !request.url.startsWith('about:') && !request.url.startsWith('data:')) {
          debugPrint('[WebView] Blocked external navigation: ${request.url}');
          return NavigationDecision.prevent;
        }
        // Block navigation away from simulado flow (e.g. /simulados, /desempenho, /login)
        if (isHostFromDomain(uri) &&
            !isTentativaPath(uri.path) &&
            !isResultadoPath(uri.path) &&
            _tokenInjected) {
          debugPrint('[WebView] Blocked PWA navigation outside simulado: ${uri.path}');
          // Return to Flutter app
          if (mounted) {
            WidgetsBinding.instance.addPostFrameCallback((_) {
              if (mounted) context.go(RoutePaths.simulados);
            });
          }
          return NavigationDecision.prevent;
        }
        return NavigationDecision.navigate;
      },
    ));
    ctrl.addJavaScriptChannel(
      WebViewBridge.channelName,
      onMessageReceived: (message) => _bridge.handleMessage(message.message),
    );

    // Load the simulado URL directly
    debugPrint('[WebView] Loading: $_targetUrl');
    ctrl.loadRequest(Uri.parse(_targetUrl!));
  }

  void _onExamFinished(ExamFinishedEvent event) {
    final policy = ref.read(invalidationPolicyProvider);
    policy.invalidate(CacheInvalidationEvent.examFinished);
    if (mounted) context.go(RoutePaths.simulados);
  }

  void _onRequestExit() {
    // Called by React Router navigation observer when leaving simulado flow
    debugPrint('[WebView] requestExit received - returning to app');
    if (mounted) context.go(RoutePaths.simulados);
  }

  Future<void> _showExitDialog() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Abandonar simulado?'),
        content: const Text(
            'Seu progresso nesta tentativa será mantido e você poderá retomar depois.'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(ctx, false),
              child: const Text('Continuar')),
          FilledButton(
              onPressed: () => Navigator.pop(ctx, true),
              child: const Text('Sair')),
        ],
      ),
    );
    if (confirm == true && mounted) context.go(RoutePaths.simulados);
  }

  Future<void> _retry() async {
    setState(() { _hasError = false; _isLoading = true; });
    await _controller?.loadRequest(Uri.parse(_targetUrl!));
  }

  @override
  void dispose() {
    final connectivity = ref.read(connectivityManagerProvider);
    connectivity.status.removeListener(_onConnectivityChanged);
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, _) async {
        if (didPop) return;
        if (_controller != null && await _controller!.canGoBack()) {
          await _controller!.goBack();
        } else {
          _showExitDialog();
        }
      },
      child: Scaffold(
        body: SafeArea(
          child: Stack(
            children: [
              if (_controller != null)
                WebViewWidget(controller: _controller!),

              // Loading indicator
              if (_isLoading)
                const Center(child: CircularProgressIndicator()),

              // Error overlay
              if (_hasError)
                Container(
                  color: Theme.of(context).scaffoldBackgroundColor,
                  child: Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.error_outline, size: 48,
                            color: Colors.red),
                        const SizedBox(height: 16),
                        const Text('Erro ao carregar o simulado'),
                        const SizedBox(height: 16),
                        FilledButton(
                            onPressed: _retry,
                            child: const Text('Tentar novamente')),
                        const SizedBox(height: 8),
                        TextButton(
                          onPressed: () => context.go(RoutePaths.simulados),
                          child: const Text('Voltar'),
                        ),
                      ],
                    ),
                  ),
                ),

              // Offline overlay
              if (_isOffline && !_hasError)
                Positioned(
                  top: 0,
                  left: 0,
                  right: 0,
                  child: MaterialBanner(
                    content: const Text('Sem conexão com a internet'),
                    leading: const Icon(Icons.wifi_off),
                    actions: [
                      TextButton(
                          onPressed: _retry,
                          child: const Text('Tentar novamente')),
                    ],
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
