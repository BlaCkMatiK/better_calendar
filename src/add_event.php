<?php
session_start();

// Vérifie si l'utilisateur est connecté et a le rôle de professeur
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prof') {
    // header("Location: /views/unauthorized.php");
    // exit();
// }

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $name = $_POST['name'];
    $color = $_POST['color'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $place = $_POST['place'];
    $room = $_POST['room'];
    $teacher = $_POST['teacher']; // ID du professeur (récupéré de la session)
    $type = $_POST['type'];

    // Inclure la classe Calendar
    include('Calendar.php');

    // Créer une instance de Calendar
    $calendar = new Calendar();

    // Sauvegarder l'événement dans la base de données
    $calendar->save_event_to_db($name, $color, $start_time, $end_time, $place, $room, $teacher, $type);
}
?>
