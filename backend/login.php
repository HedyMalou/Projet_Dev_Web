<?php
session_start();
require_once 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$mdp      = trim($_POST['mot_de_passe'] ?? '');
$role     = trim($_POST['role'] ?? '');

if (!$email || !$mdp || !$role) {
    header('Location: ../frontend/pages/login.html?erreur=champs');
    exit;
}

// Vérification email + rôle
$stmt = $pdo->prepare("SELECT * FROM UTILISATEUR WHERE email = ? AND role = ?");
$stmt->execute([$email, $role]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($mdp, $user['mot_de_passe'])) {
    header('Location: ../frontend/pages/login.html?erreur=identifiants');
    exit;
}

// Génération du code A2F à 6 chiffres
$code = strval(rand(100000, 999999));
$expiration = date('Y-m-d H:i:s', time() + 600); // 10 minutes

// Supprime les anciens codes de cet utilisateur
$stmt = $pdo->prepare("DELETE FROM AUTH_CODE WHERE id_utilisateur = ?");
$stmt->execute([$user['id']]);

// Enregistre le nouveau code
$stmt = $pdo->prepare("INSERT INTO AUTH_CODE (id_utilisateur, code, date_expiration) VALUES (?, ?, ?)");
$stmt->execute([$user['id'], $code, $expiration]);

// Envoi du code par email
$sujet  = "Votre code de connexion - Plateforme Stages CY Tech";
$corps  = "Bonjour " . $user['prenom'] . ",\n\n";
$corps .= "Votre code de vérification est : " . $code . "\n\n";
$corps .= "Ce code est valable 10 minutes.\n\n";
$corps .= "Si vous n'êtes pas à l'origine de cette connexion, ignorez ce message.";

mail($user['email'], $sujet, $corps);

// On stocke l'id en session temporaire (pas encore connecté)
$_SESSION['user_id_temp'] = $user['id'];
$_SESSION['email_2fa']    = $user['email'];
$_SESSION['role_temp']    = $user['role'];

header('Location: ../frontend/pages/verify_2fa.html');
exit;
?>
