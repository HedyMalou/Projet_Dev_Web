<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../frontend/pages/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/dashboard_admin.php');
    exit;
}

$id_candidature = isset($_POST['id_candidature']) ? (int)$_POST['id_candidature'] : 0;

if (!$id_candidature) {
    header('Location: ../frontend/pages/dashboard_admin.php?erreur=donnees');
    exit;
}

// On ne peut archiver que les stages déjà validés
$stmt = $pdo->prepare("SELECT id FROM CANDIDATURE WHERE id = ? AND statut = 'validee'");
$stmt->execute([$id_candidature]);
if (!$stmt->fetch()) {
    header('Location: ../frontend/pages/dashboard_admin.php?erreur=statut');
    exit;
}

$stmt = $pdo->prepare("UPDATE CANDIDATURE SET statut = 'archivee' WHERE id = ?");
$stmt->execute([$id_candidature]);

header('Location: ../frontend/pages/dashboard_admin.php?ok=archive');
exit;
?>
