<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/connexion.php';
require_once __DIR__ . '/../fonctions.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch();

    if ($admin) {
        if ($password === $admin['mot_de_passe'] || password_verify($password, $admin['mot_de_passe'])) {
            
            session_regenerate_id(true); 
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_prenom'] = $admin['prenom'];
            
            header('Location: dashboard.php');
            exit();
        } else {
            $erreur = "Le compte existe, mais le mot de passe ne correspond pas. En base il y a : " . substr($admin['mot_de_passe'], 0, 15) . "...";
        }
    } else {
        $erreur = "Aucun utilisateur trouvé avec l'email : " . htmlspecialchars($email);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Espace Admin | Khadidiatou Diongue</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #fcfaf7; /* Fond beige très clair et doux */
            color: #4a3728; /* Texte marron foncé */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: #ffffff; /* Carte blanche pour le contraste */
            padding: 40px;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(74, 55, 40, 0.08); /* Ombre douce teintée marron */
            border: 1px solid #eee7de; /* Bordure subtile beige */
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header .icon {
            font-size: 2.8rem;
            color: #8c6239; /* Marron chaud pour l'icône principale */
            margin-bottom: 10px;
        }

        .login-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #4a3728;
        }

        .login-header p {
            font-size: 0.875rem;
            color: #8a7665; /* Beige foncé / terre pour le sous-titre */
            margin-top: 5px;
        }

        .error-box {
            background-color: #fdf2f2;
            border: 1px solid #f8b4b4;
            color: #c81e1e;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 8px;
            color: #5c4738;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #a39081; /* Icône de saisie beige intermédiaire */
            font-size: 1rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 12px 12px 44px;
            background: #faf8f5; /* Intérieur des champs beige très doux */
            border: 1px solid #ded5ca;
            border-radius: 8px;
            color: #2e2218;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8c6239; /* Focus marron */
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(140, 98, 57, 0.12); /* Halo doré/marron léger */
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #8c6239; /* Bouton marron principal */
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(140, 98, 57, 0.2);
        }

        .btn-submit:hover {
            background: #704d2b; /* Marron plus foncé au survol */
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            font-size: 0.875rem;
            color: #8a7665;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #4a3728;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <div class="icon"><i class="fa-solid fa-lock-open"></i></div>
            <h2>Espace Privé</h2>
            <p>Administration du Portfolio</p>
        </div>

        <?php if(!empty($erreur)): ?>
            <div class="error-box">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= e($erreur); ?></span>
            </div>
        <?php endif; ?>
        
        <form action="connexion.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generer_csrf(); ?>">
            
            <div class="form-group">
                <label for="email">Identifiant (Email)</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="votre.email@mail.com" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-key"></i>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••" required>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-right-to-bracket"></i> Se connecter
            </button>
        </form>

        <a href="../index.php" class="back-link"><i class="fa-solid fa-arrow-left-long"></i> Retour au site public</a>
    </div>

</body>
</html>
