
    <?php $page_courante = basename($_SERVER['PHP_SELF']); ?>
    <nav>
        <h1 class="logo"><?php echo isset($titre_page) ? $titre_page : 'Khadidiatou Diongue'; ?></h1>
        <ul>
            <li><a href="/Projet examen porfolio/index.php" class="<?php echo ($page_courante == 'index.php') ? 'actif' : ''; ?>">Présentation</a></li>
            <li><a href="/Projet examen porfolio/pages/projet.php" class="<?php echo ($page_courante == 'projets.php') ? 'actif' : ''; ?>">Projets</a></li>
            <li><a href="/Projet examen porfolio/pages/competence.php" class="<?php echo ($page_courante == 'competence.php') ? 'actif' : ''; ?>">Compétences</a></li>
            <li><a href="/Projet examen porfolio/pages/contact.php" class="<?php echo ($page_courante == 'contact.php') ? 'actif' : ''; ?>">Contact</a></li>

        </ul>
    </nav>