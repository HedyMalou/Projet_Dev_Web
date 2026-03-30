<?php
session_start();
require_once 'connexion.php';

// Seul l'admin peut affecter un tuteur à une candidature
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../frontend/pages/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/dashboard_admin.php');
    exit;
}

$id_candidature = isset($_POST['id_candidature']) ? (int)$_POST['id_candidature'] : 0;
$id_tuteur      = isset($_POST['id_tuteur'])      ? (int)$_POST['id_tuteur']      : 0;

if (!$id_candidature || !$id_tuteur) {
    header('Location: ../frontend/pages/dashboard_admin.php?erreur=donnees');
    exit;
}

// Vérifier que la candidature existe et est validée
$stmt = $pdo->prepare("SELECT id FROM CANDIDATURE WHERE id = ? AND statut = 'validee'");
$stmt->execute([$id_candidature]);
if (!$stmt->fetch()) {
    header('Location: ../frontend/pages/dashboard_admin.php?erreur=candidature');
    exit;
}

// Vérifier que le tuteur existe
$stmt = $pdo->prepare("SELECT id FROM TUTEUR WHERE id = ?");
$stmt->execute([$id_tuteur]);
if (!$stmt->fetch()) {
    header('Location: ../frontend/pages/dashboard_admin.php?erreur=tuteur');
    exit;
}

// Vérifier si un suivi existe déjà pour cette candidature
$stmt = $pdo->prepare("SELECT id FROM SUIVI WHERE id_candidature = ?");
$stmt->execute([$id_candidature]);
$suivi = $stmt->fetch();

if ($suivi) {
    // Mettre à jour le tuteur affecté
    $stmt = $pdo->prepare("UPDATE SUIVI SET id_tuteur = ? WHERE id_candidature = ?");
    $stmt->execute([$id_tuteur, $id_candidature]);
} else {
    // Créer le suivi
    $stmt = $pdo->prepare("INSERT INTO SUIVI (id_tuteur, id_candidature) VALUES (?, ?)");
    $stmt->execute([$id_tuteur, $id_candidature]);
}

header('Location: ../frontend/pages/dashboard_admin.php?ok=affectation');
exit;
?>
