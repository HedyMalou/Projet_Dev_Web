<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: ../frontend/pages/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/dashboard_etudiant.php');
    exit;
}

$id_candidature = isset($_POST['id_candidature']) ? (int)$_POST['id_candidature'] : 0;
$type = trim($_POST['type'] ?? '');
$types_autorisés = ['rapport', 'resume', 'fiche_evaluation', 'convention', 'autre'];

if (!$id_candidature || !in_array($type, $types_autorisés)) {
    header('Location: ../frontend/pages/mon_dossier.php?erreur=donnees');
    exit;
}

// Vérifier que la candidature appartient bien à cet étudiant
$stmt = $pdo->prepare("
    SELECT c.id FROM CANDIDATURE c
    JOIN ETUDIANT e ON e.id = c.id_etudiant
    WHERE c.id = ? AND e.id_utilisateur = ?
");
$stmt->execute([$id_candidature, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header('Location: ../frontend/pages/mon_dossier.php?erreur=acces');
    exit;
}

if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../frontend/pages/mon_dossier.php?erreur=fichier');
    exit;
}

$fichier = $_FILES['fichier'];
$ext = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
$exts_autorisées = ['pdf', 'doc', 'docx'];

if (!in_array($ext, $exts_autorisées)) {
    header('Location: ../frontend/pages/mon_dossier.php?erreur=format');
    exit;
}

if ($fichier['size'] > 5 * 1024 * 1024) {
    header('Location: ../frontend/pages/mon_dossier.php?erreur=taille');
    exit;
}

$dossier_upload = __DIR__ . '/../uploads/';
if (!is_dir($dossier_upload)) {
    mkdir($dossier_upload, 0755, true);
}

$nom_fichier = 'doc_' . $id_candidature . '_' . $type . '_' . time() . '.' . $ext;
$chemin = $dossier_upload . $nom_fichier;

if (!move_uploaded_file($fichier['tmp_name'], $chemin)) {
    header('Location: ../frontend/pages/mon_dossier.php?erreur=upload');
    exit;
}

$stmt = $pdo->prepare("INSERT INTO DOCUMENT (id_candidature, type, chemin_fichier) VALUES (?, ?, ?)");
$stmt->execute([$id_candidature, $type, 'uploads/' . $nom_fichier]);

header('Location: ../frontend/pages/mon_dossier.php?ok=upload');
exit;
?>
