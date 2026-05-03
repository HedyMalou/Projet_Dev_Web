<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;

class DashboardController extends Controller
{
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

    public function profilPublic(int $id)
    {
        $user = Utilisateur::with(['etudiant','tuteur','jury','entreprise'])->findOrFail($id);

        return view('profil_public', compact('user'));
    }
}
