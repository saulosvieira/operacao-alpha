import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../services/providers.dart';
import '../services/auth_manager.dart';
import '../features/auth/screens/splash_screen.dart';
import '../features/auth/screens/onboarding_screen.dart';
import '../features/auth/screens/login_screen.dart';
import '../features/auth/screens/register_screen.dart';
import '../features/dashboard/screens/dashboard_screen.dart';
import '../features/exams/screens/exam_list_screen.dart';
import '../features/exams/screens/exam_detail_screen.dart';
import '../features/exams/screens/webview_simulado_screen.dart';
import '../features/ranking/screens/ranking_screen.dart';
import '../features/profile/screens/profile_screen.dart';
import '../features/profile/screens/delete_account_screen.dart';
import '../features/notifications/screens/notifications_screen.dart';
import '../features/plans/screens/plans_screen.dart';
import '../features/history/screens/history_screen.dart';
import '../features/system/screens/force_update_screen.dart';
import '../features/shell/home_shell.dart';
import 'route_paths.dart';
import 'deep_link_handler.dart';

final deepLinkHandlerProvider =
    Provider<DeepLinkHandler>((ref) => DeepLinkHandler());

final goRouterProvider = Provider<GoRouter>((ref) {
  final authManager = ref.watch(authManagerProvider);

  return GoRouter(
    initialLocation: RoutePaths.splash,
    redirect: (context, state) {
      final authState = authManager.currentState;
      final location = state.matchedLocation;

      final isAuthRoute = location == RoutePaths.login ||
          location == RoutePaths.register ||
          location == RoutePaths.splash ||
          location == RoutePaths.onboarding;

      if (authState.state == AuthState.unauthenticated && !isAuthRoute) {
        return RoutePaths.login;
      }

      if (authState.state == AuthState.authenticated &&
          isAuthRoute &&
          location != RoutePaths.splash) {
        return RoutePaths.dashboard;
      }

      return null;
    },
    routes: [
      GoRoute(
          path: RoutePaths.splash,
          builder: (_, __) => const SplashScreen()),
      GoRoute(
          path: RoutePaths.onboarding,
          builder: (_, __) => const OnboardingScreen()),
      GoRoute(
          path: RoutePaths.login,
          builder: (_, __) => const LoginScreen()),
      GoRoute(
          path: RoutePaths.register,
          builder: (_, __) => const RegisterScreen()),
      GoRoute(
          path: RoutePaths.forceUpdate,
          builder: (_, __) => const ForceUpdateScreen()),

      // Shell with BottomNav
      ShellRoute(
        builder: (_, __, child) => HomeShell(child: child),
        routes: [
          GoRoute(
              path: RoutePaths.dashboard,
              builder: (_, __) => const DashboardScreen()),
          GoRoute(
            path: RoutePaths.simulados,
            builder: (_, __) => const ExamListScreen(),
            routes: [
              GoRoute(
                path: ':examId',
                builder: (_, state) => ExamDetailScreen(
                  examId: state.pathParameters['examId']!,
                ),
                routes: [
                  GoRoute(
                    path: 'resultado/:attemptId',
                    builder: (_, state) => ExamDetailScreen(
                      examId: state.pathParameters['examId']!,
                      highlightAttemptId:
                          state.pathParameters['attemptId'],
                    ),
                  ),
                ],
              ),
            ],
          ),
          GoRoute(
              path: RoutePaths.ranking,
              builder: (_, __) => const RankingScreen()),
          GoRoute(
              path: RoutePaths.perfil,
              builder: (_, __) => const ProfileScreen()),
        ],
      ),

      // Outside shell (no BottomNav)
      GoRoute(
        path: '/simulados/:examId/tentativa/:attemptId',
        builder: (_, state) => WebViewSimuladoScreen(
          examId: state.pathParameters['examId']!,
          attemptId: state.pathParameters['attemptId']!,
        ),
      ),
      GoRoute(
          path: RoutePaths.notificacoes,
          builder: (_, __) => const NotificationsScreen()),
      GoRoute(
          path: RoutePaths.planos,
          builder: (_, __) => const PlansScreen()),
      GoRoute(
          path: RoutePaths.historico,
          builder: (_, __) => const HistoryScreen()),
      GoRoute(
          path: RoutePaths.excluirConta,
          builder: (_, __) => const DeleteAccountScreen()),
    ],
  );
});
