<?php
session_start();

var_dump($_POST);
// die();

include '../src/Calendar.php';
include '../config/database.php';

// Vérifie si l'utilisateur est connecté et a le rôle de professeur
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: /views/unauthorized.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $name = $_POST['name'];
    $color = $_POST['color'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $place = $_POST['place'];
    $room = $_POST['room'];
    $type = $_POST['type'];
    $teacher = $_POST['teacher'];

    // Requête SQL pour insérer les données
    $sql = "INSERT INTO courses (room, teacher, start_time, end_time, place, type, name, color) VALUES (:room, :teacher, :start_time, :end_time, :place, :type, :name, :color)";
    
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
            ':teacher' => $teacher
        ]);

        echo "L'événement a été ajouté avec succès !";
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout de l'événement : " . $e->getMessage();
    }

    header("Location: /week.php");
    exit();
}
function generateDropdown(PDO $pdo, string $table, string $selectName): string {
    // Prepare the query
    $query = "SELECT id, label FROM $table";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Start building the HTML for the dropdown
    $dropdown = "<select name=\"$selectName\">\n";

    // Loop through each row and create an option element
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dropdown .= '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['label']) . "</option>\n";
    }

    // Close the select tag
    $dropdown .= "</select>\n";

    return $dropdown;
}

// Example usage in a form
?>

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un cours</title>
</head>
<body>

    <h1>Ajouter un cours</h1>
    
    <form method="POST" action="add_event.php">
        <label for="name">Nom du Cours:</label>
        <input type="text" name="name" id="name" required><br><br>

        <label for="color">Couleur du cours:</label>
        <input type="text" name="color" id="color" required><br><br>

        <label for="start_time">Heure de début:</label>
        <input type="datetime-local" name="start_time" id="start_time" required><br><br>

        <label for="end_time">Heure de fin:</label>
        <input type="datetime-local" name="end_time" id="end_time" required><br><br>

        <label for="place">Lieu (ID):</label>
        <?php
    // Call the function to generate the dropdown
    echo generateDropdown($pdo, 'places', 'Choisir un lieu');
    ?>

        <label for="room">Salle:</label>
        <input type="text" name="room" id="room" required><br><br>

        <label for="teacher">Prof:</label>
        <input type="teacher" name="teacher" id="teacher" required><br><br>

        <label for="type">Type (ID):</label>
        <input type="number" name="type" id="type" required><br><br>

        <!-- ID du professeur automatiquement basé sur la session -->

        <input type="submit" value="Ajouter le cours">
    </form>

</body>
</html>
