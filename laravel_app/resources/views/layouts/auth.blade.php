<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Plateforme Stages') — CY Tech</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <style>
    :root { --bleu:#1a3a5c; --bleu-clair:#2d6a9f; --bordure:#d1dce8; --texte:#1c1c1c; --gris:#6b7280; --bg:#f0f4f8; }
    body { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--texte); min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .auth-card { background:white; border-radius:16px; padding:40px; border:0.5px solid var(--bordure); width:100%; max-width:440px; box-shadow:0 4px 24px rgba(26,58,92,0.08); }
    .logo { display:flex; align-items:center; gap:10px; margin-bottom:28px; justify-content:center; }
    .logo-carre { width:38px; height:38px; background:var(--bleu); border-radius:8px; display:flex; align-items:center; justify-content:center; font-family:'DM Serif Display',serif; font-size:16px; color:white; font-weight:700; }
    .logo-texte { font-size:14px; font-weight:600; color:var(--bleu); }
    .auth-titre { font-family:'DM Serif Display',serif; font-size:22px; text-align:center; margin-bottom:6px; }
    .auth-sous-titre { font-size:13px; color:var(--gris); text-align:center; margin-bottom:24px; }
    .form-label { font-size:12px; font-weight:500; color:var(--gris); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:5px; }
    .form-control, .form-select { border:1.5px solid var(--bordure); border-radius:8px; padding:10px 14px; font-size:14px; font-family:'DM Sans',sans-serif; background:#fafcff; transition:border-color 0.15s; }
    .form-control:focus, .form-select:focus { border-color:var(--bleu-clair); box-shadow:0 0 0 3px rgba(45,106,159,0.1); outline:none; }
    .btn-auth { width:100%; padding:11px; background:var(--bleu); color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; transition:background 0.15s; margin-top:8px; }
    .btn-auth:hover { background:var(--bleu-clair); }
    .auth-link { font-size:13px; color:var(--gris); text-align:center; margin-top:16px; }
    .auth-link a { color:var(--bleu-clair); text-decoration:none; font-weight:500; }
    .auth-link a:hover { text-decoration:underline; }
    .alerte-succes { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; font-size:13px; color:#16a34a; margin-bottom:16px; }
    .alerte-erreur { background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:10px 14px; font-size:13px; color:#dc2626; margin-bottom:16px; }
    .debug-box { background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:10px 14px; font-size:13px; color:#92400e; margin-bottom:16px; text-align:center; }
    .separateur { border:none; border-top:1px solid var(--bordure); margin:20px 0; }
  </style>
</head>
<body>
  <div class="auth-card">
    <div class="logo">
      <div class="logo-carre">CY</div>
      <span class="logo-texte">Plateforme Stages</span>
    </div>
    @yield('content')
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')
</body>
</html>
