<?php
require_once 'config/connexion.php';
require_once 'fonctions.php';
journaliser_visite($pdo, basename($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khadidiatou Diongue | Portfolio</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght=300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header>
    <?php require 'composants/navigation.php'; ?>
</header>

<main class="hero-presentation">
    <div class="hero-content">
        <span class="hero-tag">Étudiante en Génie Logiciel</span>
        <h1>Bonjour ! Je suis <br><span class="highlight">Khadidiatou</span></h1>
        
        <p class="hero-description">
           Je m’appelle Khadija, étudiante en génie logiciel. <br>
           Je suis passionnée par le développement web et la programmation. <br>
           J’aime apprendre et créer des solutions informatiques simples et utiles.<br>
           Mon objectif est de devenir une développeuse compétente dans le domaine du numérique. <br>
        </p>

        <h3>Compétences clés :</h3>
        <div class="competences-box">
            <div class="mini-cadre">Développement Web</div>
            <div class="mini-cadre">Système embarqué</div>
            <div class="mini-cadre">Administration réseau</div>
            <div class="mini-cadre">Bases de données</div>
        </div>

        <div class="hero-actions">
            <a href="fichiers/mon_cv.pdf" download="CV_Khadija_Diongue.pdf" class="btn_cv">Télécharger mon CV</a>
            <a href="pages/contact.php" class="btn_cv outline">Me contacter</a>
        </div>
    </div>
    <div class="hero-visual">
        <div class="cadre-photo-moderne">
            <img src="image/projets/photo de profil.jpeg" alt="Khadidiatou Diongue">
            <div class="shape-decoration"></div>
        </div>
    </div>
</main>

<section class="services-section">
    <h2 class="section-title">Mes Services</h2>
    <div class="services-container">
        <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-code"></i></div>
            <h3>Développement Web</h3>
            <p>Conception d'interfaces modernes et dynamiques (PHP/MySQL).</p>
        </div>
        <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-server"></i></div>
            <h3>Réseaux</h3>
            <p>Configuration de serveurs Windows et services DNS/DHCP.</p>
        </div>
        <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-microchip"></i></div>
            <h3>Embarqué</h3>
            <p>Programmation Arduino et intégration de capteurs RFID.</p>
        </div>
    </div>
</section>

<?php require 'composants/pied-de-page.php'; ?>

</body>
</html>
