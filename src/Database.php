<?php

class Database {
    private $pdo;

    public function __construct($config) {
        try {
            $this->pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']}",
                $config['username'],
                $config['password']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>