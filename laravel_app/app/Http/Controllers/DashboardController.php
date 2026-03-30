<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * Redirige vers le tableau de bord correspondant au rôle de l'utilisateur.
     */
    public function index()
    {
        return match (session('role')) {
            'etudiant'   => redirect()->route('etudiant.dashboard'),
            'tuteur'     => redirect()->route('tuteur.dashboard'),
            'jury'       => redirect()->route('jury.dashboard'),
            'entreprise' => redirect()->route('entreprise.dashboard'),
            'admin'      => redirect()->route('admin.dashboard'),
            default      => redirect()->route('login'),
        };
    }
}
