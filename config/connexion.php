<?php
$serveur="localhost";
$baseDedonne="porfolio";
$utilisateur="root";
$motDePasse="";
try {
    $pdo = new PDO("mysql:host=$serveur;dbname=$baseDedonne;charset=utf8mb4", $utilisateur, $motDePasse);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Une erreur est survenue lors de la connexion à la base de données.");}
?>