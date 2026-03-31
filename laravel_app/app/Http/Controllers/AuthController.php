<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Models\AuthCode;
use App\Models\Etudiant;
use App\Models\Tuteur;
use App\Models\Jury;
use App\Models\Entreprise;

class AuthController extends Controller
{
    // ── Connexion ─────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (session('user_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'        => 'required|email',
            'mot_de_passe' => 'required',
            'role'         => 'required|in:etudiant,tuteur,jury,entreprise,admin',
        ], [
            'email.required'        => "L'email est obligatoire.",
            'email.email'           => "L'email n'est pas valide.",
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
            'role.required'         => 'Veuillez sélectionner votre rôle.',
        ]);

        $user = Utilisateur::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->mot_de_passe, $user->mot_de_passe)) {
            return back()
                ->withErrors(['email' => 'Email ou mot de passe incorrect.'])
                ->withInput($request->only('email', 'role'));
        }

        // Vérification du rôle déclaré
        if ($user->role !== $request->role) {
            return back()
                ->withErrors(['email' => 'Le rôle sélectionné ne correspond pas à ce compte.'])
                ->withInput($request->only('email', 'role'));
        }

        // Vérification validation admin
        if ($user->valide == 0) {
            return back()
                ->withErrors(['email' => 'Votre compte est en attente de validation par l\'administrateur.'])
                ->withInput($request->only('email', 'role'));
        }

        // Supprimer les anciens codes A2F de cet utilisateur
        AuthCode::where('id_utilisateur', $user->id)->delete();

        // Générer un code à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        AuthCode::create([
            'id_utilisateur'  => $user->id,
            'code'            => $code,
            'date_expiration' => now()->addMinutes(10),
            'utilise'         => 0,
        ]);

        // Stocker l'ID temporairement (pas encore authentifié)
        session(['a2f_user_id' => $user->id]);

        // TODO production : envoyer le code par email
        // En développement : on l'affiche dans la vue A2F
        session(['a2f_code_debug' => $code]);

        return redirect()->route('a2f');
    }

    // ── A2F ───────────────────────────────────────────────────────────────────

    public function showA2f()
    {
        if (!session('a2f_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.a2f');
    }

    public function verifyA2f(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'Le code est obligatoire.',
            'code.digits'   => 'Le code doit contenir exactement 6 chiffres.',
        ]);

        $userId = session('a2f_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $authCode = AuthCode::where('id_utilisateur', $userId)
            ->where('code', $request->code)
            ->where('utilise', 0)
            ->where('date_expiration', '>', now())
            ->first();

        if (!$authCode) {
            return back()->withErrors(['code' => 'Code invalide ou expiré. Reconnectez-vous.']);
        }

        // Marquer le code comme utilisé
        $authCode->update(['utilise' => 1]);

        // Ouvrir la session authentifiée
        $user = Utilisateur::find($userId);

        session()->forget(['a2f_user_id', 'a2f_code_debug']);
        session([
            'user_id' => $user->id,
            'role'    => $user->role,
            'prenom'  => $user->prenom,
            'nom'     => $user->nom,
        ]);

        return redirect()->route('dashboard');
    }

    // ── Mot de passe oublié ───────────────────────────────────────────────────

    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    // ── Déconnexion ───────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }

    // ── Inscription ───────────────────────────────────────────────────────────

    public function showRegister()
    {
        if (session('user_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom'                  => 'required|string|max:100',
            'prenom'               => 'required|string|max:100',
            'email'                => 'required|email|unique:UTILISATEUR,email',
            'mot_de_passe'         => 'required|min:8|confirmed',
            'mot_de_passe_confirmation' => 'required',
            'role'                 => 'required|in:etudiant,tuteur,jury,entreprise',
        ], [
            'email.unique'              => 'Cet email est déjà utilisé.',
            'mot_de_passe.min'          => 'Le mot de passe doit contenir au moins 8 caractères.',
            'mot_de_passe.confirmed'    => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = Utilisateur::create([
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'email'        => $request->email,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'role'         => $request->role,
            'valide'       => $request->role === 'etudiant' ? 1 : 0,
        ]);

        // Créer le profil spécifique au rôle
        match ($request->role) {
            'etudiant' => Etudiant::create([
                'id_utilisateur'  => $user->id,
                'filiere'         => $request->filiere ?? 'Informatique',
                'promotion'       => $request->promotion ?? 'ING1',
                'numero_etudiant' => $request->numero_etudiant ?? 'ETU'.$user->id,
            ]),
            'tuteur' => Tuteur::create([
                'id_utilisateur' => $user->id,
                'departement'    => $request->departement ?? 'Informatique',
            ]),
            'jury' => Jury::create([
                'id_utilisateur' => $user->id,
                'specialite'     => $request->specialite ?? 'Génie Logiciel',
            ]),
            'entreprise' => Entreprise::create([
                'id_utilisateur' => $user->id,
                'nom_entreprise' => $request->nom_entreprise ?? '',
                'secteur'        => $request->secteur ?? '',
                'adresse'        => $request->adresse ?? '',
            ]),
            default => null,
        };

        $message = $request->role === 'etudiant'
            ? 'Compte créé avec succès. Vous pouvez vous connecter.'
            : 'Compte créé. Votre accès sera activé après validation par l\'administrateur.';

        return redirect()->route('login')->with('succes', $message);
    }
}
