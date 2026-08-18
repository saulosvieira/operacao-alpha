import 'dart:collection';
import '../core/utils/url_patterns.dart';
import 'route_paths.dart';

class DeepLinkHandler {
  final Queue<Uri> _pendingLinks = Queue<Uri>();

  String? resolve(Uri uri) {
    if (!isHostFromDomain(uri)) return null;
    final path = uri.path;

    // Tentativa pattern: /simulado/:id/(tentativa|executar)/:attemptId
    final tentativaMatch =
        RegExp(r'^/simulado/([^/]+)/(tentativa|executar)/([^/]+)/?$')
            .firstMatch(path);
    if (tentativaMatch != null) {
      return RoutePaths.tentativa(
          tentativaMatch.group(1)!, tentativaMatch.group(3)!);
    }

    // Resultado pattern: /simulado/:id/resultado/:attemptId
    final resultadoMatch =
        RegExp(r'^/simulado/([^/]+)/resultado/([^/]+)/?$').firstMatch(path);
    if (resultadoMatch != null) {
      return RoutePaths.examResultado(
          resultadoMatch.group(1)!, resultadoMatch.group(2)!);
    }

    // Simulado detail: /simulado/:id
    final detailMatch = RegExp(r'^/simulado/([^/]+)/?$').firstMatch(path);
    if (detailMatch != null) {
      return RoutePaths.examDetail(detailMatch.group(1)!);
    }

    // Known paths
    if (path.startsWith('/dashboard')) return RoutePaths.dashboard;
    if (path.startsWith('/perfil')) return RoutePaths.perfil;
    if (path.startsWith('/ranking')) return RoutePaths.ranking;
    if (path.startsWith('/simulados')) return RoutePaths.simulados;

    // Fallback
    return RoutePaths.dashboard;
  }

  void enqueuePending(Uri uri) => _pendingLinks.addLast(uri);

  Uri? consumePending() {
    if (_pendingLinks.isEmpty) return null;
    return _pendingLinks.removeFirst();
  }

  bool get hasPending => _pendingLinks.isNotEmpty;
}
