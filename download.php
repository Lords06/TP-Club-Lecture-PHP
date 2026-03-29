<?php
session_start();
require_once 'configuration/db.php';
if (!isset($_SESSION['membre_id']) || !isset($_GET['id'])) {
    die("Accès refusé.");
}

$doc_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT nom_fichier, chemin_fichier, type_mime, taille FROM documents WHERE id = ?");
$stmt->execute([$doc_id]);
$doc = $stmt->fetch();
if ($doc && file_exists($doc['chemin_fichier'])) {
    header('Content-Type: ' . $doc['type_mime']);
    header('Content-Disposition: attachment; filename="' . basename($doc['nom_fichier']) . '"');
    header('Content-Length: ' . $doc['taille']);
    readfile($doc['chemin_fichier']);
    exit;
} else {
    die("Désolé, ce fichier est introuvable.");
}
?>