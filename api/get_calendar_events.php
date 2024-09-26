<?php
header('Content-Type: application/json');
require_once 'config/database.php';

// Vérification de l'en-tête Authorization
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$token = str_replace('Bearer ', '', $headers['Authorization']);

// Vérification du token dans la base de données
$stmt = $pdo->prepare("SELECT id FROM users WHERE api_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Continuation pour récupérer les événements
if (isset($_GET['semaine'])) {
    $semaine = (int)$_GET['semaine'];
    $annee = date('Y');
    $date_debut = new DateTime();
    $date_debut->setISODate($annee, $semaine);
    $date_fin = clone $date_debut;
    $date_fin->modify('+6 days');

    // Requête pour récupérer les événements entre les dates spécifiées
    $stmt = $pdo->prepare("
        SELECT courses.*, teachers.lastname, places.name AS lieu, types.name AS type
        FROM courses
        LEFT JOIN teachers ON courses.teacher = teachers.id
        LEFT JOIN places ON courses.place = places.id
        LEFT JOIN types ON courses.type = types.id
        WHERE start_time BETWEEN ? AND ?
    ");
    $stmt->execute([$date_debut->format('Y-m-d'), $date_fin->format('Y-m-d')]);

    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $events[] = [
            'name' => $row['name'],
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'color' => $row['color'],
            'room' => $row['room'],
            'teacher' => $row['lastname'],
            'place' => $row['lieu'],
            'type' => $row['type']
        ];
    }

    echo json_encode($events);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing week parameter']);
}
?>
