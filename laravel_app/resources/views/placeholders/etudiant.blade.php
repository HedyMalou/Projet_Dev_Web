<!DOCTYPE html><html><body style="font-family:sans-serif;padding:40px;background:#f0f4f8">
<h2>✅ Connecté en tant qu'Étudiant</h2>
<p>Bonjour {{ session('prenom') }} {{ session('nom') }} — rôle : <strong>{{ session('role') }}</strong></p>
<p style="color:#6b7280">Dashboard étudiant — sera implémenté à l'Étape 4.</p>
<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" style="padding:8px 16px;background:#1a3a5c;color:white;border:none;border-radius:6px;cursor:pointer">Déconnexion</button></form>
</body></html>
