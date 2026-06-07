<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusions de vos fichiers de configuration
require_once __DIR__ . '/../../config/connexion.php';
require_once __DIR__ . '/../../fonctions.php';
require_once __DIR__ . '/../verif_session.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $lien = trim($_POST['lien'] ?? '') ?: null;
    $nom_image = null;

    if (empty($titre) || empty($description) || empty($technologies)) {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Traitement de l'image (si une image est fournie)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $extensions_valides = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $extension_upload = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($extension_upload, $extensions_valides)) {
                $nom_image = bin2hex(random_bytes(10)) . '.' . $extension_upload;
                
                $dossier_cible = __DIR__ . '/../../image/projets/';
                if (!is_dir($dossier_cible)) {
                    mkdir($dossier_cible, 0777, true);
                }
                
                move_uploaded_file($_FILES['image']['tmp_name'], $dossier_cible . $nom_image);
            } else {
                $erreur = "Format d'image invalide (Uniquement : jpg, jpeg, png, webp, gif).";
            }
        }

        // Insertion sécurisée dans la base de données s'il n'y a pas d'erreur d'image
        if (empty($erreur)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO projets (titre, description, technologies, image, lien) VALUES (:t, :d, :tech, :i, :l)");
                $stmt->execute([
                    ':t' => $titre, 
                    ':d' => $description, 
                    ':tech' => $technologies, 
                    ':i' => $nom_image, 
                    ':l' => $lien
                ]);
                
                // Redirection vers la liste des projets après le succès
                header('Location: index.php');
                exit();
            } catch (PDOException $e) {
                // Si la table ou les colonnes ont un problème, l'erreur s'affichera clairement ici
                $erreur = "Erreur de base de données : " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Projet | Espace Privé</title>
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
            background-color: #fcfaf7;
            color: #2e2218; /* Texte général marron très foncé pour être parfaitement visible */
            display: flex;
            min-height: 100vh;
        }

        /* Barre latérale fixe à gauche */
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

        /* Zone de contenu à droite */
        main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            background-color: #fcfaf7;
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
            color: #2e2218; /* Titre principal bien noir */
        }

        /* Conteneur Blanc du Formulaire */
        .form-container {
            background: #ffffff;
            padding: 35px;
            border-radius: 14px;
            border: 1px solid #eee7de;
            box-shadow: 0 5px 15px rgba(74, 55, 40, 0.03);
            max-width: 800px;
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

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2e2218; /* Écritures des labels forcées en sombre */
        }

        /* Couleur et fond des champs de saisie */
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            background: #ffffff; /* Fond bien blanc */
            border: 1px solid #ded5ca;
            border-radius: 8px;
            color: #2e2218; /* Texte tapé par l'utilisateur bien visible */
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8c6239;
            box-shadow: 0 0 0 4px rgba(140, 98, 57, 0.1);
        }

        .form-group input[type="file"] {
            font-size: 0.9rem;
            color: #2e2218;
            background: #faf8f5;
            padding: 10px;
            border: 1px dashed #ded5ca;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
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
            <a href="index.php" class="active"><i class="fa-solid fa-layer-group"></i> Gestion Projets</a>
            <a href="../utilisateurs/index.php"><i class="fa-solid fa-users-gear"></i> Gestion Admins</a>
            <a href="../messages/index.php"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="../demandes/index.php"><i class="fa-solid fa-file-signature"></i> Demandes</a>
            <a href="../deconnexion.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Déconnexion</a>
        </nav>
    </aside>

    <main>
        <header>
            <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Retour à la liste</a>
            <div class="header-title">
                <h1>Ajouter un Projet</h1>
            </div>
        </header>

        <div class="form-container">
            <?php if (!empty($erreur)): ?>
                <div class="error-box">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= $erreur; ?>
                </div>
            <?php endif; ?>

            <form action="creer.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="titre">Titre du projet *</label>
                    <input type="text" id="titre" name="titre" placeholder="Entrez le titre du projet" required>
                </div>

                <div class="form-group">
                    <label for="technologies">Technologies utilisées *</label>
                    <input type="text" id="technologies" name="technologies" placeholder="Ex: PHP, MySQL, CSS, JS" required>
                </div>

                <div class="form-group">
                    <label for="lien">Lien du projet (URL / Github)</label>
                    <input type="url" id="lien" name="lien" placeholder="https://github.com/votre-username/nom-du-projet">
                </div>

                <div class="form-group">
                    <label for="image">Illustration (Image du projet)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="description">Description complète *</label>
                    <textarea id="description" name="description" rows="6" placeholder="Décrivez les objectifs et fonctionnalités de votre réalisation..." required></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-circle-plus"></i> Créer le projet
                </button>
            </form>
        </div>
    </main>

</body>
</html>