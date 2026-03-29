<?php
session_start();
require_once 'configuration/db.php';

if (!isset($_SESSION['membre_id'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_GET['id'])) {
    header("Location: lectures.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_avis'])) {
    $avis_id = (int)$_POST['avis_id'];
        $stmt_check = $pdo->prepare("SELECT membre_id FROM avis WHERE id = ?");
    $stmt_check->execute([$avis_id]);
    $avis_cible = $stmt_check->fetch();

    if ($avis_cible) {
        if ($avis_cible['membre_id'] === $_SESSION['membre_id'] || $_SESSION['membre_role'] === 'Admin' || $_SESSION['membre_role'] === 'Modérateur') {
            $del = $pdo->prepare("DELETE FROM avis WHERE id = ?");
            $del->execute([$avis_id]);
            $succes = "L'avis a bien été supprimé ";
        } else {
            $erreur = "Tu n'as pas le droit de supprimer cet avis.";
        }
    }
}

$livre_id = (int)$_GET['id'];

$erreur = '';
$succes = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poster_avis'])) {
    $note = (int)$_POST['note'];
    $commentaire = trim($_POST['commentaire'] ?? '');

    if ($note >= 1 && $note <= 5) {
        $insert = $pdo->prepare("INSERT INTO avis (livre_id, membre_id, note, commentaire) VALUES (:livre_id, :membre_id, :note, :commentaire)");
        $insert->execute(['livre_id' => $livre_id, 'membre_id' => $_SESSION['membre_id'], 'note' => $note, 'commentaire' => $commentaire]);
        $succes = "Ton avis a été publié ";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maj_progression'])) {
    $pourcentage = (int)$_POST['pourcentage'];
    if ($pourcentage >= 0 && $pourcentage <= 100) {
        $stmt_prog = $pdo->prepare("INSERT INTO progression (livre_id, membre_id, pourcentage) VALUES (:livre_id, :membre_id, :pourcentage) ON DUPLICATE KEY UPDATE pourcentage = VALUES(pourcentage)");
        $stmt_prog->execute(['livre_id' => $livre_id, 'membre_id' => $_SESSION['membre_id'], 'pourcentage' => $pourcentage]);
        $succes = "Ta progression a été mise à jour ";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_doc'])) {
    if ($_SESSION['membre_role'] === 'Admin' || $_SESSION['membre_role'] === 'Modérateur') {
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['document'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if ($ext === 'pdf') {
             $dossier = 'uploads/docs/';
            if (!is_dir($dossier)) mkdir($dossier, 0777, true);
            $nom_sauvegarde = uniqid() . '_' . basename($file['name']);
            $chemin = $dossier . $nom_sauvegarde;
            if (move_uploaded_file($file['tmp_name'], $chemin)) {
                $insert_doc = $pdo->prepare("INSERT INTO documents (livre_id, nom_fichier, chemin_fichier, type_mime, taille, ajoute_par) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_doc->execute([
                    $livre_id, 
                    $file['name'], 
                    $chemin, 
                    $file['type'],
                    $file['size'], 
                    $_SESSION['membre_id']
                    ]);
                    $succes = "Le PDF a été ajouté avec succès ";
                } else {
                    $erreur = "Erreur lors de l'enregistrement du fichier.";
                }
            } else {
                $erreur = "Seuls les fichiers PDF sont autorisés !";
            }
        } else {
            $erreur = "Veuillez sélectionner un fichier valide.";
        }
    }
}
$stmt_livre = $pdo->prepare("SELECT * FROM livres WHERE id = :id");
$stmt_livre->execute(['id' => $livre_id]);
$livre = $stmt_livre->fetch();

if (!$livre) {
    header("Location: lectures.php");
    exit;
}
$stmt_avis = $pdo->prepare("SELECT a.*, m.nom FROM avis a JOIN membres m ON a.membre_id = m.id WHERE a.livre_id = :livre_id ORDER BY a.cree_le DESC");
$stmt_avis->execute(['livre_id' => $livre_id]);
$liste_avis = $stmt_avis->fetchAll();
$stmt_ma_prog = $pdo->prepare("SELECT pourcentage FROM progression WHERE livre_id = :livre_id AND membre_id = :membre_id");
$stmt_ma_prog->execute(['livre_id' => $livre_id, 'membre_id' => $_SESSION['membre_id']]);
$ma_progression = $stmt_ma_prog->fetchColumn() ?: 0;
$stmt_moy_prog = $pdo->prepare("SELECT AVG(pourcentage) FROM progression WHERE livre_id = :livre_id");
$stmt_moy_prog->execute(['livre_id' => $livre_id]);
$moyenne_progression = round((float)$stmt_moy_prog->fetchColumn());

$stmt_docs = $pdo->prepare("SELECT * FROM documents WHERE livre_id = :livre_id ORDER BY ajoute_le DESC");
$stmt_docs->execute(['livre_id' => $livre_id]);
$documents = $stmt_docs->fetchAll();
?>

<?php include 'page/header.php'; ?>

<main class="container">
    <p class="back-link"><a href="lectures.php"> Retour à la bibliothèque</a></p>

    <?php if ($erreur): ?><p class="error-text"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>
    <?php if ($succes): ?><p class="success-text"><?= htmlspecialchars($succes) ?></p><?php endif; ?>

    <div class="details-livre-header">
        <?php if (!empty($livre['chemin_couverture'])): ?>
            <img src="<?= htmlspecialchars($livre['chemin_couverture']) ?>" alt="Couverture" class="details-couverture">
        <?php endif; ?>
        
        <div class="details-infos">
            <h2><?= htmlspecialchars($livre['titre']) ?></h2>
            <p class="details-auteur">Par <?= htmlspecialchars($livre['auteur']) ?></p>
            <p class="details-description"><?= nl2br(htmlspecialchars($livre['description'] ?? 'Aucune description.')) ?></p>
            <div class="progression-section">
                <h4>Progression du Club : <?= $moyenne_progression ?>%</h4>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: <?= $moyenne_progression ?>%;"></div>
                </div>
                <form method="POST" class="form-progression">
                    <label>J'en suis à :</label>
                    <input type="number" name="pourcentage" min="0" max="100" value="<?= $ma_progression ?>" class="input-pourcentage" required>
                    <span>%</span>
                    <button type="submit" name="maj_progression" class="btn-primary btn-small">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
    <div class="documents-section">
        <h3>Documents liés (PDF)</h3>
        
        <?php if (count($documents) > 0): ?>
            <ul class="docs-list">
                <?php foreach ($documents as $doc): ?>
                    <li>
                    <span><?= htmlspecialchars($doc['nom_fichier']) ?> <small style="color:#7f8c8d;">(<?= round($doc['taille'] / 1024) ?> Ko)</small></span>
                    <a href="download.php?id=<?= $doc['id'] ?>" class="btn-primary btn-small" style="text-decoration: none;"> Télécharger</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="color: #7f8c8d; font-style: italic;">Aucun document n'a été ajouté pour ce livre.</p>
        <?php endif; ?>
        <?php if ($_SESSION['membre_role'] === 'Admin' || $_SESSION['membre_role'] === 'Modérateur'): ?>
            <div class="upload-box">
            <form method="POST" enctype="multipart/form-data" class="form-upload-doc">
                <label style="font-weight: bold;">Ajouter un PDF :</label>
                <input type="file" name="document" accept=".pdf" class="form-input-file" required>
                <button type="submit" name="upload_doc" class="btn-primary btn-small">Uploader le fichier</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <h3>Avis des lecteurs (<?= count($liste_avis) ?>)</h3>
    <div class="form-avis-container">
        <h4>Laisser un avis</h4>
        <form method="POST">
            <div class="form-group">
                <label>Note :</label><br>
                <select name="note" class="form-select">
                    <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                    <option value="4">⭐⭐⭐⭐ (4/5)</option>
                    <option value="3">⭐⭐⭐ (3/5)</option>
                    <option value="2">⭐⭐ (2/5)</option>
                    <option value="1">⭐ (1/5)</option>
                </select>
            </div>
            <div class="form-group">
            <label>Commentaire (optionnel) :</label><br>
            <textarea name="commentaire" rows="3" class="form-textarea"></textarea>
            </div>
            
            <button type="submit" name="poster_avis" class="btn-primary btn-submit">Publier l'avis</button>
        </form>
    </div>
    <div class="liste-avis">
        <?php if (count($liste_avis) > 0): ?>
            <?php foreach ($liste_avis as $avis): ?>
                <div class="avis-card">
                    <p class="avis-header">
                    <strong><?= htmlspecialchars($avis['nom']) ?></strong> a donné la note de 
                    <span class="avis-note"><?= str_repeat('⭐', $avis['note']) ?></span>
                    </p>
                    <?php if (!empty($avis['commentaire'])): ?>
                    <p class="avis-texte">"<?= nl2br(htmlspecialchars($avis['commentaire'])) ?>"</p>
                    <?php endif; ?>
                    <?php if ($_SESSION['membre_id'] === $avis['membre_id'] || $_SESSION['membre_role'] === 'Admin' || $_SESSION['membre_role'] === 'Modérateur'): ?>
                        <div class="avis-actions">
                            <?php if ($_SESSION['membre_id'] === $avis['membre_id']): ?>
                                <a href="modifier_avis.php?id=<?= $avis['id'] ?>" class="btn-edit btn-small-action">Modifier</a>
                            <?php endif; ?>
                            <form method="POST" class="form-delete-avis" onsubmit="return confirm('Sûr de vouloir supprimer cet avis ?');">
                        <input type="hidden" name="avis_id" value="<?= $avis['id'] ?>">
                        <button type="submit" name="supprimer_avis" class="btn-delete btn-small-action">Supprimer</button>
                         </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Il n'y a pas encore d'avis sur ce livre. Sois le premier !</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'page/footer.php'; ?>