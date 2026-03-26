<?php
$host     = 'localhost';
$dbname   = 'stages_cytech';
$user     = 'hedy';
$password = 'hedy123456789';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET time_zone = '+01:00'");
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
