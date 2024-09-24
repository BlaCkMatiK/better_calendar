<?php
$config = [
    'host' => $_ENV['MYSQL_HOST'],
    'dbname' => 'workshop',
    'username' => $_ENV['MYSQL_USER'], // Remplacez par votre nom d'utilisateur MySQL
    'password' => $_ENV['MYSQL_PASSWORD'], // Remplacez par votre mot de passe MySQL
];

$dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8';

try {
    $pdo = new PDO(dsn: $dsn, username: $config['username'], password: $config['password']);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit; 
}


$db_host = $_ENV['MYSQL_HOST'];
$db_user_user = $_ENV['MYSQL_USER'];
$db_password_user = $_ENV['MYSQL_PASSWORD'];
try {
    $conn_workshop_pdo = new PDO("mysql:host=$db_host;dbname=workshop", $db_user_user, $db_password_user);
    $conn_workshop_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
}
$conn_workshop_mysqli = new mysqli($db_host, $db_user_user, $db_password_user, "workshop");



?>