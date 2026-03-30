<?php
session_start();
require_once 'connexion.php';

// Entreprise ou admin peuvent supprimer une offre
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['entreprise', 'admin'])) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/dashboard_entreprise.php');
    exit;
}

$id_offre = isset($_POST['id_offre']) ? (int)$_POST['id_offre'] : 0;

if (!$id_offre) {
    header('Location: ../frontend/pages/dashboard_entreprise.php?erreur=donnees');
    exit;
}

// Une entreprise ne peut supprimer que ses propres offres
if ($_SESSION['role'] === 'entreprise') {
    $stmt = $pdo->prepare("
        SELECT o.id FROM OFFRE_STAGE o
        JOIN ENTREPRISE e ON e.id = o.id_entreprise
        WHERE o.id = ? AND e.id_utilisateur = ?
    ");
    $stmt->execute([$id_offre, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        header('Location: ../frontend/pages/dashboard_entreprise.php?erreur=acces');
        exit;
    }
    $redirect = '../frontend/pages/dashboard_entreprise.php';
}

// L'admin peut supprimer n'importe quelle offre
if ($_SESSION['role'] === 'admin') {
    $stmt = $pdo->prepare("SELECT id FROM OFFRE_STAGE WHERE id = ?");
    $stmt->execute([$id_offre]);
    if (!$stmt->fetch()) {
        header('Location: ../frontend/pages/dashboard_admin.php?erreur=offre');
        exit;
    }
    $redirect = '../frontend/pages/dashboard_admin.php';
}

$stmt = $pdo->prepare("DELETE FROM OFFRE_STAGE WHERE id = ?");
$stmt->execute([$id_offre]);

header("Location: $redirect?ok=suppression");
exit;
?>
