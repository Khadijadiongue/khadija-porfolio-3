<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/connexion.php';
require_once __DIR__ . '/../../fonctions.php';
require_once __DIR__ . '/../verif_session.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
        die("Action non autorisée.");
    }
    
    $id = (int)$_POST['id'];
    $stmtImg = $pdo->prepare("SELECT image FROM projets WHERE id = :id");
    $stmtImg->execute([':id' => $id]);
    $projet = $stmtImg->fetch();
    if ($projet && $projet['image'] && file_exists(__DIR__ . '/../../image/projets/' . $projet['image'])) {
        unlink(__DIR__ . '/../../image/projets/' . $projet['image']);
    }

    $stmt = $pdo->prepare("DELETE FROM projets WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header('Location: index.php');
    exit();
}

$projets = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Projets | Espace Privé</title>
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

        /* Barre latérale de navigation */
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

        /* Zone de contenu principal */
        main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        header {
            display: flex;
            justify-content: flex-start;
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

        /* Bouton Ajouter un projet */
        .btn-add {
            background-color: #8c6239;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s ease;
            box-shadow: 0 4px 12px rgba(140, 98, 57, 0.15);
            margin-bottom: 30px;
        }

        .btn-add:hover {
            background-color: #704d2b;
        }

        /* Section de la table */
        .table-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 14px;
            border: 1px solid #eee7de;
            box-shadow: 0 5px 15px rgba(74, 55, 40, 0.03);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.92rem;
        }

        table th {
            background-color: #faf8f5;
            color: #5c4738;
            font-weight: 600;
            padding: 14px 16px;
            border-bottom: 2px solid #eee7de;
        }

        table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f4eae1;
            color: #4a3728;
            vertical-align: middle;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        /* Images miniatures */
        .thumbnail-wrapper {
            width: 70px;
            height: 50px;
            border-radius: 6px;
            overflow: hidden;
            background: #faf8f5;
            border: 1px solid #ded5ca;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .thumbnail-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-img {
            font-size: 0.75rem;
            color: #a39081;
            font-style: italic;
        }

        .tech-badge {
            background-color: #faf8f5;
            color: #704d2b;
            border: 1px solid #eaddcf;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            display: inline-block;
        }

        /* Liens d'actions */
        .action-links {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .link-edit {
            color: #8c6239;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .link-edit:hover {
            color: #4a3728;
            text-decoration: underline;
        }

        .btn-delete {
            background: none;
            border: none;
            color: #c81e1e;
            font-size: 0.92rem;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-delete:hover {
            color: #9b1c1c;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <aside>
        <h2>Espace Privé</h2>
        <nav>
            <a href="../dashboard.php"><i class="fa-solid fa-chart-pie"></i> Vue d'ensemble</a>
            <a href="#" class="active"><i class="fa-solid fa-layer-group"></i> Gestion Projets</a>
            <a href="../utilisateurs/index.php"><i class="fa-solid fa-users-gear"></i> Gestion Admins</a>
            <a href="../messages/index.php"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="../demandes/index.php"><i class="fa-solid fa-file-signature"></i> Demandes</a>
            <a href="../deconnexion.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Déconnexion</a>
        </nav>
    </aside>

    <main>
        <header>
            <a href="../dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
            <div class="header-title">
                <h1>Gestion des Projets</h1>
            </div>
        </header>

        <a href="creer.php" class="btn-add"><i class="fa-solid fa-plus"></i> Ajouter un nouveau projet</a>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Miniature</th>
                        <th>Titre du projet</th>
                        <th>Technologies utilisées</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($projets)): ?>
                        <tr><td colspan="4" style="text-align:center; color:#a39081; padding: 30px;">Aucun projet publié pour le moment.</td></tr>
                    <?php else: ?>
                        <?php foreach($projets as $p): ?>
                        <tr>
                            <td>
                                <div class="thumbnail-wrapper">
                                    <?php if($p['image']): ?>
                                        <img src="../../image/projets/<?= e($p['image']); ?>" alt="Miniature">
                                    <?php else: ?>
                                        <span class="no-img">Aucune</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="font-weight: 500; color: #2e2218;"><?= e($p['titre']); ?></td>
                            <td><span class="tech-badge"><?= e($p['technologies']); ?></span></td>
                            <td>
                                <div class="action-links">
                                    <a href="modifier.php?id=<?= $p['id']; ?>" class="link-edit">
                                        <i class="fa-solid fa-pen-to-square"></i> Modifier
                                    </a>
                                    <form action="index.php" method="POST" style="display:inline;" onsubmit="return confirm('Confirmer la suppression définitive de ce projet ?');">
                                        <input type="hidden" name="csrf_token" value="<?= generer_csrf(); ?>">
                                        <input type="hidden" name="action" value="supprimer">
                                        <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                        <button type="submit" class="btn-delete">
                                            <i class="fa-solid fa-trash-can"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
