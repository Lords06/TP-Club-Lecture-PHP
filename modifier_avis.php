<?php
session_start();
require_once 'configuration/db.php';

if (!isset($_SESSION['membre_id']) || !isset($_GET['id'])) {
    header("Location: lectures.php");
    exit;
}
$avis_id = (int)$_GET['id'];
$erreur = '';
$stmt = $pdo->prepare("SELECT * FROM avis WHERE id = ?");
$stmt->execute([$avis_id]);
$avis = $stmt->fetch();

if (!$avis || $avis['membre_id'] !== $_SESSION['membre_id']) {
    header("Location: lectures.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = (int)$_POST['note'];
    $commentaire = trim($_POST['commentaire'] ?? '');

    if ($note >= 1 && $note <= 5) {
        $update = $pdo->prepare("UPDATE avis SET note = ?, commentaire = ? WHERE id = ?");
        $update->execute([$note, $commentaire, $avis_id]);
        
        header("Location: details_livre.php?id=" . $avis['livre_id']);
        exit;
    } else {
        $erreur = "La note doit être entre 1 et 5.";
    }
}
?>

<?php include 'page/header.php'; ?>

<main class="container">
    <p class="back-link"><a href="details_livre.php?id=<?= $avis['livre_id'] ?>">Retour au livre</a></p>

    <h2>Modifier mon avis</h2>
    <?php if ($erreur): ?>
        <p class="error-text"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <div class="form-container">
        <form method="POST">
            <div class="form-group">
            <label>Note :</label><br>
                <select name="note" class="form-select">
                <option value="5" <?= $avis['note'] == 5 ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5/5)</option>
                <option value="4" <?= $avis['note'] == 4 ? 'selected' : '' ?>>⭐⭐⭐⭐ (4/5)</option>
                <option value="3" <?= $avis['note'] == 3 ? 'selected' : '' ?>>⭐⭐⭐ (3/5)</option>
                <option value="2" <?= $avis['note'] == 2 ? 'selected' : '' ?>>⭐⭐ (2/5)</option>
                <option value="1" <?= $avis['note'] == 1 ? 'selected' : '' ?>>⭐ (1/5)</option>
            </select>
            </div>
            <div class="form-group">
                <label>Commentaire :</label><br>
                <textarea name="commentaire" rows="5" class="form-textarea"><?= htmlspecialchars($avis['commentaire']) ?></textarea>
            </div>
            
            <button type="submit" class="btn-primary btn-submit">Mettre à jour mon avis</button>
        </form>
    </div>
</main>

<?php include 'page/footer.php'; ?>