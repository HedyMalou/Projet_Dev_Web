<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $__env->yieldContent('title', 'Plateforme Stages'); ?> — CY Tech</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <style>
    :root { --bleu:#1a3a5c; --bleu-clair:#2d6a9f; --bordure:#d1dce8; --texte:#1c1c1c; --gris:#6b7280; --bg:#f0f4f8; }
    * { box-sizing:border-box; }
    body { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--texte); margin:0; }
    .sidebar { width:240px; min-height:100vh; background:var(--bleu); position:fixed; top:0; left:0; display:flex; flex-direction:column; padding:24px 0; z-index:100; }
    .sidebar-logo { display:flex; align-items:center; gap:10px; padding:0 24px 28px; border-bottom:1px solid rgba(255,255,255,0.1); margin-bottom:16px; }
    .logo-carre { width:34px; height:34px; background:white; border-radius:7px; display:flex; align-items:center; justify-content:center; font-family:'DM Serif Display',serif; font-size:15px; color:var(--bleu); font-weight:700; }
    .logo-texte { font-size:13px; font-weight:600; color:rgba(255,255,255,0.9); }
    .nav-item { display:flex; align-items:center; gap:12px; padding:11px 24px; color:rgba(255,255,255,0.65); text-decoration:none; font-size:14px; font-weight:500; transition:all 0.15s; border-left:3px solid transparent; }
    .nav-item:hover { background:rgba(255,255,255,0.07); color:white; }
    .nav-item.active { background:rgba(255,255,255,0.1); color:white; border-left-color:white; }
    .nav-icon { width:18px; height:18px; flex-shrink:0; }
    .sidebar-footer { margin-top:auto; padding:16px 24px; border-top:1px solid rgba(255,255,255,0.1); }
    .user-info { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
    .avatar { width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; color:white; flex-shrink:0; }
    .user-nom { font-size:13px; font-weight:600; color:white; }
    .user-role { font-size:11px; color:rgba(255,255,255,0.5); }
    .btn-deconnexion { width:100%; padding:8px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); border-radius:6px; color:rgba(255,255,255,0.7); font-size:13px; cursor:pointer; text-align:center; text-decoration:none; display:block; font-family:'DM Sans',sans-serif; }
    .btn-deconnexion:hover { background:rgba(255,255,255,0.14); color:white; }
    .main { margin-left:240px; padding:32px; min-height:100vh; }
    .page-header { margin-bottom:28px; }
    .page-header h1 { font-family:'DM Serif Display',serif; font-size:26px; margin-bottom:4px; }
    .page-header p { font-size:14px; color:var(--gris); margin:0; }
    .kpi-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:28px; }
    .kpi-grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
    .kpi-grid-2 { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-bottom:28px; }
    .kpi-card { background:white; border-radius:12px; padding:20px 24px; border:0.5px solid var(--bordure); }
    .kpi-label { font-size:12px; color:var(--gris); font-weight:500; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.05em; }
    .kpi-valeur { font-size:28px; font-weight:600; margin-bottom:4px; }
    .kpi-sous { font-size:12px; color:var(--gris); }
    .section-card { background:white; border-radius:12px; padding:24px; border:0.5px solid var(--bordure); margin-bottom:24px; }
    .section-titre { font-size:15px; font-weight:600; margin-bottom:16px; }
    .form-label-sm { font-size:12px; font-weight:500; color:var(--gris); margin-bottom:5px; display:block; }
    .form-control, .form-select { border:1.5px solid var(--bordure); border-radius:8px; padding:9px 12px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--texte); background:#fafcff; width:100%; }
    .form-control:focus, .form-select:focus { border-color:var(--bleu-clair); box-shadow:0 0 0 3px rgba(45,106,159,0.1); outline:none; }
    table { width:100%; border-collapse:collapse; font-size:13px; }
    thead th { padding:10px 12px; background:var(--bg); color:var(--gris); font-weight:500; font-size:12px; text-align:left; border-bottom:1px solid var(--bordure); }
    tbody td { padding:12px; border-bottom:0.5px solid var(--bordure); vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fafcff; }
    .badge-attente { font-size:11px; padding:3px 10px; border-radius:20px; background:#fef9c3; color:#a16207; font-weight:500; }
    .badge-validee { font-size:11px; padding:3px 10px; border-radius:20px; background:#f0fdf4; color:#16a34a; font-weight:500; }
    .badge-refusee { font-size:11px; padding:3px 10px; border-radius:20px; background:#fef2f2; color:#dc2626; font-weight:500; }
    .badge-archive { font-size:11px; padding:3px 10px; border-radius:20px; background:#f3f4f6; color:#6b7280; font-weight:500; }
    .badge-role { font-size:11px; padding:3px 10px; border-radius:20px; background:#e8f0f7; color:var(--bleu-clair); font-weight:500; }
    .badge-admin { background:#fef9c3; color:#a16207; }
    .badge-note { font-size:11px; padding:3px 10px; border-radius:20px; background:#e8f0f7; color:var(--bleu-clair); font-weight:500; }
    .btn-action { font-size:12px; padding:5px 12px; border-radius:6px; border:1.5px solid var(--bleu); color:var(--bleu); background:white; font-family:'DM Sans',sans-serif; font-weight:500; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-action:hover { background:var(--bleu); color:white; }
    .btn-supprimer { font-size:12px; padding:5px 12px; border-radius:6px; border:1.5px solid #dc2626; color:#dc2626; background:white; font-family:'DM Sans',sans-serif; font-weight:500; cursor:pointer; }
    .btn-supprimer:hover { background:#dc2626; color:white; }
    .btn-valider { padding:9px 20px; background:var(--bleu); color:white; border:none; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-valider:hover { background:var(--bleu-clair); }
    .alerte-succes { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; font-size:13px; color:#16a34a; margin-bottom:20px; }
    .alerte-erreur { background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:10px 14px; font-size:13px; color:#dc2626; margin-bottom:20px; }
    .vide { text-align:center; padding:32px; color:var(--gris); font-size:14px; }
    @media (max-width:992px) { .sidebar { display:none; } .main { margin-left:0; padding:20px; } .kpi-grid-3,.kpi-grid-4 { grid-template-columns:1fr 1fr; } .kpi-grid-2 { grid-template-columns:1fr; } }
    <?php echo $__env->yieldContent('styles'); ?>
  </style>
</head>
<body>

<nav class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-carre">CY</div>
    <span class="logo-texte">Plateforme Stages</span>
  </div>

  <?php echo $__env->yieldContent('nav'); ?>

  <div class="sidebar-footer">
    <div class="user-info">
      <div class="avatar"><?php echo e(strtoupper(substr(session('prenom','?'),0,1).substr(session('nom','?'),0,1))); ?></div>
      <div>
        <div class="user-nom"><?php echo e(session('prenom')); ?> <?php echo e(session('nom')); ?></div>
        <div class="user-role"><?php echo $__env->yieldContent('role-label'); ?></div>
      </div>
    </div>
    <form method="POST" action="<?php echo e(route('logout')); ?>">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn-deconnexion">Déconnexion</button>
    </form>
  </div>
</nav>

<main class="main">
  <?php echo $__env->yieldContent('content'); ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/cytech/Projet_Dev_Web/laravel_app/resources/views/layouts/app.blade.php ENDPATH**/ ?>