<?php
// 1. Inclusions obligatoires pour la base de données et les fonctions de sécurité
require_once '../config/connexion.php';
require_once '../fonctions.php';

// 2. Journalisation automatique de la visite sur cette page (Fonction corrigée pour éviter l'erreur fatale)
journaliser_visite($pdo, basename($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Compétences | Khadidiatou Diongue</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght=700;900&family=Poppins:wght=300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header>
    <?php 
    $titre_page = "Mes Compétences"; 
    require '../composants/navigation.php'; 
    ?>
</header>

<main class="skills-page">
    <div class="skills-header">
        <span class="hero-tag">Mon Expertise</span>
        <h2 class="section-title">Mes <span class="highlight">Compétences</span></h2>
        <p class="section-subtitle">Un mélange de rigueur technique et de qualités humaines au service de vos projets.</p>
    </div>

    <div class="category-wrapper">
        <h3 class="skill-category-title">Savoir-être</h3>
        <div class="skills-grid">
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-bullseye"></i></div>
                <h3>Persévérance</h3>
                <p>Capacité à poursuivre mes objectifs avec détermination face aux défis techniques.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-users"></i></div>
                <h3>Travail en équipe</h3>
                <p>Collaboration active et communication fluide au sein de projets académiques.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-calendar-check"></i></div>
                <h3>Organisation</h3>
                <p>Gestion rigoureuse du temps et priorisation des tâches critiques.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-lightbulb"></i></div>
                <h3>Créativité</h3>
                <p>Recherche de solutions innovantes et design d'interfaces centrées utilisateur.</p>
            </div>
        </div>
    </div>

    <div class="category-wrapper">
        <h3 class="skill-category-title">Expertise Technique</h3>
        <div class="skills-grid hard-skills">
            <div class="card">
                <div class="card-icon"><i class="fa-brands fa-html5"></i></div>
                <h3>HTML / CSS</h3>
                <p>Développement d'interfaces modernes, responsives et élégantes.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-brands fa-php"></i></div>
                <h3>PHP</h3>
                <p>Création de sites web dynamiques et gestion de la logique serveur.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-database"></i></div>
                <h3>MySQL</h3>
                <p>Conception, modélisation et manipulation de bases de données structurées.</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-brands fa-js"></i></div>
                <h3>JavaScript</h3>
                <p>Intégration de fonctionnalités interactive pour améliorer l'expérience utilisateur.</p>
            </div>
        </div>
    </div>
</main>

<?php require '../composants/pied-de-page.php'; ?>

</body>
</html>