<?php
session_start();


include_once '../src/Calendar.php';
include_once '../config/database.php';



// Vérifie si l'utilisateur est connecté et a le rôle de professeur
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: /views/unauthorized.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $name = sanitizeInput($_POST['name']);
    $color = sanitizeInput($_POST['color']);
    $start_time = sanitizeInput($_POST['start_time']);
    $end_time = sanitizeInput($_POST['end_time']);
    $place = sanitizeInput($_POST['place']);
    $room = sanitizeInput($_POST['room']);
    $type = sanitizeInput($_POST['type']);
    $role = sanitizeInput($_POST['role']);
    $teacher = sanitizeInput($_POST['teacher']);

    // Requête SQL pour insérer les données
    $sql = "INSERT INTO courses (
        room,
        teacher,
        start_time,
        end_time,
        place,
        type,
        group_id,
        name,
        color
    ) VALUES (
        :room,
        :teacher,
        :start_time,
        :end_time,
        :place,
        :type,
        :group_id,
        :name,
        :color
    )";

    $stmt = $pdo->prepare($sql);

    // Exécution de la requête avec les données du formulaire
    try {
        $stmt->execute([
            ':name' => $name,
            ':color' => $color,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':place' => $place,
            ':room' => $room,
            ':type' => $type,
            ':group_id' => $role,
            ':teacher' => $teacher
        ]);
        

        echo "L'événement a été ajouté avec succès !";
    } catch (PDOException $e) {
        echo $e;
    }

    header("Location: ../week");
    exit();
}
