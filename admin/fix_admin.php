<?php
require_once __DIR__ . '/../config/connexion.php';

$email_test = "el.hadji.ahmadou.cherif.diouf@gmail.com"; 
$password_clair = "Passer1234"; 
$password_hache = password_hash($password_clair, PASSWORD_BCRYPT);

$message = "";

try {
    $stmt = $pdo->prepare("SELECT id FROM administrateurs WHERE email = :email");
    $stmt->execute([':email' => $email_test]);
    $admin = $stmt->fetch();

    if ($admin) {
        $update = $pdo->prepare("UPDATE administrateurs SET mot_de_passe = :mdp WHERE email = :email");
        $update->execute([':mdp' => $password_hache, ':email' => $email_test]);
        $message = "<h3>[Succès] Mot de passe réinitialisé !</h3>";
    } else {
        $insert = $pdo->prepare("INSERT INTO administrateurs (prenom, email, mot_de_passe) VALUES ('Khadidiatou', :email, :mdp)");
        $insert->execute([':email' => $email_test, ':mdp' => $password_hache]);
        $message = "<h3>[Succès] Nouveau compte créé !</h3>";
    }
} catch (Exception $e) {
    $message = "<h3 style='color:red;'>[Erreur] :</h3> " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fix Admin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #fcfaf7; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            color: #4a3728; 
        }
        .container { 
            background: #ffffff; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(74, 55, 40, 0.08); 
            text-align: center; 
            max-width: 450px; 
            width: 90%;
            border: 1px solid #eee7de;
        }
        h3 { color: #8c6239; margin-bottom: 20px; }
        .data-box { 
            background: #fdfaf7; 
            padding: 20px; 
            border-radius: 12px; 
            margin: 25px 0; 
            border: 1px dashed #dcd0c0;
            text-align: left;
        }
        .btn { 
            display: inline-block; 
            padding: 12px 25px; 
            background: #4a3728; 
            color: #ffffff; 
            text-decoration: none; 
            border-radius: 10px; 
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover { background: #8c6239; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $message; ?>
        <div class="data-box">
            <p><strong>Email :</strong> <?php echo htmlspecialchars($email_test); ?></p>
            <p><strong>Mot de passe :</strong> <?php echo htmlspecialchars($password_clair); ?></p>
        </div>
        <a href="connexion.php" class="btn">Retour à la connexion</a>
    </div>
</body>
</html>
