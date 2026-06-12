<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Versão mínima do app mobile (Flutter)
    |--------------------------------------------------------------------------
    |
    | Define a versão mínima obrigatória do app Flutter para comunicar-se com
    | a API. O valor é enviado no header X-API-Min-Version em todas as respostas
    | autenticadas. O app Flutter compara este valor com sua versão local e,
    | caso esteja abaixo, exibe a tela de Force Update impedindo o uso.
    |
    | Ao publicar uma nova versão obrigatória do app, basta atualizar a env var
    | MOBILE_MIN_VERSION para a versão mínima aceitável (semver: MAJOR.MINOR.PATCH).
    |
    */

    'min_version' => env('MOBILE_MIN_VERSION', '1.0.0'),

];
