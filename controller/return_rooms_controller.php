<?php

include("../config/database.php");

function rendreSalle($pdo, $room_id, $user_id)
{
    // Requête SQL pour supprimer la réservation
    $sql = "DELETE FROM reservations_room WHERE room = :room_id AND user = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':room_id' => $room_id,
        ':user_id' => $user_id
    ]);
    echo $room_id;
    header('Location: ../room');
    exit();
}

// Vérifier si la requête est une POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $user_id = $_POST['user_id'];
    rendreSalle($pdo, $room_id, $user_id);
}
?>