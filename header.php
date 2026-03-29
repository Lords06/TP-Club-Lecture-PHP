<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club de Lecture</title>
    
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">
</head>
<body>
<header class="navbar">
    <div class="nav-logo">
        <a href="index.php">Club de Lecture</a>
    </div>
    <nav class="nav-menu">
        <?php if (isset($_SESSION['membre_id'])): ?>
            <span class="nav-user">
                <?= htmlspecialchars($_SESSION['membre_nom']) ?> (<?= htmlspecialchars($_SESSION['membre_role']) ?>)
            </span>
            <a href="index.php">Tableau de bord</a>
            <a href="lectures.php">La Bibliothèque</a>
            <a href="sessions.php">Évènements</a>
            
            <?php if ($_SESSION['membre_role'] === 'Admin' || $_SESSION['membre_role'] === 'Modérateur'): ?>
                <a href="ajouter_livre.php">Ajouter un livre</a>
            <?php endif; ?>
            <?php if ($_SESSION['membre_role'] === 'Admin'): ?>
                <a href="gestion_membres.php" class="nav-admin-link"> Gérer les membres</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-nav btn-logout-nav">Déconnexion</a>
            
        <?php else: ?>
            <a href="login.php">Connexion</a>
            <a href="register.php" class="btn-nav btn-register-nav">Inscription</a>
            
        <?php endif; ?>

    </nav>
</header>