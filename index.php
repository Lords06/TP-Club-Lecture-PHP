<?php
session_start();
require_once 'configuration/db.php';

if (!isset($_SESSION['membre_id'])) {
    header("Location: login.php");
    exit;
}


$stmt_livres = $pdo->query("SELECT COUNT(*) FROM livres");
$total_livres = $stmt_livres->fetchColumn();

$stmt_membres = $pdo->query("SELECT COUNT(*) FROM membres");
$total_membres = $stmt_membres->fetchColumn();
$stmt_avis = $pdo->query("SELECT COUNT(*) FROM avis");
$total_avis = $stmt_avis->fetchColumn();

$stmt_ma_prog = $pdo->prepare("SELECT AVG(pourcentage) FROM progression WHERE membre_id = :membre_id");
$stmt_ma_prog->execute(['membre_id' => $_SESSION['membre_id']]);
$ma_moyenne = $stmt_ma_prog->fetchColumn();
$ma_moyenne = $ma_moyenne !== null ? round((float)$ma_moyenne) : 0;
$stmt_derniers = $pdo->query("SELECT id, titre, auteur FROM livres ORDER BY id DESC LIMIT 3");
$derniers_livres = $stmt_derniers->fetchAll();
?>

<?php include 'page/header.php'; ?>

<main class="container">
    <div class="dashboard-header">
        <h2> Bienvenue, <?= htmlspecialchars($_SESSION['membre_nom']) ?> !</h2>
        <p class="dashboard-subtitle">Voici un résumé de l'activité du Club de Lecture.</p>
    </div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-info">
                <h3><?= $total_livres ?></h3>
                <p>Livres au total</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-info">
                <h3><?= $total_membres ?></h3>
                <p>Membres inscrits</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-info">
                <h3><?= $total_avis ?></h3>
                <p>Avis partagés</p>
            </div>
        </div>
        <div class="stat-card stat-card-highlight">
            <div class="stat-icon"></div>
            <div class="stat-info">
                <h3><?= $ma_moyenne ?> %</h3>
                <p>Ma progression moyenne</p>
            </div>
        </div>

    </div>

    <div class="dashboard-bottom">
        
        <div class="dashboard-panel">
            <h3>Nouveautés dans la bibliothèque</h3>
            <?php if (count($derniers_livres) > 0): ?>
                <ul class="recent-list">
                    <?php foreach ($derniers_livres as $livre): ?>
                        <li>
                            <span class="recent-title"><?= htmlspecialchars($livre['titre']) ?></span> 
                            <span class="recent-author">par <?= htmlspecialchars($livre['auteur']) ?></span>
                            <a href="details_livre.php?id=<?= $livre['id'] ?>" class="recent-link">Voir </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun livre pour le moment.</p>
            <?php endif; ?>
        </div>
        <div class="dashboard-panel">
            <h3>Actions rapides</h3>
            <div class="quick-actions">
                <a href="lectures.php" class="btn-primary btn-block">Parcourir la bibliothèque</a>
                <a href="sessions.php" class="btn-primary btn-block" style="background-color: #9b59b6;">Voir les évènements</a>
                
                <?php if ($_SESSION['membre_role'] === 'Admin' || $_SESSION['membre_role'] === 'Modérateur'): ?>
                    <a href="ajouter_livre.php" class="btn-primary btn-block" style="background-color: #f39c12;">Ajouter un livre</a>
                <?php endif; ?>
            </div>
        </div>

    </div>

</main>

<?php include 'page/footer.php'; ?>