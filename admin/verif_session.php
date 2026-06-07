
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l'administrateur n'est pas connecté, redirection immédiate vers la page de connexion
if (!isset($_SESSION['admin_id'])) {
    header('Location: /Projet examen porfolio/admin/connexion.php');
    exit();
}
?>