class RoutePaths {
  static const splash = '/splash';
  static const onboarding = '/onboarding';
  static const login = '/login';
  static const register = '/register';
  static const dashboard = '/dashboard';
  static const simulados = '/simulados';
  static const ranking = '/ranking';
  static const perfil = '/perfil';
  static const notificacoes = '/notificacoes';
  static const planos = '/planos';
  static const historico = '/historico';
  static const excluirConta = '/excluir-conta';
  static const forceUpdate = '/forcar-atualizacao';

  static String examDetail(String examId) => '/simulados/$examId';
  static String examResultado(String examId, String attemptId) =>
      '/simulados/$examId/resultado/$attemptId';
  static String tentativa(String examId, String attemptId) =>
      '/simulados/$examId/tentativa/$attemptId';
}
