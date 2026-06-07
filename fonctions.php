<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarre la session de manière sécurisée si ce n'est pas déjà fait
}

/**
 * Règle XSS : Échappe les données avant l'affichage dans le code HTML
 */
function e(string $valeur): string {
    return htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8');
}

/**
 * Règle CSRF : Génère un jeton unique et sécurisé
 */
function generer_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Règle CSRF : Vérifie le jeton soumis avec hash_equals() pour éviter les attaques temporelles
 */
function verifier_csrf(string $token): bool {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Règle de Journalisation : Enregistre l'activité sur les pages publiques
 */
function journaliser_visite(PDO $pdo, string $page): void {
    // Gestion de l'adresse IP (Prend en compte les reverse-proxies ou CDN éventuels)
    $adresse_ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $adresse_ip = trim($ips[0]);
    }

    $sql = "INSERT INTO visites (adresse_ip, page) VALUES (:ip, :page)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ip' => $adresse_ip,
        ':page' => $page
    ]);
}
?>