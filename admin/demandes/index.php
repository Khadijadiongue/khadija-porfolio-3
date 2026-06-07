<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/connexion.php';
require_once __DIR__ . '/../../fonctions.php';
require_once __DIR__ . '/../verif_session.php';

if (isset($_GET['lire'])) {
    $id_demande = (int)$_GET['lire'];
    $stmt = $pdo->prepare("UPDATE demandes_projet SET lu = 1 WHERE id = :id");
    $stmt->execute([':id' => $id_demande]);
    header('Location: index.php');
    exit();
}

$demandes = $pdo->query("SELECT * FROM demandes_projet ORDER BY date_demande DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes Clients | Espace Privé</title>
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
            color: #4a3728;
            display: flex;
            min-height: 100vh;
        }

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

        .demandes-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 900px;
        }

        .demande-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #eee7de;
            box-shadow: 0 4px 12px rgba(74, 55, 40, 0.02);
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .demande-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(74, 55, 40, 0.04);
        }

        .demande-card.non-lue {
            background: #fffcf9;
            border-left: 5px solid #d97706;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            border-bottom: 1px dashed #f4eae1;
            padding-bottom: 12px;
        }

        .project-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2e2218;
        }

        .budget-badge {
            background-color: #f3eae1;
            color: #704d2b;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .non-lue .budget-badge {
            background-color: #fef3c7;
            color: #92400e;
        }

        .card-meta {
            font-size: 0.88rem;
            color: #8a7665;
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .card-meta a {
            color: #8c6239;
            text-decoration: none;
        }

        .card-meta a:hover {
            text-decoration: underline;
        }

        .card-body {
            font-size: 0.92rem;
            line-height: 1.6;
            color: #4a3728;
            background-color: #faf8f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #f4eae1;
        }

        .card-body strong {
            color: #5c4738;
            display: block;
            margin-bottom: 5px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .btn-action-read {
            background-color: #8c6239;
            color: #ffffff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s ease;
        }

        .btn-action-read:hover {
            background-color: #704d2b;
        }

        .status-read {
            color: #a19082;
            font-size: 0.82rem;
            font-style: italic;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .empty-box {
            background: #ffffff;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            border: 1px solid #eee7de;
            color: #8a7665;
        }

        .empty-box i {
            font-size: 3rem;
            color: #ded5ca;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <aside>
        <h2>Espace Privé</h2>
        <nav>
            <a href="../dashboard.php"><i class="fa-solid fa-chart-pie"></i> Vue d'ensemble</a>
            <a href="../projets/index.php"><i class="fa-solid fa-layer-group"></i> Gestion Projets</a>
            <a href="../utilisateurs/index.php"><i class="fa-solid fa-users-gear"></i> Gestion Admins</a>
            <a href="../messages/index.php"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="#" class="active"><i class="fa-solid fa-file-signature"></i> Demandes</a>
            <a href="../deconnexion.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Déconnexion</a>
        </nav>
    </aside>

    <main>
        <header>
            <a href="../dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
            <div class="header-title">
                <h1>Demandes de travaux clients</h1>
            </div>
        </header>

        <div class="demandes-container">
            <?php if (empty($demandes)): ?>
                <div class="empty-box">
                    <i class="fa-solid fa-folder-open"></i>
                    <p>Aucune demande de projet reçue pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach($demandes as $d): ?>
                    <div class="demande-card <?= $d['lu'] == 0 ? 'non-lue' : ''; ?>">
                        
                        <div class="card-header">
                            <div class="project-title">
                                <i class="fa-solid fa-laptop-code" style="color: #8c6239; margin-right: 5px;"></i>
                                <?= e($d['type_projet']); ?>
                            </div>
                            <div class="budget-badge">
                                <i class="fa-solid fa-wallet"></i> <?= e($d['budget'] ?? 'Non spécifié'); ?>
                            </div>
                        </div>

                        <div class="card-meta">
                            <span><i class="fa-solid fa-user"></i> <strong><?= e($d['nom']); ?></strong></span>
                            <span><i class="fa-solid fa-envelope"></i> <a href="mailto:<?= e($d['email']); ?>"><?= e($d['email']); ?></a></span>
                        </div>

                        <div class="card-body">
                            <strong>Description du besoin :</strong>
                            <?= nl2br(e($d['description'])); ?>
                        </div>

                        <div class="card-footer">
                            <?php if($d['lu'] == 0): ?>
                                <a href="index.php?lire=<?= $d['id']; ?>" class="btn-action-read">
                                    <i class="fa-solid fa-envelope-open"></i> Marquer comme lue
                                </a>
                            <?php else: ?>
                                <span class="status-read">
                                    <i class="fa-solid fa-circle-check" style="color: #a3b899;"></i> Demande traitée
                                </span>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>