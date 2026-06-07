<?php
require_once '../config/connexion.php';
require_once '../fonctions.php';

journaliser_visite($pdo, basename($_SERVER['PHP_SELF']));

$search = trim($_GET['search'] ?? '');
$projets_afficher = [];

try {

    if (!empty($search)) {

        $sql = "SELECT * FROM projets
                WHERE titre LIKE :search
                OR description LIKE :search
                OR technologies LIKE :search
                ORDER BY date_creation DESC";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':search' => '%' . $search . '%'
        ]);

    } else {

        $sql = "SELECT * FROM projets ORDER BY date_creation DESC";
        $stmt = $pdo->query($sql);
    }

    $projets_afficher = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    error_log("Erreur récupération projets : " . $e->getMessage());

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Projets | Khadidiatou Diongue</title>

    <link rel="stylesheet" href="../css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght=700;900&family=Poppins:wght=300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header>
    <?php $titre_page = "Mes Projets"; ?>
    <?php require '../composants/navigation.php'; ?>
</header>

<main class="projects-page">

    <div class="projects-header">

        <span class="hero-tag">
            <i class="fa-solid fa-briefcase"></i>
            Portfolio
        </span>

        <h2 class="section-title">
            Mes <span class="highlight">Réalisations</span>
        </h2>

        <form class="search-container" method="GET" action="">
            <input
                type="text"
                name="search"
                placeholder="Rechercher un projet..."
                value="<?= e($search) ?>"
            >

            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
                Rechercher
            </button>
        </form>

    </div>

    <section class="projects-grid">

        <?php if (!empty($projets_afficher)): ?>

            <?php foreach ($projets_afficher as $projet): ?>

                <?php
                $techArray = explode(',', $projet['technologies']);

                $chemin_image = "../image/projets/" . $projet['image'];
                ?>

                <div class="project-card">

                    <div class="project-image">

                        <img
                            src="<?= $chemin_image ?>"
                            alt="<?= e($projet['titre']) ?>"
                        >

                    </div>

                    <div class="project-info">

                        <h3><?= e($projet['titre']) ?></h3>

                        <p><?= nl2br(e($projet['description'])) ?></p>

                        <div class="technologies">

                            <?php foreach ($techArray as $tech): ?>

                                <span class="badge">
                                    <i class="fa-solid fa-tag"></i>
                                    <?= e(trim($tech)) ?>
                                </span>

                            <?php endforeach; ?>

                        </div>

                        <?php if (!empty($projet['lien'])): ?>

                            <a
                                href="<?= e($projet['lien']) ?>"
                                target="_blank"
                                class="btn-detail"
                            >
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                Voir le projet
                            </a>

                        <?php else: ?>

                            <a
                                href="<?= $chemin_image ?>"
                                target="_blank"
                                class="btn-detail"
                            >
                                <i class="fa-solid fa-image"></i>
                                Voir l'image
                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="no-result">

                <i class="fa-solid fa-folder-open"></i>

                <p>
                    Aucun projet trouvé pour :
                    <strong><?= e($search) ?></strong>
                </p>

                <a href="projet.php" class="btn-detail">
                    Afficher tous les projets
                </a>

            </div>

        <?php endif; ?>

    </section>

</main>

<?php require '../composants/pied-de-page.php'; ?>

</body>
</html>