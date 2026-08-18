class AppConfig {
  static const env = String.fromEnvironment('ENV', defaultValue: 'homolog');
  static const apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'https://operacao-alfa.mydevhomolog.live',
  );
  static const version = String.fromEnvironment(
    'APP_VERSION',
    defaultValue: '1.0.0+5',
  );

  static bool get isProd => env == 'prod';
  static bool get isHomolog => env == 'homolog';

  static const Set<String> dominioSistema = {
    'operacaoalfa.com.br',
    'operacao-alfa.mydevhomolog.live',
  };

  static String get versionName => version.split('+').first;
  static int get versionCode => int.tryParse(version.split('+').last) ?? 1;
}
