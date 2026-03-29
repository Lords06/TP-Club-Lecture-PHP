<?php
session_start();
require_once 'configuration/db.php';

if (!isset($_SESSION['membre_id'])) {
    header("Location: login.php");
    exit;
}
$succes = '';
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_inscription'])) {
    $session_id = (int)$_POST['session_id'];
    $membre_id = $_SESSION['membre_id'];
    $action = $_POST['action_inscription'];
    if ($action === 'inscrire') {
        $insert = $pdo->prepare("INSERT IGNORE INTO inscriptions_sessions (session_id, membre_id) VALUES (:session_id, :membre_id)");
        $insert->execute(['session_id' => $session_id, 'membre_id' => $membre_id]);
        $succes = "Tu es bien inscrit(e) à cet évènement ";
    } elseif ($action === 'desinscrire') {
        $delete = $pdo->prepare("DELETE FROM inscriptions_sessions WHERE session_id = :session_id AND membre_id = :membre_id");
        $delete->execute(['session_id' => $session_id, 'membre_id' => $membre_id]);
        $succes = "Tu as bien été désinscrit(e).";
    }
}
$stmt = $pdo->query("
    SELECT s.*, l.titre as livre_titre, 
           (SELECT COUNT(*) FROM inscriptions_sessions WHERE session_id = s.id) as nb_inscrits
    FROM sessions s
    LEFT JOIN livres l ON s.livre_id = l.id
    ORDER BY s.date_heure ASC
");
$sessions = $stmt->fetchAll();
$stmt_mes_inscriptions = $pdo->prepare("SELECT session_id FROM inscriptions_sessions WHERE membre_id = :membre_id");
$stmt_mes_inscriptions->execute(['membre_id' => $_SESSION['membre_id']]);
$mes_inscriptions = $stmt_mes_inscriptions->fetchAll(PDO::FETCH_COLUMN);
?>
<?php include 'page/header.php'; ?>
<main class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Les Évènements du Club</h2>
        <?php if ($_SESSION['membre_role'] === 'Admin' || $_SESSION['membre_role'] === 'Modérateur'): ?>
            <a href="ajouter_session.php" class="btn-primary" style="text-decoration: none; border-radius: 4px; padding: 10px 15px;">Créer une session</a>
        <?php endif; ?>
    </div>
    <?php if ($erreur): ?><p class="error-text"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>
    <?php if ($succes): ?><p class="success-text"><?= htmlspecialchars($succes) ?></p><?php endif; ?>
    <?php if (count($sessions) > 0): ?>
        <div class="sessions-grid">
            <?php foreach ($sessions as $session): ?>
                <div class="session-card">
                    <h3><?= htmlspecialchars($session['titre']) ?></h3>
                    <?php if ($session['livre_titre']): ?>
                        <p class="session-livre">Livre : <strong><?= htmlspecialchars($session['livre_titre']) ?></strong></p>
                    <?php endif; ?>
                    <p class="session-date">Date : <?= date('d/m/Y à H:i', strtotime($session['date_heure'])) ?></p>
                    <p class="session-lieu">Lieu/Lien : <?= htmlspecialchars($session['lieu'] ?? $session['lien'] ?? 'À définir') ?></p>
                    <p class="session-desc"><?= nl2br(htmlspecialchars($session['description'] ?? 'Aucune description fournie.')) ?></p>
                    <div class="session-footer">
                        <span class="session-inscrits"> <?= $session['nb_inscrits'] ?> inscrit(s)</span>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                            <?php if (in_array($session['id'], $mes_inscriptions)): ?>
                            <button type="submit" name="action_inscription" value="desinscrire" class="btn-delete" style="border: none; cursor: pointer;">Se désinscrire</button>
                            <?php else: ?>
                            <button type="submit" name="action_inscription" value="inscrire" class="btn-primary" style="border: none; cursor: pointer; padding: 8px 15px; border-radius: 4px;">S'inscrire</button>
                            <?php endif; ?>
                        </form>
                    </div>
                    <?php if ($_SESSION['membre_role'] === 'Admin' || $_SESSION['membre_role'] === 'Modérateur'): ?>
                        <div class="session-admin-actions">
                            <a href="voir_inscrits.php?id=<?= $session['id'] ?>" class="btn-voir-participants">Voir les participants</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune session n'est prévue pour le moment. Repasse plus tard !</p>
    <?php endif; ?>
</main>

<?php include 'page/footer.php'; ?>