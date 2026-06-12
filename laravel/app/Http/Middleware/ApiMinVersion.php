<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adiciona o header X-API-Min-Version em todas as respostas autenticadas.
 *
 * O app Flutter usa este header para comparar com sua versão local.
 * Se a versão do app for inferior, exibe a tela de Force Update (Requisito 21.7).
 */
class ApiMinVersion
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ($request->user()) {
            $minVersion = config('mobile.min_version') ?? '1.0.0';

            $response->headers->set(
                'X-API-Min-Version',
                (string) $minVersion
            );
        }

        return $response;
    }
}
