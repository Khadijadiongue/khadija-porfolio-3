<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/connexion.php';
require_once __DIR__ . '/../fonctions.php';
require_once __DIR__ . '/verif_session.php';
$total_projets = $pdo->query("SELECT COUNT(*) FROM projets")->fetchColumn();
$messages_non_lus = $pdo->query("SELECT COUNT(*) FROM messages_contact WHERE lu = 0")->fetchColumn();
$demandes_non_lus = $pdo->query("SELECT COUNT(*) FROM demandes_projet WHERE lu = 0")->fetchColumn();
$dernieres_visites = $pdo->query("SELECT * FROM visites ORDER BY date_visite DESC LIMIT 5")->fetchAll();
$dernieres_demandes = $pdo->query("SELECT * FROM demandes_projet ORDER BY date_demande DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur | Espace Privé</title>
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
            background-color: #4a3728; /* Marron foncé */
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
            background-color: #8c6239; /* Or/Marron clair au survol */
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

        /* Zone principale du contenu */
        main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        header {
            margin-bottom: 40px;
        }

        header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2e2218;
        }

        header p {
            color: #8a7665;
            font-size: 0.95rem;
            margin-top: 5px;
        }

        /* Cartes d'indicateurs clés */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 14px;
            border: 1px solid #eee7de;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 15px rgba(74, 55, 40, 0.03);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            background-color: #faf8f5;
            color: #8c6239;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
        }

        .stat-info h3 {
            font-size: 0.85rem;
            color: #8a7665;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info p {
            font-size: 1.6rem;
            font-weight: 700;
            color: #4a3728;
            margin-top: 2px;
        }

        /* Sections Tableaux */
        .dashboard-section {
            background: #ffffff;
            padding: 30px;
            border-radius: 14px;
            border: 1px solid #eee7de;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(74, 55, 40, 0.03);
        }

        .dashboard-section h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #4a3728;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Tables modernisées */
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
        }

        table tr:last-child td {
            border-bottom: none;
        }

        .badge-unread {
            background-color: #8c6239;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: auto;
        }
    </style>
</head>
<body>

    <aside>
        <h2>Espace Privé</h2>
        <nav>
            <a href="#" class="active"><i class="fa-solid fa-chart-pie"></i> Vue d'ensemble</a>
            <a href="projets/index.php"><i class="fa-solid fa-layer-group"></i> Gestion Projets</a>
            <a href="utilisateurs/index.php"><i class="fa-solid fa-users-gear"></i> Gestion Admins</a>
            <a href="messages/index.php">
                <i class="fa-solid fa-envelope"></i> Messages 
                <?php if($messages_non_lus > 0): ?><span class="badge-unread"><?= $messages_non_lus; ?></span><?php endif; ?>
            </a>
            <a href="demandes/index.php">
                <i class="fa-solid fa-file-signature"></i> Demandes 
                <?php if($demandes_non_lus > 0): ?><span class="badge-unread"><?= $demandes_non_lus; ?></span><?php endif; ?>
            </a>
            <a href="deconnexion.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Déconnexion</a>
        </nav>
    </aside>

    <main>
        <header>
            <h1>Tableau de bord</h1>
            <p>Bienvenue, <?= e($_SESSION['admin_prenom'] ?? 'Administrateur'); ?> ! Voici l'activité récente de votre portfolio.</p>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
                <div class="stat-info">
                    <h3>Projets publiés</h3>
                    <p><?= $total_projets; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                <div class="stat-info">
                    <h3>Messages non lus</h3>
                    <p><?= $messages_non_lus; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-folder-open"></i></div>
                <div class="stat-info">
                    <h3>Demandes en attente</h3>
                    <p><?= $demandes_non_lus; ?></p>
                </div>
            </div>
        </div>

        <div class="dashboard-section">
            <h3><i class="fa-solid fa-paper-plane"></i> Les 5 dernières demandes reçues</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Type de Projet</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($dernieres_demandes)): ?>
                        <tr><td colspan="4" style="text-align:center; color:#a39081;">Aucune demande enregistrée.</td></tr>
                    <?php else: ?>
                        <?php foreach($dernieres_demandes as $d): ?>
                            <tr style="<?= $d['lu'] == 0 ? 'font-weight: 600; background-color: #faf8f5;' : ''; ?>">
                                <td><?= e($d['nom']); ?></td>
                                <td><?= e($d['email']); ?></td>
                                <td><?= e($d['type_projet']); ?></td>
                                <td><?= e($d['date_demande']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-section">
            <h3><i class="fa-solid fa-eye"></i> Historique des 5 dernières visites</h3>
            <table>
                <thead>
                    <tr>
                        <th>Adresse IP</th>
                        <th>Page Web</th>
                        <th>Horodatage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($dernieres_visites)): ?>
                        <tr><td colspan="3" style="text-align:center; color:#a39081;">Aucune visite enregistrée.</td></tr>
                    <?php else: ?>
                        <?php foreach($dernieres_visites as $v): ?>
                            <tr>
                                <td><code><?= e($v['adresse_ip']); ?></code></td>
                                <td><?= e($v['page']); ?></td>
                                <td><?= e($v['date_visite']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
