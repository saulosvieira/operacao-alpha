final RegExp kUrlTentativaPattern = RegExp(
  r'^/simulado/[^/]+/(tentativa|executar)/[^/]+/?$',
);

final RegExp kUrlResultadoPattern = RegExp(
  r'^/simulado/[^/]+/resultado/[^/]+/?$',
);

const Set<String> kDominioSistema = {
  'operacaoalfa.com.br',
  'operacao-alfa.mydevhomolog.live',
};

bool isTentativaPath(String path) => kUrlTentativaPattern.hasMatch(path);
bool isResultadoPath(String path) => kUrlResultadoPattern.hasMatch(path);
bool isHostFromDomain(Uri uri) => kDominioSistema.contains(uri.host);
