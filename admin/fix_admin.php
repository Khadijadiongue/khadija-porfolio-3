<?php
require_once __DIR__ . '/../config/connexion.php';
$email_test = "el.hadji.ahmadou.cherif.diouf@gmail.com"; 
$password_clair = "Passer1234"; 
$password_hache = password_hash($password_clair, PASSWORD_BCRYPT);
password_verify(
$password_clair,
$password_hache
);

try {
    $stmt = $pdo->prepare("SELECT id FROM administrateurs WHERE email = :email");
    $stmt->execute([':email' => $email_test]);
    $admin = $stmt->fetch();

    if ($admin) {
        $update = $pdo->prepare("UPDATE administrateurs SET mot_de_passe = :mdp WHERE email = :email");
        $update->execute([
            ':mdp' => $password_hache,
            ':email' => $email_test
        ]);
        echo "<h3>[Succès] Le mot de passe du compte existant a été réinitialisé !</h3>";
    } else {
        $insert = $pdo->prepare("INSERT INTO administrateurs (prenom, email, mot_de_passe) VALUES ('Khadidiatou', :email, :mdp)");
        $insert->execute([
            ':email' => $email_test,
            ':mdp' => $password_hache
        ]);
        echo "<h3>[Succès] Un nouveau compte administrateur a été créé !</h3>";
    }

    echo "<p><strong>Email à utiliser :</strong> " . htmlspecialchars($email_test) . "</p>";
    echo "<p><strong>Mot de passe à taper :</strong> " . htmlspecialchars($password_clair) . "</p>";
    echo "<br><a href='connexion.php'>Retourner à la page de connexion</a>";

} catch (Exception $e) {
    echo "<h3>[Erreur] Impossible de mettre à jour la base de données :</h3> " . $e->getMessage();
}
?>
