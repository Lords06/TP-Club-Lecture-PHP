<?php
session_start();
require_once 'configuration/db.php';
if (!isset($_SESSION['membre_id']) || ($_SESSION['membre_role'] !== 'Admin' && $_SESSION['membre_role'] !== 'Modérateur')) {
    header("Location: index.php");
    exit;
}
$erreur = '';
$succes = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $livre_id = !empty($_POST['livre_id']) ? (int)$_POST['livre_id'] : null;
    $date_heure = $_POST['date_heure'] ?? '';
    $lieu = trim($_POST['lieu'] ?? '');
    $lien = trim($_POST['lien'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if (empty($titre) || empty($date_heure)) {
        $erreur = "Le titre et la date/heure sont obligatoires.";
    } else {
        $insert = $pdo->prepare("
            INSERT INTO sessions (livre_id, titre, date_heure, lien, lieu, description, cree_par) 
            VALUES (:livre_id, :titre, :date_heure, :lien, :lieu, :description, :cree_par)
        ");
        $insert->execute([
            'livre_id' => $livre_id,
            'titre' => $titre,
            'date_heure' => $date_heure,
            'lien' => $lien,
            'lieu' => $lieu,
            'description' => $description,
            'cree_par' => $_SESSION['membre_id']
        ]);
        $succes = "La session '$titre' a été programmée avec succès ! ";
    }
}
$stmt_livres = $pdo->query("SELECT id, titre FROM livres ORDER BY titre ASC");
$liste_livres = $stmt_livres->fetchAll();
?>
<?php include 'page/header.php'; ?>
<main class="container">
    <p class="back-link"><a href="sessions.php">Retour aux évènements</a></p>
    <h2>Planifier une nouvelle session</h2>
    <?php if ($erreur): ?>
        <p class="error-text"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <?php if ($succes): ?>
        <p class="success-text"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>
    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Titre de l'évènement * :</label><br>
            <input type="text" name="titre" class="form-input" required>
        </div>
        <div class="form-group">
            <label>Lier à un livre ? :</label><br>
            <select name="livre_id" class="form-select-large">
                <option value="">-- Aucun livre spécifique (Général) --</option>
                <?php foreach ($liste_livres as $livre): ?>
                    <option value="<?= $livre['id'] ?>"><?= htmlspecialchars($livre['titre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date et Heure * :</label><br>
            <input type="datetime-local" name="date_heure" class="form-input" required>
        </div>
        <div class="form-group">
            <label>Lieu (si rencontre physique) :</label><br>
            <input type="text" name="lieu" class="form-input" placeholder="Ex: Café de la Gare, Paris">
        </div>
        <div class="form-group">
            <label>Lien (si live / visio) :</label><br>
            <input type="url" name="lien" class="form-input" placeholder="Ex: https://zoom.us/j/123456">
        </div>
        <div class="form-group">
            <label>Description :</label><br>
            <textarea name="description" rows="5" class="form-textarea" placeholder="De quoi allons-nous parler ?"></textarea>
        </div>
        <button type="submit" class="btn-primary btn-submit">Créer la session</button>
    </form>
</main>
<?php include 'page/footer.php'; ?>