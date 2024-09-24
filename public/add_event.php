<?php
session_start();

// Vérifie si l'utilisateur est connecté et a le rôle de professeur
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Professeur') {
//     header("Location: /views/unauthorized.php");
//     exit();
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un événement</title>
</head>
<body>

    <h1>Ajouter un événement</h1>
    
    <form method="POST" action="add_event.php">
        <label for="name">Nom de l'événement:</label>
        <input type="text" name="name" id="name" required><br><br>

        <label for="color">Couleur de l'événement:</label>
        <input type="text" name="color" id="color" required><br><br>

        <label for="start_time">Heure de début:</label>
        <input type="datetime-local" name="start_time" id="start_time" required><br><br>

        <label for="end_time">Heure de fin:</label>
        <input type="datetime-local" name="end_time" id="end_time" required><br><br>

        <label for="place">Lieu (ID):</label>
        <input type="number" name="place" id="place" required><br><br>

        <label for="room">Salle:</label>
        <input type="text" name="room" id="room" required><br><br>

        <label for="type">Type (ID):</label>
        <input type="number" name="type" id="type" required><br><br>

        <!-- ID du professeur automatiquement basé sur la session -->
        <input type="hidden" name="teacher" value="<?php echo $_SESSION['id_utilisateur']; ?>">

        <input type="submit" value="Ajouter l'événement">
    </form>

</body>
</html>
