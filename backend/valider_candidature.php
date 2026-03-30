<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header('Location: ../frontend/pages/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/dashboard_entreprise.php');
    exit;
}

$id_candidature = isset($_POST['id_candidature']) ? (int)$_POST['id_candidature'] : 0;
$nouveau_statut = trim($_POST['statut'] ?? '');

if (!$id_candidature || !in_array($nouveau_statut, ['validee', 'refusee'])) {
    header('Location: ../frontend/pages/dashboard_entreprise.php?erreur=donnees');
    exit;
}

// Vérifier que la candidature appartient bien à une offre de cette entreprise
$stmt = $pdo->prepare("
    SELECT c.id FROM CANDIDATURE c
    JOIN OFFRE_STAGE o ON o.id = c.id_offre
    JOIN ENTREPRISE e ON e.id = o.id_entreprise
    WHERE c.id = ? AND e.id_utilisateur = ?
");
$stmt->execute([$id_candidature, $_SESSION['user_id']]);
$candidature = $stmt->fetch();

if (!$candidature) {
    header('Location: ../frontend/pages/dashboard_entreprise.php?erreur=acces');
    exit;
}

// Mettre à jour le statut
$stmt = $pdo->prepare("UPDATE CANDIDATURE SET statut = ? WHERE id = ?");
$stmt->execute([$nouveau_statut, $id_candidature]);

// Si validée, créer une convention (si elle n'existe pas déjà)
if ($nouveau_statut === 'validee') {
    $stmt = $pdo->prepare("SELECT id FROM CONVENTION WHERE id_candidature = ?");
    $stmt->execute([$id_candidature]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO CONVENTION (id_candidature) VALUES (?)");
        $stmt->execute([$id_candidature]);
    }
}

header('Location: ../frontend/pages/dashboard_entreprise.php?ok=statut');
exit;
?>
