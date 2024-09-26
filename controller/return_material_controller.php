<?php

include("../config/database.php");

function rendreMateriel($pdo, $hardware_id, $user_id)
{
    $sql = "DELETE FROM reservations_hardware WHERE hardware_id = :hardware_id AND user = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':hardware_id' => $hardware_id,
        ':user_id' => $user_id
    ]);

    header('Location: ../hardware');
    exit();
}

// Vérifier si la requête est une POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hardware_id = $_POST['hardware_id'];
    $user_id = $_POST['user_id'];
    rendreMateriel($pdo, $hardware_id, $user_id);
}
?>