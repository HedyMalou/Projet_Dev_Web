<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserActivite;

/**
 * Middleware de journalisation des activités utilisateur.
 *
 * Enregistre dans la table USER_ACTIVITE chaque requête authentifiée
 * (méthode HTTP + chemin), classée en "acces" pour les lectures (GET)
 * et en "action" pour les écritures (POST, PUT, PATCH, DELETE).
 *
 * L'enregistrement est tenté après l'exécution de la requête pour ne pas
 * bloquer la réponse en cas d'erreur d'écriture en base.
 */
class LogActivite
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (session('user_id')) {
            try {
                $type = in_array($request->method(), ['POST','PUT','PATCH','DELETE']) ? 'action' : 'acces';
                UserActivite::create([
                    'id_utilisateur' => session('user_id'),
                    'type'           => $type,
                    'detail'         => $request->method().' '.substr($request->path(), 0, 240),
                    'date_action'    => now(),
                ]);
            } catch (\Throwable $ex) {
                // La journalisation ne doit jamais empêcher la requête de se terminer
            }
        }

        return $response;
    }
}
