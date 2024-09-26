<?php
include("../config/database.php");

function emprunterMateriel($pdo, $hardware_id, $user_id)
{
    try {
        // Vérifier la disponibilité du matériel
        $check_sql = "SELECT quantity FROM hardware WHERE id = :hardware_id";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([':hardware_id' => $hardware_id]);
        $hardware = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($hardware) {
            // Insérer l'emprunt dans la table reservations_hardware
            $sql = "INSERT INTO reservations_hardware (hardware_id, user, time, quantity) 
                    VALUES (:hardware_id, :user_id, NOW(), 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':hardware_id' => $hardware_id,
                ':user_id' => $user_id
            ]);

            // Rediriger vers la page principale après un emprunt réussi
            header('Location: ../hardware'); 
            exit(); // S'assurer que le script s'arrête ici
        } else {
            // Rediriger si le matériel n'est pas trouvé
            header('Location: ../hardware');
            exit();
        }
    } catch (PDOException $e) {
        // Rediriger en cas d'erreur
        error_log("Erreur lors de l'emprunt du matériel: " . $e->getMessage()); // Journaliser l'erreur
        header('Location: ../hardware.');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['hardware_id']) && isset($_POST['user_id'])) {
        $hardware_id = htmlspecialchars($_POST['hardware_id']);
        $user_id = htmlspecialchars($_POST['user_id']);
        emprunterMateriel($pdo, $hardware_id, $user_id);
    } else {
        // Rediriger si les données sont manquantes
        header('Location: ../hardware');
        exit();
    }
}
?>
