<?php
session_start();
require_once 'configuration/db.php';
$erreur = '';
$succes = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';
    if (empty($nom) || empty($email) || empty($password)) {
        $erreur = "Remplissez toutes les cases d'abord.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM membres WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $erreur = "Cet email est déjà utilisé. Veuillez vous connecter.";
        } else {
        $insert = $pdo->prepare("INSERT INTO membres (nom, email, mot_de_passe) VALUES (:nom, :email, :mdp)");
        $insert->execute([
            'nom' => $nom,
            'email' => $email,
            'mdp' => password_hash($password, PASSWORD_DEFAULT)
            ]);
            $succes = "Inscription réussie";
        }
    }
}
?>
<?php include 'page/header.php'; ?>
<main class="container">
    <h2>Inscription au Club de Lecture</h2>

    <?php if ($erreur): ?>
        <p class="error-text"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <?php if ($succes): ?>
        <p class="success-text"><?= htmlspecialchars($succes) ?></p>
        <a href="login.php" class="btn-primary btn-submit" style="text-decoration: none; display: inline-block;">Aller à la connexion</a>
    <?php else: ?>
        <form method="POST">
     <div class="form-group">
         <label>Nom d'utilisateur :</label><br>
         <input type="text" name="nom" required>
     </div> 
     <div class="form-group">
             <label>Adresse Email :</label><br>
            <input type="email" name="email" required>
            </div>
         <div class="form-group">
            <label>Mot de passe :</label><br>
            <input type="password" name="mot_de_passe" required>
            </div>
      <button type="submit" class="btn-primary btn-submit">S'inscrire</button>
        </form>
        <p class="register-prompt">Déjà membre ? <a href="login.php">Connectez-vous ici</a>.</p>
    <?php endif; ?>
</main>

<?php include 'page/footer.php'; ?>