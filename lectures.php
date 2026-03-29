<?php
session_start();
require_once 'configuration/db.php';

if (!isset($_SESSION['membre_id'])) {
    header("Location: login.php");
    exit;
}
$stmt = $pdo->query("
    SELECT livres.*, ROUND(AVG(progression.pourcentage)) as moyenne_progression 
    FROM livres 
    LEFT JOIN progression ON livres.id = progression.livre_id 
    GROUP BY livres.id 
    ORDER BY livres.id DESC
");
$livres = $stmt->fetchAll();
?>

<?php include 'page/header.php'; ?>

<main class="container">
    <h2>La Bibliothèque du Club</h2>

    <?php if (count($livres) > 0): ?>
        <div class="livres-grid">
            <?php foreach ($livres as $livre): ?>
                <div class="livre-card">
                    
                    <?php if (!empty($livre['chemin_couverture'])): ?>
                        <img src="<?= htmlspecialchars($livre['chemin_couverture']) ?>" alt="Couverture" class="livre-couverture">
                    <?php endif; ?>
                    <h3 class="livre-titre"><?= htmlspecialchars($livre['titre']) ?></h3>
                    <p class="livre-auteur">Par <?= htmlspecialchars($livre['auteur']) ?></p>
                    <?php if (!empty($livre['description'])): ?>
                        <p class="livre-description"><?= nl2br(htmlspecialchars($livre['description'])) ?></p>
                    <?php endif; ?>
                    <div class="livre-dates">
                        <?php if (!empty($livre['date_debut'])): ?>
                            <span> Début : <?= htmlspecialchars($livre['date_debut']) ?></span><br>
                        <?php endif; ?>
                        <?php if (!empty($livre['date_fin'])): ?>
                            <span>Fin : <?= htmlspecialchars($livre['date_fin']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php 
                        $moyenne = $livre['moyenne_progression'] !== null ? $livre['moyenne_progression'] : 0; 
                    ?>
                    <div class="mini-progression">
                        <p class="mini-progression-texte">Progression du club : <strong><?= $moyenne ?>%</strong></p>
                        <div class="mini-progress-container">
                            <div class="progress-bar-fill" style="width: <?= $moyenne ?>%;"></div>
                        </div>
                    </div>
                    <div class="livre-actions">
                        <a href="details_livre.php?id=<?= $livre['id'] ?>" class="btn-primary btn-voir">Voir</a>
                        <?php if ($_SESSION['membre_role'] === 'Admin' || $_SESSION['membre_role'] === 'Modérateur'): ?>
                            <a href="modifier_livre.php?id=<?= $livre['id'] ?>" class="btn-edit">Modifier</a>
                            <a href="supprimer_livre.php?id=<?= $livre['id'] ?>" class="btn-delete" onclick="return confirm('Sûr de vouloir supprimer ce livre ?');">Supprimer</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p>Aucun livre n'a encore été ajouté au club. Revenez plus tard </p>
    <?php endif; ?>
    
</main>

<?php include 'page/footer.php'; ?>