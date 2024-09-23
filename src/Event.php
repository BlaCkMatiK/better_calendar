<?php

class Event {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllEvents() {
        $stmt = $this->db->query('SELECT title, start, end FROM events');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addEvent($title, $start, $end) {
        $stmt = $this->db->prepare('INSERT INTO events (title, start, end) VALUES (:title, :start, :end)');
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':start', $start);
        $stmt->bindParam(':end', $end);
        $stmt->execute();
    }
}
?>