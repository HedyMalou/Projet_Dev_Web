<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware d'authentification basé sur la session.
 *
 * Vérifie qu'un utilisateur est connecté (présence de user_id en session)
 * et, optionnellement, qu'il dispose d'un rôle autorisé pour la route.
 */
class CheckAuth
{
    /**
     * Vérifie que l'utilisateur est connecté.
     *
     * Usage dans les routes :
     *   middleware('auth.check')
     *   middleware('auth.check:etudiant,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pas de session active : redirection vers la page de connexion
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        // Filtrage par rôle si la route en exige
        if (!empty($roles) && !in_array(session('role'), $roles)) {
            abort(403, 'Accès interdit : vous n\'avez pas les droits nécessaires.');
        }

        return $next($request);
    }
}
