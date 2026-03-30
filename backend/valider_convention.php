<?php
session_start();
require_once 'connexion.php';

$roles_autorises = ['etudiant', 'entreprise', 'tuteur'];
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], $roles_autorises)) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$id_convention = isset($_POST['id_convention']) ? (int)$_POST['id_convention'] : 0;
$role = $_SESSION['role'];

if (!$id_convention) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

// Vérifier que l'utilisateur a le droit de signer cette convention
$stmt = $pdo->prepare("
    SELECT conv.id, conv.id_candidature, conv.statut_etudiant, conv.statut_entreprise, conv.statut_tuteur
    FROM CONVENTION conv
    JOIN CANDIDATURE c ON c.id = conv.id_candidature
    WHERE conv.id = ?
");
$stmt->execute([$id_convention]);
$convention = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$convention) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$id_candidature = $convention['id_candidature'];
$autorise = false;

if ($role === 'etudiant') {
    $stmt = $pdo->prepare("
        SELECT 1 FROM CANDIDATURE c
        JOIN ETUDIANT e ON e.id = c.id_etudiant
        WHERE c.id = ? AND e.id_utilisateur = ?
    ");
    $stmt->execute([$id_candidature, $_SESSION['user_id']]);
    $autorise = (bool)$stmt->fetch();
    $champ = 'statut_etudiant';
    $redirect = '../frontend/pages/mon_dossier.php';
}

if ($role === 'entreprise') {
    $stmt = $pdo->prepare("
        SELECT 1 FROM CANDIDATURE c
        JOIN OFFRE_STAGE o ON o.id = c.id_offre
        JOIN ENTREPRISE e ON e.id = o.id_entreprise
        WHERE c.id = ? AND e.id_utilisateur = ?
    ");
    $stmt->execute([$id_candidature, $_SESSION['user_id']]);
    $autorise = (bool)$stmt->fetch();
    $champ = 'statut_entreprise';
    $redirect = '../frontend/pages/dashboard_entreprise.php';
}

if ($role === 'tuteur') {
    $stmt = $pdo->prepare("
        SELECT 1 FROM SUIVI s
        JOIN TUTEUR t ON t.id = s.id_tuteur
        WHERE s.id_candidature = ? AND t.id_utilisateur = ?
    ");
    $stmt->execute([$id_candidature, $_SESSION['user_id']]);
    $autorise = (bool)$stmt->fetch();
    $champ = 'statut_tuteur';
    $redirect = '../frontend/pages/dashboard_tuteur.php';
}

if (!$autorise) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$stmt = $pdo->prepare("UPDATE CONVENTION SET $champ = 'signe' WHERE id = ?");
$stmt->execute([$id_convention]);

header("Location: $redirect?ok=convention");
exit;
?>
