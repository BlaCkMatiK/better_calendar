<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Better Calendar</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="public/css/root.css">
    <link rel="stylesheet" href="public/css/rooms.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<?php


// ---------------------------------------------------------
// Sécuriser les cookies de session
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
session_start();
include 'config/database.php';

if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] != true) {
    header('Location: main');
}

function getMaterielStats($pdo)
{
    $sql = "
        SELECT 
            h.category,
            SUM(CASE WHEN rh.hardware_id IS NOT NULL THEN 1 ELSE 0 END) AS emprunte,
            COUNT(h.id) AS total
        FROM 
            hardware h
        LEFT JOIN 
            reservations_hardware rh ON h.id = rh.hardware_id
        GROUP BY 
            h.category
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$stats = getMaterielStats($pdo);
$categories = [];
$emprunte = [];
$disponible = [];

foreach ($stats as $row) {
    $categories[] = $row['category'];
    $emprunte[] = $row['emprunte'];
    $disponible[] = $row['total'] - $row['emprunte'];
}
function afficherRoomList($pdo)
{
    // Requête SQL pour récupérer les informations des salles qui ne sont pas réservées, triées par capacité
    $sql = "
        SELECT r.room_number, r.informations, r.capacity, r.creation_date
        FROM rooms r
        LEFT JOIN reservations_room rr ON r.id = rr.id
        WHERE rr.id IS NULL
        ORDER BY r.capacity
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $currentCapacity = null;

    // Boucle sur les résultats
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Affichage des informations des salles
        echo '<div class="content_left_rooms_list_box">';
        echo '    <div class="content_left_rooms_list_box_header">';
        echo '        <div class="content_left_rooms_list_box_header_title">Salle ' . htmlspecialchars($row['room_number']) . '</div>';
        echo '    </div>';
        echo '    <div class="content_left_rooms_list_description">';
        echo '        ' . htmlspecialchars($row['informations']);
        echo '    </div>';
        echo '    <div class="content_left_rooms_list_capacity">';
        echo '        Capacité: ' . htmlspecialchars($row['capacity']);
        echo '    </div>';
        echo '</div>';
    }
}


function afficherRoomEmpruntee($pdo)
{
    // Requête SQL pour récupérer les informations des salles empruntées et les détails de l'utilisateur
    $sql = "
        SELECT 
            rr.id AS reservation_id,  
            r.id AS room_id,  -- Ajout de l'id de la salle pour la libération
            r.room_number, 
            r.informations, 
            r.capacity, 
            rr.time AS emprunt_date,
            rr.user AS user_id,      
            u.nom, 
            u.prenom 
        FROM 
            reservations_room rr
        JOIN 
            rooms r ON r.id = rr.room  -- Assurez-vous que 'room' correspond à la colonne dans reservations_room
        JOIN 
            users u ON rr.user = u.id  
        WHERE 
            rr.time IS NOT NULL
        ORDER BY 
            rr.time;  -- Tri par date d'emprunt
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Boucle sur les résultats
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="content_right_rooms_list_box">';
        echo '    <div class="content_right_rooms_list_box_header">';
        echo '        <div class="content_right_rooms_list_box_header_title">Salle ' . htmlspecialchars($row['room_number']) . '</div>';
        echo '    </div>';
        echo '    <div class="content_right_rooms_list_capacity">';
        echo '        <strong>Capacité : </strong>' . htmlspecialchars($row['capacity']);
        echo '    </div>';
        echo '    <div class="content_right_rooms_list_emprunt_date">';
        echo '        <strong>Date de réservation : </strong>' . htmlspecialchars($row['emprunt_date']);
        echo '    </div>';
        echo '    <div class="content_right_rooms_list_user">';
        echo '        <strong>Réservée par : </strong>' . htmlspecialchars($row['prenom']) . ' ' . htmlspecialchars($row['nom']);
        echo '    </div>';
        echo '    <form action="controller/return_rooms_controller.php" method="post" style="display:inline;">';
        echo '        <input type="hidden" name="room_id" value="' . htmlspecialchars($row['room_id']) . '">'; // Utiliser l'ID de la salle
        echo '        <input type="hidden" name="user_id" value="' . htmlspecialchars($row['user_id']) . '">'; // Utiliser l'ID de l'utilisateur
        echo '        <button type="submit" class="content_right_rooms_return">Libérer</button>';
        echo '    </form>';
        echo '</div>';
    }
}


function afficherFormulaireReservationSalle($pdo)
{
    // Requête SQL pour récupérer la liste des salles qui ne sont pas encore réservées
    $sql_rooms = "
    SELECT r.id, r.room_number, r.capacity 
    FROM rooms r 
    WHERE r.id NOT IN (SELECT rr.room FROM reservations_room rr)
    ORDER BY r.room_number
";
    $stmt_rooms = $pdo->prepare($sql_rooms);
    $stmt_rooms->execute();

    // Requête SQL pour récupérer la liste des utilisateurs
    $sql_users = "SELECT id, prenom, nom FROM users ";
    $stmt_users = $pdo->prepare($sql_users);
    $stmt_users->execute();

    echo '<div class="content_center_reservation_form">';
    echo '    <h2 class="content_center_form_title">Réservation de Salle</h2>';
    echo '    <form class="content_center_form" action="controller/reservation_room_controller.php" method="post">';

    // Champ pour sélectionner la salle
    echo '        <div class="form_group">';
    echo '            <label class="content_center_form_label" for="room_id"><strong>Sélectionnez la Salle :</strong></label><br>';
    echo '            <select class="content_center_form_select" name="room_id" id="room_id" required>';

    // Affichage des salles sans tri par capacité
    while ($row = $stmt_rooms->fetch(PDO::FETCH_ASSOC)) {
        echo '                <option value="' . htmlspecialchars($row['id']) . '">Salle : ' . htmlspecialchars($row['room_number']) . ' - Capacitée :' . htmlspecialchars($row['capacity']) . '</option>';
    }

    echo '            </select>';
    echo '        </div>';

    // Champ pour sélectionner l'utilisateur
    echo '        <div class="form_group">';
    echo '            <label class="content_center_form_label" for="user_id"><strong>Sélectionnez l\'Utilisateur :</strong></label><br>';
    echo '            <select class="content_center_form_select" name="user_id" id="user_id" required>';

    // Affichage des utilisateurs
    while ($row = $stmt_users->fetch(PDO::FETCH_ASSOC)) {
        echo '                <option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['prenom'] . ' ' . $row['nom']) . '</option>';
    }
    echo '            </select>';
    echo '        </div>';

    echo '        <div class="form_group">';
    echo '            <button class="content_center_form_submit" type="submit">Réserver</button>';
    echo '        </div>';
    echo '    </form>';
    echo '</div>';
}

?>

<body>
    <div class="header_navigation">
        <div class="box_button_left"><a href="/index"><img class="logo_epsi" src="public/img/epsi.svg" alt=""></a>
        </div>
        <div class="box_semaine_spinner">

        </div>
        <div class="box_button_right">
            <button class="btn btn-outline-light d-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#Modal">
                <i class="bi bi-person-circle"></i>
            </button>
            <a href="/"><button class="btn btn-outline-light d-flex align-items-center">
                    <i class="bi bi-house"></i>
                </button></a>
        </div>
    </div>
    <div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <?php
                        if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
                            echo ($_SESSION['prenom'] . ' ' . $_SESSION['nom']);
                        } else {
                            echo "l'utilisateur";
                        }
                        ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div class="modal-body">
                    <h6>Rôle : <?= $_SESSION['role'] ?></h6>
                    <h6>Email : <?= $_SESSION['pseudo_user'] ?></h6>
                </div>
                <div class="modal-footer">
                    <a href="controller/disconect_controller.php" class="disconnect_a"><button
                            class="disconnect_button">Déconnexion</button></a>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="content_left">
            <div class="content_left_rooms_list">
                <h3 class="content_left_rooms_list_title">Liste des salles disponibles</h3>
                <div class="content_left_rooms_box">
                    <?php afficherRoomList($pdo); ?>
                </div>
            </div>
        </div>
        <div class="content_center">
            <?php afficherFormulaireReservationSalle($pdo); ?>

        </div>
        <div class="content_right">
            <div class="content_right_rooms_list">
                <h3 class="content_right_rooms_list_title">Liste des salles réservées</h3>
                <div class="content_right_rooms_box">
                    <?php afficherRoomEmpruntee($pdo); ?>
                </div>
            </div>
        </div>
    </div>
</body>