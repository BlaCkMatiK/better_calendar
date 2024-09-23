<?php
$config = [
    'host' => $_ENV['MYSQL_HOST'],
    'dbname' => 'workshop',
    'username' => $_ENV['MYSQL_USER'], // Remplacez par votre nom d'utilisateur MySQL
    'password' => $_ENV['MYSQL_PASSWORD'], // Remplacez par votre mot de passe MySQL
];

$dsn = 'mysql:host' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8';

try {
    $pdo = new PDO(dsn: $dsn, username: $config['username'], password: $config['password']);
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    echo "Erreur de connexion : " . $e->getMessage();
    exit; // Arrêter l'exécution si la connexion échoue
}

?>