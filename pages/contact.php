<?php
// 1. Inclusions obligatoires tout en haut (sans espaces dans les chemins)
require_once '../config/connexion.php';
require_once '../fonctions.php';

// 2. Journalisation de la visite (Nom exact de votre fonction globale)
journaliser_visite($pdo, basename($_SERVER['PHP_SELF']));

// Initialisation des variables de messages
$contact_sucess = "";
$contact_erreur = "";
$projet_sucess = "";
$projet_erreur = "";

// 3. TRAITEMENT DU FORMULAIRE : CONTACT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["envoyer_message"])) {
    // Vérification du jeton CSRF (Nom exact de votre fonction globale)
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
        $contact_erreur = "Échec de la vérification de sécurité (CSRF).";
    } else {
        $nom = trim($_POST["nom"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $message = trim($_POST["message"] ?? "");

        // Validation (Logique Partie 2 adaptée)
        if (empty($nom)) {
            $contact_erreur = "Le nom est obligatoire.";
        } elseif (empty($email)) {
            $contact_erreur = "L'email est obligatoire.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $contact_erreur = "L'email n'est pas valide.";
        } elseif (empty($message)) {
            $contact_erreur = "Le message est obligatoire.";
        } else {
            try {
                // Insertion sécurisée en BDD (Requête préparée)
                $sql = "INSERT INTO messages_contact (nom, email, message) VALUES (:nom, :email, :message)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':email' => $email,
                    ':message' => $message
                ]);
                $contact_sucess = "Votre message a été envoyé avec succès !";
                // On vide les champs après succès
                $_POST['nom'] = $_POST['email'] = $_POST['message'] = "";
            } catch (Exception $e) {
                error_log("Erreur insertion contact : " . $e->getMessage());
                $contact_erreur = "Une erreur technique est survenue.";
            }
        }
    }
}

// 4. TRAITEMENT DU FORMULAIRE : DEMANDE DE PROJET
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["envoyer_projet"])) {
    // Vérification du jeton CSRF
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
        $projet_erreur = "Échec de la vérification de sécurité (CSRF).";
    } else {
        $nom_du_projet = trim($_POST["nom_du_projet"] ?? "");
        $email_demandeur = trim($_POST["email_projet"] ?? ""); // Récupération de l'email dynamique
        $type_de_projet = trim($_POST["type_de_projet"] ?? "");
        $budget = trim($_POST["budget"] ?? "");
        $description = trim($_POST["description"] ?? "");

        if (empty($nom_du_projet)) {
            $projet_erreur = "Le nom du projet est obligatoire.";
        } elseif (empty($email_demandeur) || !filter_var($email_demandeur, FILTER_VALIDATE_EMAIL)) {
            $projet_erreur = "Un email de contact valide est obligatoire.";
        } elseif (empty($type_de_projet)) {
            $projet_erreur = "Le type de projet est obligatoire.";
        } elseif (empty($description)) {
            $projet_erreur = "La description est obligatoire.";
        } else {
            try {
                $sql = "INSERT INTO demandes_projet (nom, email, type_projet, description, budget) 
                        VALUES (:nom, :email, :type_projet, :description, :budget)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom_du_projet,
                    ':email' => $email_demandeur,
                    ':type_projet' => $type_de_projet,
                    ':description' => $description,
                    ':budget' => !empty($budget) ? $budget : null
                ]);
                $projet_sucess = "Votre demande de projet a été enregistrée avec succès !";
                $_POST['nom_du_projet'] = $_POST['email_projet'] = $_POST['type_de_projet'] = $_POST['budget'] = $_POST['description'] = "";
            } catch (Exception $e) {
                error_log("Erreur insertion projet : " . $e->getMessage());
                $projet_erreur = "Une erreur technique est survenue.";
            }
        }
    }
}

// Génération du jeton pour l'affichage des formulaires
$token = generer_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact & Demande de Projet | Khadidiatou Diongue</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
    <?php 
    $titre_page = "Contactez-moi"; 
    require '../composants/navigation.php'; 
    ?>
</header>

<section class="contact-page">
    <div class="bloc-formulaire">
        <h2><i class="fa-solid fa-envelope"></i> Contact</h2>
        <form method="POST" action="contact.php">
            <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">

            <input type="text" placeholder="Votre nom *" name="nom" value="<?php echo isset($_POST['nom']) ? e($_POST['nom']) : ''; ?>" required>
            <input type="email" placeholder="Votre email *" name="email" value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>" required>
            <input type="text" placeholder="Votre numéro de téléphone" name="telephone">
            <textarea placeholder="Votre message *" name="message" required><?php echo isset($_POST['message']) ? e($_POST['message']) : ''; ?></textarea>
            
            <button type="submit" name="envoyer_message">Envoyer le message</button>
        </form>

        <?php if ($contact_erreur): ?>
            <p style="color: #ff3333; font-weight: 500; margin-top: 10px;"><?php echo e($contact_erreur); ?></p>
        <?php endif; ?>
        
        <?php if ($contact_sucess): ?>
            <p style="color: #22bb22; font-weight: 500; margin-top: 10px;"><?php echo e($contact_sucess); ?></p>
        <?php endif; ?>
    </div>

    <div class="bloc-formulaire">
        <h2><i class="fa-solid fa-diagram-project"></i> Demande de Projet</h2>
        <form method="POST" action="contact.php">
            <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">

            <input type="text" placeholder="Nom de l'entreprise ou du projet *" name="nom_du_projet" value="<?php echo isset($_POST['nom_du_projet']) ? e($_POST['nom_du_projet']) : ''; ?>" required>
            <input type="email" placeholder="Votre email de contact *" name="email_projet" value="<?php echo isset($_POST['email_projet']) ? e($_POST['email_projet']) : ''; ?>" required>
            <input type="text" placeholder="Type de projet * (ex: E-commerce, Application)" name="type_de_projet" value="<?php echo isset($_POST['type_de_projet']) ? e($_POST['type_de_projet']) : ''; ?>" required>
            <input type="text" placeholder="Budget estimé (ex: 300 000 FCFA)" name="budget" value="<?php echo isset($_POST['budget']) ? e($_POST['budget']) : ''; ?>">
            <textarea placeholder="Décrivez votre besoin technique détaillé * " name="description" required><?php echo isset($_POST['description']) ? e($_POST['description']) : ''; ?></textarea>
            
            <button type="submit" name="envoyer_projet">Soumettre mon projet</button>
        </form>

        <?php if ($projet_erreur): ?>
            <p style="color: #ff3333; font-weight: 500; margin-top: 10px;"><?php echo e($projet_erreur); ?></p>
        <?php endif; ?>
        
        <?php if ($projet_sucess): ?>
            <p style="color: #22bb22; font-weight: 500; margin-top: 10px;"><?php echo e($projet_sucess); ?></p>
        <?php endif; ?>
    </div>
</section>

<footer>
    <h3>Mes réseaux sociaux</h3>
    <div class="reseaux">
        <a href="https://www.tiktok.com/@user6175813877915" target="_blank"><i class="fab fa-tiktok"></i></a>
        <a href="mailto:dionguekhadija35@gmail.com"><i class="fa-solid fa-envelope"></i></a>
        <a href="https://www.snapchat.com/@kdiongue2028967" target="_blank"><i class="fab fa-snapchat"></i></a>
        <a href="https://www.instagram.com/khadijahhhhh__d" target="_blank"><i class="fab fa-instagram"></i></a>
    </div>
</footer>
</body>
</html>