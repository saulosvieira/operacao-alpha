<?php

use App\Http\Controllers\Api\QuoteController as ApiQuoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    // Listar cotações (com filtros e paginação)
    Route::get('/quotes', [ApiQuoteController::class, 'index']);
    
    // Obter detalhes de uma cotação
    Route::get('/quotes/{quote}', [ApiQuoteController::class, 'show']);
    
    // Criar nova cotação
    Route::post('/quotes', [ApiQuoteController::class, 'store']);
    
    // Atualizar cotação existente
    Route::put('/quotes/{quote}', [ApiQuoteController::class, 'update']);
    
    // Excluir cotação
    Route::delete('/quotes/{quote}', [ApiQuoteController::class, 'destroy']);
    
    // Gerar PDF da cotação
    Route::get('/quotes/{quote}/pdf', [ApiQuoteController::class, 'generatePdf']);
    
    // Enviar cotação por e-mail
    Route::post('/quotes/{quote}/send', [ApiQuoteController::class, 'sendQuote']);
    
    // Aceitar cotação
    Route::post('/quotes/{quote}/accept', [ApiQuoteController::class, 'accept']);
    
    // Rejeitar cotação
    Route::post('/quotes/{quote}/reject', [ApiQuoteController::class, 'reject']);
    
    // Gerar plano de voo (integração com IA)
    Route::post('/quotes/generate-flight-plan', [ApiQuoteController::class, 'generateFlightPlan']);
    
    // Estatísticas de cotações
    Route::get('/quotes/statistics', [ApiQuoteController::class, 'statistics']);
});
