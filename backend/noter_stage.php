<?php
session_start();
require_once 'connexion.php';

// Tuteur et jury peuvent noter un stage
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['tuteur', 'jury'])) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/dashboard_tuteur.php');
    exit;
}

$id_candidature = isset($_POST['id_candidature']) ? (int)$_POST['id_candidature'] : 0;
$note = isset($_POST['note']) ? floatval($_POST['note']) : -1;

if (!$id_candidature || $note < 0 || $note > 20) {
    header('Location: ../frontend/pages/dashboard_tuteur.php?erreur=donnees');
    exit;
}

// Vérifier que le tuteur est bien affecté à cette candidature
if ($_SESSION['role'] === 'tuteur') {
    $stmt = $pdo->prepare("
        SELECT s.id FROM SUIVI s
        JOIN TUTEUR t ON t.id = s.id_tuteur
        WHERE s.id_candidature = ? AND t.id_utilisateur = ?
    ");
    $stmt->execute([$id_candidature, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        header('Location: ../frontend/pages/dashboard_tuteur.php?erreur=acces');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE SUIVI SET note_finale = ? WHERE id_candidature = ?");
    $stmt->execute([$note, $id_candidature]);
}

// Le jury peut noter n'importe quel stage validé
if ($_SESSION['role'] === 'jury') {
    $stmt = $pdo->prepare("SELECT id FROM CANDIDATURE WHERE id = ? AND statut = 'validee'");
    $stmt->execute([$id_candidature]);
    if (!$stmt->fetch()) {
        header('Location: ../frontend/pages/dashboard_tuteur.php?erreur=acces');
        exit;
    }

    // Le jury met à jour la note dans SUIVI si elle existe
    $stmt = $pdo->prepare("SELECT id FROM SUIVI WHERE id_candidature = ?");
    $stmt->execute([$id_candidature]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE SUIVI SET note_finale = ? WHERE id_candidature = ?");
        $stmt->execute([$note, $id_candidature]);
    }
}

header('Location: ../frontend/pages/dashboard_tuteur.php?ok=note');
exit;
?>
