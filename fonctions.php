<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  
}

function e(string $valeur): string {
    return htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8');
}

function generer_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}


function verifier_csrf(string $token): bool {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
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
