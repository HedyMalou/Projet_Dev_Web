@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
  <div class="auth-titre">Mot de passe oublié</div>
  <div class="auth-sous-titre">Contactez votre administrateur à l'adresse <strong>admin@cytech.fr</strong> pour réinitialiser votre mot de passe.</div>

  <div style="background:#e8f0f7;border-radius:8px;padding:16px;font-size:13px;color:var(--bleu);text-align:center;margin-bottom:20px;">
    La réinitialisation automatique par email sera disponible prochainement.
  </div>

  <div class="auth-link">
    <a href="{{ route('login') }}">← Retour à la connexion</a>
  </div>
@endsection
