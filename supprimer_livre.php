<?php
session_start();
require_once 'configuration/db.php';
if (!isset($_SESSION['membre_id']) || ($_SESSION['membre_role'] !== 'Admin' && $_SESSION['membre_role'] !== 'Modérateur')) {
    header("Location: index.php");
    exit;
}
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $delete = $pdo->prepare("DELETE FROM livres WHERE id = :id");
    $delete->execute(['id' => $id]);
}
header("Location: lectures.php");
exit;
?>