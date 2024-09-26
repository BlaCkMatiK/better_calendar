<?php
include("../config/database.php");

function emprunterSalle($pdo, $room_id, $user_id)
{

    try {

        $sql = "INSERT INTO reservations_room (user, room, time) 
                    VALUES (:user_id, :room_id, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':room_id' => $room_id
        ]);
        echo $user_id;
        echo $room_id;
        header('Location: ../room');
        exit(); // S'assurer que le script s'arrête ici

    } catch (PDOException $e) {
        header('Location: ../room');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['room_id']) && isset($_POST['user_id'])) {
        $room_id = htmlspecialchars($_POST['room_id']);
        $user_id = htmlspecialchars($_POST['user_id']);
        emprunterSalle($pdo, $room_id, $user_id);
    } else {
        // Rediriger si les données sont manquantes
        header('Location: ../room');
        exit();
    }
}
?>