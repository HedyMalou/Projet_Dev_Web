<?php
// Démarre la session
session_start();

// Connexion BDD
require_once 'connexion.php';

// Vérifie la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $role = $_POST['role'] ?? '';

    // Vérifie les champs vides
    if (empty($email) || empty($mot_de_passe) || empty($role)) {
        die("Veuillez remplir tous les champs.");
    }

    try {
        // Recherche utilisateur
        $stmt = $pdo->prepare("SELECT id, nom, prenom, mot_de_passe FROM UTILISATEUR WHERE email = ? AND role = ?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifie le mdp hashé bcrypt
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            
            // Génère code A2F 6 chiffres
            $code_2fa = sprintf("%06d", mt_rand(0, 999999));
            $date_exp = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Enregistre le code dans AUTH_CODE
            $insertCode = $pdo->prepare("INSERT INTO AUTH_CODE (id_utilisateur, code, date_expiration) VALUES (?, ?, ?)");
            $insertCode->execute([$user['id'], $code_2fa, $date_exp]);

            // Stocke infos temporaires en session
            $_SESSION['auth_temp'] = [
                'id_utilisateur' => $user['id'],
                'role' => $role,
                'email' => $email
            ];

            // Redirige vers A2F
            header("Location: ../frontend/pages/verify_2fa.html");
            exit();

        } else {
            // On renvoie sur la page de connexion avec erreur
            header("Location: ../frontend/pages/login.html?erreur=identifiants");
            exit();
        }
    } catch (PDOException $e) {
        die("Erreur BDD : " . $e->getMessage());
    }
} else {
    // On renvoie vers le login si accès direct
    header("Location: ../frontend/pages/login.html");
    exit();
}
?>
