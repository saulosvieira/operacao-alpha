<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Arquivo centralizado de rotas de API. Importa todos os arquivos de rotas
| da pasta api/ para manter a organização por feature.
|
*/

// Importar rotas de autenticação
require __DIR__.'/api/auth.php';

// Importar rotas de carreiras
require __DIR__.'/api/careers.php';

// Importar rotas de exams
require __DIR__.'/api/exams.php';

// Importar rotas de ranking
require __DIR__.'/api/ranking.php';

// Importar rotas de performance
require __DIR__.'/api/performance.php';

// Importar rotas de approved
require __DIR__.'/api/approved.php';

// Importar rotas de subscription
require __DIR__.'/api/subscription.php';

// Importar rotas de user
require __DIR__.'/api/user.php';

// Importar rotas de notifications
require __DIR__.'/api/notifications.php';

// Importar rotas de quotes (comentado temporariamente - controller não existe)
// require __DIR__.'/api/quotes.php';
