<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/connexion.php';
require_once __DIR__ . '/../../fonctions.php';
require_once __DIR__ . '/../verif_session.php';

if (isset($_GET['lire'])) {
    $id_msg = (int)$_GET['lire'];
    $stmt = $pdo->prepare("UPDATE messages_contact SET lu = 1 WHERE id = :id");
    $stmt->execute([':id' => $id_msg]);
    header('Location: index.php');
    exit(); 
}

$messages = $pdo->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages reçus | Espace Privé</title>
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

        /* Sidebar latérale fixe */
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

        /* Zone centrale */
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

        /* Conteneur des cartes de messages */
        .messages-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 900px;
        }

        /* Style des cartes de messages */
        .message-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #eee7de;
            box-shadow: 0 4px 12px rgba(74, 55, 40, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(74, 55, 40, 0.04);
        }

        /* Style spécifique pour les messages NON LUS */
        .message-card.non-lue {
            background: #fffcf9;
            border-left: 5px solid #8c6239; /* Bordure marron dorée pour marquer l'inédit */
        }

        /* En-tête de la carte */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            border-bottom: 1px dashed #f4eae1;
            padding-bottom: 10px;
        }

        .sender-info {
            font-size: 1.05rem;
            font-weight: 600;
            color: #2e2218;
        }

        .sender-email {
            font-size: 0.9rem;
            color: #8a7665;
            font-weight: 400;
        }

        .message-date {
            font-size: 0.8rem;
            color: #a19082;
            background-color: #faf8f5;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #eee7de;
        }

        /* Corps du message */
        .card-body {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #4a3728;
            padding: 10px 5px;
            margin-bottom: 15px;
            white-space: pre-line;
        }

        /* Pied de la carte / Actions */
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

        /* Message si aucun élément */
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
            <a href="#" class="active"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="../demandes/index.php"><i class="fa-solid fa-file-signature"></i> Demandes</a>
            <a href="../deconnexion.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> Déconnexion</a>
        </nav>
    </aside>

    <main>
        <header>
            <a href="../dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
            <div class="header-title">
                <h1>Boîte de réception</h1>
            </div>
        </header>

        <div class="messages-container">
            <?php if (empty($messages)): ?>
                <div class="empty-box">
                    <i class="fa-solid fa-envelope-open-text"></i>
                    <p>Aucun message reçu dans la boîte de réception.</p>
                </div>
            <?php else: ?>
                <?php foreach($messages as $m): ?>
                    <div class="message-card <?= $m['lu'] == 0 ? 'non-lue' : ''; ?>">
                        
                        <div class="card-header">
                            <div class="sender-info">
                                <i class="fa-solid fa-user-tie" style="color: #8c6239; margin-right: 5px;"></i>
                                <?= e($m['nom']); ?> 
                                <span class="sender-email">&lt;<?= e($m['email']); ?>&gt;</span>
                            </div>
                            <div class="message-date">
                                <i class="fa-regular fa-clock"></i> <?= e($m['date_envoi']); ?>
                            </div>
                        </div>

                        <div class="card-body">
                            <?= nl2br(e($m['message'])); ?>
                        </div>

                        <div class="card-footer">
                            <?php if($m['lu'] == 0): ?>
                                <a href="index.php?lire=<?= $m['id']; ?>" class="btn-action-read">
                                    <i class="fa-solid fa-check-double"></i> Marquer comme lu
                                </a>
                            <?php else: ?>
                                <span class="status-read">
                                    <i class="fa-solid fa-circle-check" style="color: #a3b899;"></i> Message lu
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
