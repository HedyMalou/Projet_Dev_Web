<?php
session_start();
require_once 'connexion.php';

$roles_autorises = ['etudiant', 'tuteur', 'jury', 'entreprise', 'admin'];
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], $roles_autorises)) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$id_document = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id_document) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

// Récupérer le document
$stmt = $pdo->prepare("SELECT * FROM DOCUMENT WHERE id = ?");
$stmt->execute([$id_document]);
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$document) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$id_candidature = $document['id_candidature'];
$role = $_SESSION['role'];
$autorise = false;

// L'étudiant peut télécharger ses propres documents
if ($role === 'etudiant') {
    $stmt = $pdo->prepare("
        SELECT 1 FROM CANDIDATURE c
        JOIN ETUDIANT e ON e.id = c.id_etudiant
        WHERE c.id = ? AND e.id_utilisateur = ?
    ");
    $stmt->execute([$id_candidature, $_SESSION['user_id']]);
    $autorise = (bool)$stmt->fetch();
}

// Le tuteur peut télécharger les documents de ses étudiants suivis
if ($role === 'tuteur') {
    $stmt = $pdo->prepare("
        SELECT 1 FROM SUIVI s
        JOIN TUTEUR t ON t.id = s.id_tuteur
        WHERE s.id_candidature = ? AND t.id_utilisateur = ?
    ");
    $stmt->execute([$id_candidature, $_SESSION['user_id']]);
    $autorise = (bool)$stmt->fetch();
}

// Le jury peut télécharger les documents des stages validés
if ($role === 'jury') {
    $stmt = $pdo->prepare("SELECT 1 FROM CANDIDATURE WHERE id = ? AND statut = 'validee'");
    $stmt->execute([$id_candidature]);
    $autorise = (bool)$stmt->fetch();
}

// L'entreprise peut télécharger les documents des candidats à ses offres
if ($role === 'entreprise') {
    $stmt = $pdo->prepare("
        SELECT 1 FROM CANDIDATURE c
        JOIN OFFRE_STAGE o ON o.id = c.id_offre
        JOIN ENTREPRISE e ON e.id = o.id_entreprise
        WHERE c.id = ? AND e.id_utilisateur = ?
    ");
    $stmt->execute([$id_candidature, $_SESSION['user_id']]);
    $autorise = (bool)$stmt->fetch();
}

// L'admin peut tout télécharger
if ($role === 'admin') {
    $autorise = true;
}

if (!$autorise) {
    header('Location: ../frontend/pages/login.html');
    exit;
}

$chemin = __DIR__ . '/../' . $document['chemin_fichier'];

if (!file_exists($chemin)) {
    http_response_code(404);
    echo "Fichier introuvable.";
    exit;
}

$nom_fichier = basename($document['chemin_fichier']);
$ext = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));

$types_mime = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
];

$mime = $types_mime[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $nom_fichier . '"');
header('Content-Length: ' . filesize($chemin));
readfile($chemin);
exit;
?>
