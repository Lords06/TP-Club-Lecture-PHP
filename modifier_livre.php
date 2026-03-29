<?php
session_start();
require_once 'configuration/db.php';
if (!isset($_SESSION['membre_id']) || ($_SESSION['membre_role'] !== 'Admin' && $_SESSION['membre_role'] !== 'Modérateur')) {
    header("Location: index.php");
    exit;
}
$erreur = '';
$succes = '';
if (!isset($_GET['id'])) {
    header("Location: lectures.php");
    exit;
}
$id = (int)$_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date_debut = !empty($_POST['date_debut']) ? $_POST['date_debut'] : null;
    $date_fin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;
    if (empty($titre) || empty($auteur)) {
        $erreur = "Le titre et l'auteur sont obligatoires.";
    } else {
        $update = $pdo->prepare("
            UPDATE livres 
            SET titre = :titre, auteur = :auteur, description = :description, date_debut = :date_debut, date_fin = :date_fin 
            WHERE id = :id
        ");
        $update->execute([
            'titre' => $titre,
            'auteur' => $auteur,
            'description' => $description,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'id' => $id
        ]);
        
        $succes = "Le livre a été mis à jour avec succès ! ✏️";
    }
}
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = :id");
$stmt->execute(['id' => $id]);
$livre = $stmt->fetch();
if (!$livre) {
    header("Location: lectures.php");
    exit;
}
?>
<?php include 'page/header.php'; ?>
<main class="container">
    <h2>Modifier le livre</h2>
    <?php if ($erreur): ?>
        <p class="error-text"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <?php if ($succes): ?>
        <p class="success-text"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Titre du livre * :</label><br>
            <input type="text" name="titre" value="<?= htmlspecialchars($livre['titre']) ?>" required>
        </div>
        <div class="form-group">
            <label>Auteur * :</label><br>
            <input type="text" name="auteur" value="<?= htmlspecialchars($livre['auteur']) ?>" required>
        </div>
        <div class="form-group">
            <label>Description :</label><br>
            <textarea name="description" rows="5" class="form-textarea"><?= htmlspecialchars($livre['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Date de début de lecture :</label><br>
            <input type="date" name="date_debut" value="<?= htmlspecialchars($livre['date_debut'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Date de fin de lecture :</label><br>
            <input type="date" name="date_fin" value="<?= htmlspecialchars($livre['date_fin'] ?? '') ?>">
        </div>
        <button type="submit" class="btn-primary btn-submit" style="background-color: #f39c12;">Mettre à jour</button>
    </form>

    <p class="back-link"><a href="lectures.php">Retour à la bibliothèque</a></p>
</main>
<?php include 'page/footer.php'; ?>