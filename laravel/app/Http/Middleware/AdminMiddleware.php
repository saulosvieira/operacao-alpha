<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Acesso negado. Apenas administradores.'], 403);
            }
            
            return redirect()->back()->with('error', 'Acesso negado. Apenas administradores podem realizar esta ação.');
        }

        return $next($request);
    }
}