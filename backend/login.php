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
$expiration = date('Y-m-d H:i:s', time() + 600);

$stmt = $pdo->prepare("DELETE FROM AUTH_CODE WHERE id_utilisateur = ?");
$stmt->execute([$user['id']]);

$stmt = $pdo->prepare("INSERT INTO AUTH_CODE (id_utilisateur, code, date_expiration) VALUES (?, ?, ?)");
$stmt->execute([$user['id'], $code, $expiration]);

mail($user['email'], "Code connexion - Plateforme Stages",
    "Bonjour " . $user['prenom'] . ",\n\nVotre code : " . $code . "\n\nValable 10 minutes.");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Redirection...</title>
</head>
<body>
<form id="f" action="../frontend/pages/verify_2fa.php" method="POST">
  <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
  <input type="hidden" name="role"    value="<?= htmlspecialchars($user['role']) ?>">
  <input type="hidden" name="email"   value="<?= htmlspecialchars($user['email']) ?>">
</form>
<script>document.getElementById('f').submit();</script>
</body>
</html>
