<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Vérifie que l'utilisateur est connecté.
     * Usage dans les routes : middleware('auth.check') ou middleware('auth.check:etudiant,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Non connecté → login
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        // Rôle(s) requis précisés et rôle actuel non autorisé
        if (!empty($roles) && !in_array(session('role'), $roles)) {
            abort(403, 'Accès interdit : vous n\'avez pas les droits nécessaires.');
        }

        return $next($request);
    }
}
