<?php
$host = 'localhost';
$dbname = 'club_lecture';
$username = 'root';
$password = ''; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<h3 style='color:red;'>Erreur de connexion : " . $e->getMessage() . "</h3>");
}
?>