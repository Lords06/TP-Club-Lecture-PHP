<?php
session_start();
require_once 'configuration/db.php';

if (!isset($_SESSION['membre_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['membre_role'] !== 'Admin' && $_SESSION['membre_role'] !== 'Modérateur') {
    header("Location: index.php");
    exit;
}
$erreur = '';
$succes = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date_debut = !empty($_POST['date_debut']) ? $_POST['date_debut'] : null;
    $date_fin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;
    $chemin_couverture = null;
    
    if (isset($_FILES['couverture']) && $_FILES['couverture']['error'] === UPLOAD_ERR_OK) {
        $dossier_destination = 'uploads/image/';
                $nom_fichier = uniqid() . '_' . basename($_FILES['couverture']['name']);
        $chemin_final = $dossier_destination . $nom_fichier;
        if (move_uploaded_file($_FILES['couverture']['tmp_name'], $chemin_final)) {
            $chemin_couverture = $chemin_final;
        } else {
            $erreur = "Erreur lors de l'enregistrement de l'image.";
        }
    }

    if (empty($titre) || empty($auteur)) {
        $erreur = "Le titre et l'auteur sont obligatoires.";
    } elseif (empty($erreur)) { 
                $insert = $pdo->prepare("
            INSERT INTO livres (titre, auteur, description, chemin_couverture, date_debut, date_fin, cree_par) 
            VALUES (:titre, :auteur, :description, :chemin_couverture, :date_debut, :date_fin, :cree_par)
        ");
        $insert->execute([
            'titre' => $titre,
            'auteur' => $auteur,
            'description' => $description,
            'chemin_couverture' => $chemin_couverture,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'cree_par' => $_SESSION['membre_id']
        ]);
        $succes = "Le livre '$titre' a été ajouté avec succès ! 📚";
    }
}
?>

<?php include 'page/header.php'; ?>

<main class="container">
    <h2>Ajouter un nouveau livre</h2>
    <?php if ($erreur): ?>
        <p class="error-text"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <?php if ($succes): ?>
        <p class="success-text"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Titre du livre * :</label><br>
            <input type="text" name="titre" required>
        </div>
        <div class="form-group">
            <label>Auteur * :</label><br>
            <input type="text" name="auteur" required>
        </div>
        <div class="form-group">
            <label>Image de couverture :</label><br>
            <input type="file" name="couverture" accept="image/*" style="margin-top: 5px;">
        </div>
        <div class="form-group">
            <label>Description :</label><br>
            <textarea name="description" rows="5" class="form-textarea"></textarea>
        </div>
        <div class="form-group">
            <label>Date de début de lecture :</label><br>
            <input type="date" name="date_debut">
        </div>
        <div class="form-group">
            <label>Date de fin de lecture :</label><br>
            <input type="date" name="date_fin">
        </div>
        <button type="submit" class="btn-primary btn-submit">Ajouter le livre</button>
    </form>
    <p class="back-link"><a href="index.php">Retour au tableau de bord</a></p>
</main>

<?php include 'page/footer.php'; ?>