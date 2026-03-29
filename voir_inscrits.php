<?php
session_start();
require_once 'configuration/db.php';
if (!isset($_SESSION['membre_id']) || ($_SESSION['membre_role'] !== 'Admin' && $_SESSION['membre_role'] !== 'Modérateur')) {
    header("Location: sessions.php");
    exit;
}
if (!isset($_GET['id'])) {
    header("Location: sessions.php");
    exit;
}
$session_id = (int)$_GET['id'];
$succes = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maj_statut'])) {
    $membre_id = (int)$_POST['membre_id'];
    $nouveau_statut = $_POST['statut'];
    $update = $pdo->prepare("UPDATE inscriptions_sessions SET statut = :statut WHERE session_id = :session_id AND membre_id = :membre_id");
    $update->execute([
        'statut' => $nouveau_statut,
        'session_id' => $session_id,
        'membre_id' => $membre_id
    ]);
    $succes = "Le statut du participant a été mis à jour ";
}
$stmt_session = $pdo->prepare("SELECT titre, date_heure FROM sessions WHERE id = :id");
$stmt_session->execute(['id' => $session_id]);
$session = $stmt_session->fetch();

if (!$session) {
    header("Location: sessions.php");
    exit;
}
$stmt_inscrits = $pdo->prepare("
    SELECT m.id as membre_id, m.nom, m.email, i.statut, i.cree_le 
    FROM inscriptions_sessions i
    JOIN membres m ON i.membre_id = m.id
    WHERE i.session_id = :session_id
    ORDER BY i.cree_le ASC
");
$stmt_inscrits->execute(['session_id' => $session_id]);
$inscrits = $stmt_inscrits->fetchAll();
?>

<?php include 'page/header.php'; ?>

<main class="container">
    <p class="back-link"><a href="sessions.php">Retour aux évènements</a></p>
    <h2>Inscrits : <?= htmlspecialchars($session['titre']) ?></h2>
    <p class="session-subtitle">
        Prévu le <?= date('d/m/Y à H:i', strtotime($session['date_heure'])) ?> </p>

    <?php if ($succes): ?>
        <p class="success-text"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>
    <?php if (count($inscrits) > 0): ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Participant</th>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                    <th>Présence (Appel)</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($inscrits as $inscrit): ?>
                <tr>
                <td><strong><?= htmlspecialchars($inscrit['nom']) ?></strong></td>
                <td><?= htmlspecialchars($inscrit['email']) ?></td>
                <td><?= date('d/m/Y', strtotime($inscrit['cree_le'])) ?></td>
                <td>
                    <form method="POST" class="form-appel">
                    <input type="hidden" name="membre_id" value="<?= $inscrit['membre_id'] ?>">
                    <select name="statut" class="form-select-small">
                            <option value="Inscrit" <?= $inscrit['statut'] === 'Inscrit' ? 'selected' : '' ?>>Inscrit</option>
                            <option value="Présent" <?= $inscrit['statut'] === 'Présent' ? 'selected' : '' ?>> Présent</option>
                            <option value="Absent" <?= $inscrit['statut'] === 'Absent' ? 'selected' : '' ?>> Absent</option>
                                </select>
                                <button type="submit" name="maj_statut" class="btn-primary btn-small">Valider</button>
                            </form>
                            </td>
                        </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        </div>


        
    <?php else: ?>
        <p>Personne ne s'est encore inscrit à cet évènement.</p>
    <?php endif; ?>
</main>
<?php include 'page/footer.php'; ?>