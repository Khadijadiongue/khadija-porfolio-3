<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/connexion.php';
require_once __DIR__ . '/../../fonctions.php';
require_once __DIR__ . '/../verif_session.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE id = :id");
$stmt->execute([':id' => $id]); 
$admin = $stmt->fetch();

if (!$admin) { 
    die("Utilisateur introuvable."); 
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) { 
        die("Action non autorisée."); 
    }

    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';

    if (!empty($prenom) && !empty($nom) && !empty($email)) {
        // Si le mot de passe est saisi, on le hache. Sinon on conserve l'ancien (Exigence de Monsieur Diouf)
        $hash = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : $admin['mot_de_passe'];

        $stmt = $pdo->prepare("UPDATE administrateurs SET prenom = :p, nom = :n, email = :e, mot_de_passe = :m WHERE id = :id");
        $stmt->execute([':p' => $prenom, ':n' => $nom, ':e' => $email, ':m' => $hash, ':id' => $id]);
        if ($id === (int)$_SESSION['admin_id']) {
            $_SESSION['admin_prenom'] = $prenom;
        }

        header('Location: index.php'); 
        exit(); 
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Admin | Espace Privé</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #fcfaf7; /* Fond beige très clair */
            color: #4a3728; /* Texte marron foncé */
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar de navigation latérale */
        aside {
            width: 280px;
            background-color: #4a3728;
            color: #ffffff;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(74, 55, 40, 0.1);
        }

        aside h2 {
            font-size: 1.3rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            color: #f4eae1;
            border-bottom: 1px solid #634b39;
            padding-bottom: 20px;
        }

        aside nav {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        aside nav a {
            color: #ded5ca;
            text-decoration: none;
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }

        aside nav a:hover, aside nav a.active {
            background-color: #8c6239;
            color: #ffffff;
        }

        aside nav a.btn-logout {
            margin-top: 40px;
            background-color: rgba(217, 83, 79, 0.1);
            color: #f1706c;
        }

        aside nav a.btn-logout:hover {
            background-color: #d9534f;
            color: #ffffff;
        }

        /* Zone de contenu principale */
        main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .btn-back {
            background-color: #ffffff;
            color: #8c6239;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            border: 1px solid #ded5ca;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background-color: #faf8f5;
            border-color: #8c6239;
        }

        .header-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2e2218;
        }

        /* Boîte du formulaire */
        .form-container {
            background: #ffffff;
            padding: 35px;
            border-radius: 14px;
            border: 1px solid #eee7de;
            box-shadow: 0 5px 15px rgba(74, 55, 40, 0.03);
            max-width: 650px;
        }

        .error-box {
            background-color: #fdf2f2;
            border: 1px solid #f8b4b4;
            color: #c81e1e;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #5c4738;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            background: #faf8f5;
            border: 1px solid #ded5ca;
            border-radius: 8px;
            color: #2e2218;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8c6239;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(140, 98, 57, 0.1);
        }

        .help-text {
            display: block;
            font-size: 0.8rem;
            color: #8a7665;
            margin-top: 5px;
            font-style: italic;
        }

        .btn-submit {
            background-color: #8c6239;
            color: #ffffff;
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s ease;
            box-shadow: 0 4px 12px rgba(140, 98, 57, 0.15);
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #704d2b;
        }
    </style>
</head>
<body>

    <aside>
        <h2>Espace Privé</h2>
        <nav>
            <a href="../dashboard.php"><i class="fa-solid fa-chart-pie"></i> Vue d'ensemble</a>
            <a href="../projets/index.php"><i class="fa-solid fa-layer-group"></i> Gestion Projets</a>
            <a href="index.php" class="active"><i class="fa-solid fa-users-gear"></i> Gestion Admins</a>
            <a href="../messages/index.php"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="../demandes/index.php"><i class="fa-solid fa-file-signature"></i> Demandes</a>
            <a href="../deconnexion.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Déconnexion</a>
        </nav>
    </aside>

    <main>
        <header>
            <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Annuler</a>
            <div class="header-title">
                <h1>Modifier l'administrateur</h1>
            </div>
        </header>

        <div class="form-container">
            <?php if(!empty($erreur)): ?>
                <div class="error-box">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= e($erreur); ?></span>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generer_csrf(); ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" value="<?= e($admin['prenom']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" value="<?= e($admin['nom']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Adresse Email *</label>
                    <input type="email" id="email" name="email" value="<?= e($admin['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe">Nouveau mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••">
                    <span class="help-text"><i class="fa-solid fa-info-circle"></i> Laissez ce champ vide si vous souhaitez conserver le mot de passe actuel.</span>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-user-check"></i> Enregistrer les modifications
                </button>
            </form>
        </div>
    </main>

</body>
</html>
