sealed class AppException implements Exception {
  final String message;
  const AppException(this.message);

  /// Log seguro: nunca expor token, email, body de resposta
  String toLogString() => '[$runtimeType] $message';
}

// === API Exceptions ===
sealed class ApiException extends AppException {
  final int statusCode;
  const ApiException(this.statusCode, super.message);

  @override
  String toLogString() => '[$runtimeType] status=$statusCode';
}

class UnauthenticatedException extends ApiException {
  const UnauthenticatedException([String message = 'Não autenticado'])
      : super(401, message);
}

class ValidationException extends ApiException {
  final Map<String, List<String>> fieldErrors;
  const ValidationException({
    required this.fieldErrors,
    String message = 'Erro de validação',
  }) : super(422, message);
}

class RateLimitException extends ApiException {
  final Duration? retryAfter;
  const RateLimitException({this.retryAfter})
      : super(429, 'Limite de tentativas excedido');
}

class ServerException extends ApiException {
  const ServerException([int statusCode = 500])
      : super(statusCode, 'Servidor temporariamente indisponível');
}

class NotFoundException extends ApiException {
  const NotFoundException([String message = 'Recurso não encontrado'])
      : super(404, message);
}

class ApiClientException extends ApiException {
  const ApiClientException(super.statusCode, super.message);
}

class ForceUpdateRequiredException extends ApiException {
  const ForceUpdateRequiredException()
      : super(426, 'Atualização obrigatória');
}

// === Network Exceptions ===
sealed class NetworkException extends AppException {
  const NetworkException(super.message);
}

class TimeoutException extends NetworkException {
  const TimeoutException() : super('Tempo de conexão esgotado');
}

class NoConnectionException extends NetworkException {
  const NoConnectionException() : super('Sem conexão com a internet');
}

class TlsException extends NetworkException {
  const TlsException() : super('Conexão segura indisponível');
}

// === WebView Exceptions ===
sealed class WebViewException extends AppException {
  const WebViewException(super.message);
}

class WebViewLoadFailedException extends WebViewException {
  final int? errorCode;
  const WebViewLoadFailedException({this.errorCode})
      : super('Falha ao carregar página');
}

class WebViewNavigationRejectedException extends WebViewException {
  final Uri rejected;
  const WebViewNavigationRejectedException(this.rejected)
      : super('Navegação rejeitada');
}

// === FCM Exceptions ===
sealed class FcmException extends AppException {
  const FcmException(super.message);
}

class FcmTokenRegistrationFailedException extends FcmException {
  final int attempt;
  const FcmTokenRegistrationFailedException({this.attempt = 0})
      : super('Falha ao registrar token FCM');
}

class FcmPermissionDeniedException extends FcmException {
  const FcmPermissionDeniedException()
      : super('Permissão de notificação negada');
}

// === Checkout Exceptions ===
sealed class CheckoutException extends AppException {
  const CheckoutException(super.message);
}

class CheckoutUrlUnavailableException extends CheckoutException {
  const CheckoutUrlUnavailableException()
      : super('Não foi possível iniciar o pagamento');
}

class SubscriptionStatusStaleException extends CheckoutException {
  const SubscriptionStatusStaleException()
      : super('Status de assinatura desatualizado');
}
