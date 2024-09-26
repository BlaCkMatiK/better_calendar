<?php
include("../config/database.php");

function reserveRoom($pdo, $room_id, $user_id)
{
    try {
        $sql = "INSERT INTO reservations_room (user, room, time) 
                    VALUES (:user_id, :room_id, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':room_id' => $room_id
        ]);
        header('Location: ../room');
        exit(); 

    } catch (PDOException $e) {
        header('Location: ../room');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['room_id']) && isset($_POST['user_id'])) {
        $room_id = htmlspecialchars($_POST['room_id']);
        $user_id = htmlspecialchars(string: $_POST['user_id']);
        reserveRoom($pdo, $room_id, $user_id);
    } else {
        header('Location: ../room');
        exit();
    }
}
?>