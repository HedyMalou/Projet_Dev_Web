<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['tuteur', 'jury'])) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$id_candidature = (int)($_POST['id_candidature'] ?? 0);
$contenu        = trim($_POST['contenu'] ?? '');

if (!$id_candidature || !$contenu) {
    header('Location: ../frontend/pages/dashboard_tuteur.php');
    exit;
}

$stmt = $pdo->prepare("INSERT INTO COMMENTAIRE (id_candidature, id_utilisateur, contenu) VALUES (?, ?, ?)");
$stmt->execute([$id_candidature, $_SESSION['user_id'], $contenu]);

header('Location: ../frontend/pages/dashboard_tuteur.php?ok=commentaire');
exit;
?>
