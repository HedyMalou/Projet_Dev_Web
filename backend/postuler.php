<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$id_offre = (int)($_POST['id_offre'] ?? 0);

if (!$id_offre) {
    header('Location: ../frontend/pages/dashboard_etudiant.php');
    exit;
}

// Récupère l'id étudiant
$stmt = $pdo->prepare("SELECT id FROM ETUDIANT WHERE id_utilisateur = ?");
$stmt->execute([$_SESSION['user_id']]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    header('Location: ../frontend/pages/dashboard_etudiant.php');
    exit;
}

// Vérifie qu'il n'a pas déjà postulé
$stmt = $pdo->prepare("SELECT id FROM CANDIDATURE WHERE id_etudiant = ? AND id_offre = ?");
$stmt->execute([$etudiant['id'], $id_offre]);

if ($stmt->fetch()) {
    header('Location: ../frontend/pages/dashboard_etudiant.php?erreur=deja_postule');
    exit;
}

// Crée la candidature
$stmt = $pdo->prepare("INSERT INTO CANDIDATURE (id_etudiant, id_offre) VALUES (?, ?)");
$stmt->execute([$etudiant['id'], $id_offre]);

header('Location: ../frontend/pages/dashboard_etudiant.php?ok=postule');
exit;
?>
