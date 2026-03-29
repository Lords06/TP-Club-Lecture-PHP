<?php
session_start();
if (isset($_SESSION['membre_id'])) {
    header("Location: index.php");
    exit;
}
require_once 'configuration/db.php'; 
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';
    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM membres WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $membre = $stmt->fetch(); 
        if ($membre && password_verify($password, $membre['mot_de_passe'])) {
            if ($membre['statut']) {
                $_SESSION['membre_id'] = $membre['id'];
                $_SESSION['membre_nom'] = $membre['nom'];
                $_SESSION['membre_role'] = $membre['role'];
                header("Location: index.php");
                exit;
            } else {
                $erreur = "Votre compte est désactivé.";
            }
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<?php include 'page/header.php'; ?>
<main class="container">
    <h2>Connexion au Club</h2>

    <?php if ($erreur): ?>
        <p class="error-text"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Email :</label><br>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Mot de passe :</label><br>
            <input type="password" name="mot_de_passe" required>
        </div>
        <button type="submit" class="btn-primary btn-submit">Se connecter</button>
    </form>
    <p class="register-prompt">Pas encore membre ? <a href="register.php">Inscrivez-vous ici</a>.</p>
</main>

<?php include 'page/footer.php'; ?>