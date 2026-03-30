<?php
require_once 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$email = trim($_POST['email'] ?? '');
$mdp   = trim($_POST['mot_de_passe'] ?? '');
$role  = trim($_POST['role'] ?? '');

if (!$email || !$mdp || !$role) {
    header('Location: ../frontend/pages/login.html?erreur=champs');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM UTILISATEUR WHERE email = ? AND role = ?");
$stmt->execute([$email, $role]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($mdp, $user['mot_de_passe'])) {
    header('Location: ../frontend/pages/login.html?erreur=identifiants');
    exit;
}

$code = strval(rand(100000, 999999));

// On utilise directement MySQL pour générer la date d'expiration
// comme ça c'est cohérent avec NOW() dans la vérification
$stmt = $pdo->prepare("DELETE FROM AUTH_CODE WHERE id_utilisateur = ?");
$stmt->execute([$user['id']]);

$stmt = $pdo->prepare("INSERT INTO AUTH_CODE (id_utilisateur, code, date_expiration) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
$stmt->execute([$user['id'], $code]);

mail($user['email'], "Code connexion - Plateforme Stages",
    "Bonjour " . $user['prenom'] . ",\n\nVotre code : " . $code . "\n\nValable 10 minutes.");

$uid = $user['id'];
$r   = urlencode($user['role']);
$e   = urlencode($user['email']);
header("Location: ../frontend/pages/verify_2fa.php?uid=$uid&role=$r&email=$e");
exit;
?>
