<?php
session_start();
require_once 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/login.html');
    exit;
}

if (!isset($_SESSION['user_id_temp'])) {
    header('Location: ../frontend/pages/login.html?erreur=session');
    exit;
}

$code_saisi    = trim($_POST['code_2fa'] ?? '');
$id_utilisateur = $_SESSION['user_id_temp'];

if (strlen($code_saisi) !== 6) {
    header('Location: ../frontend/pages/verify_2fa.html?erreur=code');
    exit;
}

// Vérification du code
$stmt = $pdo->prepare("
    SELECT * FROM AUTH_CODE
    WHERE id_utilisateur = ?
    AND code = ?
    AND utilise = 0
    AND date_expiration > NOW()
");
$stmt->execute([$id_utilisateur, $code_saisi]);
$auth = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auth) {
    header('Location: ../frontend/pages/verify_2fa.html?erreur=code');
    exit;
}

// Marque le code comme utilisé
$stmt = $pdo->prepare("UPDATE AUTH_CODE SET utilise = 1 WHERE id = ?");
$stmt->execute([$auth['id']]);

// Connexion validée — on crée la vraie session
$_SESSION['user_id'] = $id_utilisateur;
$_SESSION['role']    = $_SESSION['role_temp'];

unset($_SESSION['user_id_temp']);
unset($_SESSION['role_temp']);
unset($_SESSION['email_2fa']);

// Redirection selon le rôle
$redirections = [
    'etudiant'   => '../frontend/pages/dashboard_etudiant.php',
    'entreprise' => '../frontend/pages/dashboard_entreprise.php',
    'tuteur'     => '../frontend/pages/dashboard_tuteur.php',
    'jury'       => '../frontend/pages/dashboard_tuteur.php',
    'admin'      => '../frontend/pages/dashboard_admin.php',
];

$destination = $redirections[$_SESSION['role']] ?? '../frontend/pages/login.html';
header('Location: ' . $destination);
exit;
?>
