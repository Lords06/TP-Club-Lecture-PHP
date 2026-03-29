<?php
session_start();
require_once 'configuration/db.php';
if (!isset($_SESSION['membre_id']) || $_SESSION['membre_role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}
$succes = '';
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_role'])) {
    $id_cible = (int)$_POST['id_membre'];
    $nouveau_role = $_POST['nouveau_role'];
    if ($id_cible === (int)$_SESSION['membre_id']) {
        $erreur = "Tu ne peux pas modifier ton propre rôle !";
    } else {
        $update = $pdo->prepare("UPDATE membres SET role = :role WHERE id = :id");
        $update->execute([
            'role' => $nouveau_role,
            'id' => $id_cible
        ]);
        $succes = "Le rôle a bien été mis à jour ";
    }
}
$stmt = $pdo->query("SELECT id, nom, email, role, statut, cree_le FROM membres ORDER BY nom ASC");
$membres = $stmt->fetchAll();
?>

<?php include 'page/header.php'; ?>

<main class="container">
    <h2>Gestion des Membres et Rôles</h2>
    <?php if ($erreur): ?>
        <p class="error-text"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <?php if ($succes): ?>
        <p class="success-text"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>
    <div class="table-responsive">
        <table class="admin-table">
      <thead>
          <tr>
             <th>Nom</th>
             <th>Email</th>
             <th>Date d'inscription</th>
             <th>Rôle actuel</th>
             <th>Action</th>
         </tr>
     </thead>
            <tbody>
                <?php foreach ($membres as $membre): ?>
                    <tr>
                    <td><strong><?= htmlspecialchars($membre['nom']) ?></strong></td>
                    <td><?= htmlspecialchars($membre['email']) ?></td>
                    <td><?= date('d/m/Y', strtotime($membre['cree_le'])) ?></td>
                    <td>
                        <span class="badge-role badge-<?= strtolower($membre['role']) ?>">
                         <?= htmlspecialchars($membre['role']) ?>
                        </span>
                        </td>
<td>
    <?php if ($membre['id'] !== $_SESSION['membre_id']): ?>
        <form method="POST" class="form-role">
        <input type="hidden" name="id_membre" value="<?= $membre['id'] ?>">
        <select name="nouveau_role" class="form-select-small">
        <option value="Membre" <?= $membre['role'] === 'Membre' ? 'selected' : '' ?>>Membre</option>
        <option value="Modérateur" <?= $membre['role'] === 'Modérateur' ? 'selected' : '' ?>>Modérateur</option>
        <option value="Admin" <?= $membre['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
            </select>
                 <button type="submit" name="changer_role" class="btn-primary btn-small">Mettre à jour</button>
            </form>
             <?php else: ?>
             <span>C'est toi </span>
             <?php endif; ?>
                </td>
                </tr>
             <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'page/footer.php'; ?>