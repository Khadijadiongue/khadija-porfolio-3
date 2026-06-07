<?php
// 1. Inclusions obligatoires
require_once __DIR__ . '/../../config/connexion.php';
require_once __DIR__ . '/../../fonctions.php';
require_once __DIR__ . '/../verif_session.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM projets WHERE id = :id");
$stmt->execute([':id' => $id]);
$projet = $stmt->fetch();

if (!$projet) { die("Projet introuvable."); }

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) { die("Action invalide."); }

    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $lien = trim($_POST['lien'] ?? '') ?: null;
    $nom_image = $projet['image']; 

    if (!empty($titre) && !empty($description) && !empty($technologies)) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $extensions_valides = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $extension_upload = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($extension_upload, $extensions_valides)) {
                if ($projet['image'] && file_exists(__DIR__ . '/../../images/projets/' . $projet['image'])) {
                    unlink(__DIR__ . '/../../images/projets/' . $projet['image']);
                }
                $nom_image = bin2hex(random_bytes(10)) . '.' . $extension_upload;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../images/projets/' . $nom_image);
            } else {
                $erreur = "Format d'image non valide.";
            }
        }

        if (empty($erreur)) {
            $stmt = $pdo->prepare("UPDATE projets SET titre = :t, description = :d, technologies = :tech, image = :i, lien = :l WHERE id = :id");
            $stmt->execute([':t' => $titre, ':d' => $description, ':tech' => $technologies, ':i' => $nom_image, ':l' => $lien, ':id' => $id]);
            header('Location: index.php');
            exit();
        }
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires (*).";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un projet | Admin</title>
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
            color: #2e2218;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            max-width: 650px;
            width: 100%;
            padding: 40px;
            border-radius: 16px;
            border: 1px solid #eee7de;
            box-shadow: 0 10px 30px rgba(74, 55, 40, 0.04);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 1.8rem;
            color: #2e2218;
            font-weight: 700;
        }

        .form-header p {
            font-size: 0.9rem;
            color: #8c6239;
            margin-top: 5px;
        }

        .alert-error {
            background-color: #fdf2f2;
            color: #ec5959;
            border: 1px solid #fbcbcb;
            padding: 12px 15px;
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
            color: #5c4738;
            margin-bottom: 8px;
        }

        .form-group label span {
            color: #ec5959;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            color: #a89280;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ded5ca;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #2e2218;
            background-color: #ffffff;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #8c6239;
            box-shadow: 0 0 0 4px rgba(140, 98, 57, 0.1);
        }

        textarea.form-control {
            padding-left: 15px;
            resize: vertical;
        }

        /* Section Image Actuelle & Upload */
        .image-section {
            display: flex;
            align-items: center;
            gap: 20px;
            background-color: #faf8f5;
            padding: 15px;
            border-radius: 10px;
            border: 1px dashed #ded5ca;
            margin-top: 5px;
        }

        .current-image-wrapper {
            width: 80px;
            height: 60px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #ded5ca;
            background-color: #ffffff;
            flex-shrink: 0;
        }

        .current-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-input-wrapper {
            flex: 1;
        }

        .file-input-wrapper input[type="file"] {
            font-size: 0.85rem;
            color: #5c4738;
        }

        /* Boutons d'actions */
        .actions-wrapper {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 13px 25px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            flex: 1;
        }

        .btn-submit {
            background-color: #8c6239;
            color: #ffffff;
            border: none;
        }

        .btn-submit:hover {
            background-color: #704d2b;
        }

        .btn-cancel {
            background-color: #ffffff;
            color: #8c6239;
            border: 1px solid #ded5ca;
        }

        .btn-cancel:hover {
            background-color: #faf8f5;
            border-color: #8c6239;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <div class="form-header">
            <h2>Modifier le projet</h2>
            <p>ID du projet : #<?= $id; ?></p>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> <?= e($erreur); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generer_csrf(); ?>">

            <div class="form-group">
                <label for="titre">Titre du projet <span>*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-heading"></i>
                    <input type="text" id="titre" name="titre" class="form-control" value="<?= e($projet['titre']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="technologies">Technologies <span>*</span> <small style="font-weight: normal; color:#a89280;">(séparées par des virgules)</small></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-tags"></i>
                    <input type="text" id="technologies" name="technologies" class="form-control" value="<?= e($projet['technologies']); ?>" placeholder="Ex: PHP, MySQL, CSS" required>
                </div>
            </div>

            <div class="form-group">
                <label for="lien">Lien du projet <small style="font-weight: normal; color:#a89280;">(optionnel)</small></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-link"></i>
                    <input type="url" id="lien" name="lien" class="form-control" value="<?= e($projet['lien'] ?? ''); ?>" placeholder="https://mon-projet.com">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description du projet <span>*</span></label>
                <textarea id="description" name="description" class="form-control" rows="5" required><?= e($projet['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Illustration du projet</label>
                <div class="image-section">
                    <div class="current-image-wrapper">
                        <?php if (!empty($projet['image']) && file_exists(__DIR__ . '/../../images/projets/' . $projet['image'])): ?>
                            <img src="../../images/projets/<?= e($projet['image']); ?>" alt="Aperçu actuel">
                        <?php else: ?>
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#f4eae1; color:#8c6239;">
                                <i class="fa-solid fa-image" style="font-size:1.2rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="file-input-wrapper">
                        <label style="font-size:0.8rem; color:#8c6239; margin-bottom:4px;">Remplacer l'image :</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="actions-wrapper">
                <a href="index.php" class="btn btn-cancel">
                    <i class="fa-solid fa-xmark"></i> Annuler
                </a>
                <button type="submit" class="btn btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>

</body>
</html>