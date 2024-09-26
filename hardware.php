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
    <link rel="stylesheet" href="public/css/hardware.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>
<?php

error_reporting(E_ALL);

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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
function afficherMaterielList($pdo)
{
    // Requête SQL pour récupérer les informations de la table hardware qui ne sont pas empruntés, triés par catégorie
    $sql = "
        SELECT h.name, h.description, h.category, h.quantity, h.location, h.purchase_date 
        FROM hardware h
        LEFT JOIN reservations_hardware r ON h.id = r.hardware_id
        WHERE r.hardware_id IS NULL
        ORDER BY h.category
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $currentCategory = null;

    // Boucle sur les résultats
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Si la catégorie change, afficher un nouveau titre de catégorie
        if ($currentCategory !== $row['category']) {
            $currentCategory = $row['category'];
            echo '<h3 class="category_title">' . htmlspecialchars($currentCategory) . '</h3>';
        }

        // Affichage du matériel sous la catégorie courante
        echo '<div class="content_left_hardware_list_box">';
        echo '    <div class="content_left_hardware_list_box_header">';
        echo '        <div class="content_left_hardware_list_box_header_title">' . htmlspecialchars($row['name']) . '</div>';
        echo '    </div>';
        echo '    <div class="content_left_hardware_list_description">';
        echo '        ' . htmlspecialchars($row['description']);
        echo '    </div>';
        echo '</div>';
    }
}

function afficherMaterielEmprunte($pdo)
{
    // Requête SQL pour récupérer les informations du matériel emprunté et les détails de l'utilisateur
    $sql = "
        SELECT 
            h.id AS hardware_id,  
            h.name AS hardware_name, 
            h.description, 
            h.category, 
            r.quantity, 
            h.location, 
            h.purchase_date, 
            r.time AS emprunt_date,
            r.user AS user_id,      
            u.nom, 
            u.prenom 
        FROM hardware h
        JOIN reservations_hardware r ON h.id = r.hardware_id
        JOIN users u ON r.user = u.id  
        WHERE r.quantity > 0
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Boucle sur les résultats
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="content_right_hardware_list_box">';
        echo '    <div class="content_right_hardware_list_box_header">';
        echo '        <div class="content_right_hardware_list_box_header_title">' . htmlspecialchars($row['hardware_name']) . '</div>';
        // echo '        <div class="content_right_hardware_list_box_header_quantity">' . htmlspecialchars($row['quantity']) . '</div>';
        echo '    </div>';
        echo '    <div class="content_right_hardware_list_description">';
        echo '        ' . htmlspecialchars($row['description']);
        echo '    </div>';
        echo '    <div class="content_right_hardware_list_category">';
        echo '        <strong>Catégorie : </strong>' . htmlspecialchars($row['category']);
        echo '    </div>';
        echo '    <div class="content_right_hardware_list_emprunt_date">';
        echo '        <strong>Date d\'emprunt : </strong>' . htmlspecialchars($row['emprunt_date']);
        echo '    </div>';
        echo '    <div class="content_right_hardware_list_user">';
        echo '        <strong>Emprunté par : </strong>' . htmlspecialchars($row['prenom']) . ' ' . htmlspecialchars($row['nom']);
        echo '    </div>';
        echo '    <form action="controller/return_material_controller.php" method="post" style="display:inline;">';
        echo '        <input type="hidden" name="hardware_id" value="' . htmlspecialchars($row['hardware_id']) . '">'; // Utiliser l'alias
        echo '        <input type="hidden" name="user_id" value="' . htmlspecialchars($row['user_id']) . '">'; // Utiliser l'alias
        echo '        <button type="submit" class="content_right_hardware_return">Rendre</button>';
        echo '    </form>';
        echo '</div>';
    }
}

function afficherFormulaireEmprunt($pdo)
{
    // Requête SQL pour récupérer la liste du matériel avec catégorie
    $sql_hardware = "SELECT h.id, h.name, h.category 
                     FROM hardware h 
                     WHERE h.id NOT IN (SELECT r.hardware_id FROM reservations_hardware r)
                     ORDER BY h.category";
    $stmt_hardware = $pdo->prepare($sql_hardware);
    $stmt_hardware->execute();

    // Requête SQL pour récupérer la liste des utilisateurs
    $sql_users = "SELECT id, prenom, nom FROM users";
    $stmt_users = $pdo->prepare($sql_users);
    $stmt_users->execute();

    // Structure pour stocker les matériels par catégorie
    $hardware_by_category = [];

    // Remplissage de la structure avec les matériels
    while ($row = $stmt_hardware->fetch(PDO::FETCH_ASSOC)) {
        $category = $row['category'];
        $hardware_by_category[$category][] = $row;
    }

    echo '<div class="content_center_emprunt_form">';
    echo '    <h2 class="content_center_form_title">Formulaire d\'emprunt</h2>';
    echo '    <form class="content_center_form" action="controller/emprunt_material_controller.php" method="post">';

    // Champ pour sélectionner le matériel
    echo '        <div class="form_group">';
    echo '            <label class="content_center_form_label" for="hardware_id"><strong>Sélectionnez le Matériel :</strong></label>';
    echo '<br>';
    echo '            <select class="content_center_form_select" name="hardware_id" id="hardware_id" required>';

    // Affichage des optgroups pour chaque catégorie
    foreach ($hardware_by_category as $category => $hardwares) {
        echo '                <optgroup label="' . htmlspecialchars($category) . '">';
        foreach ($hardwares as $hardware) {
            echo '                    <option value="' . htmlspecialchars($hardware['id']) . '">' . htmlspecialchars($hardware['name']) . '</option>';
        }
        echo '                </optgroup>';
    }

    echo '            </select>';
    echo '        </div>';
    echo '        <div class="form_group">';
    echo '            <label class="content_center_form_label" for="user_id"><strong>Sélectionnez l\'Utilisateur :</strong></label>';
    echo '<br>';
    echo '            <select class="content_center_form_select" name="user_id" id="user_id" required>';

    while ($row = $stmt_users->fetch(PDO::FETCH_ASSOC)) {
        echo '                <option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['prenom'] . ' ' . $row['nom']) . '</option>';
    }
    echo '            </select>';
    echo '        </div>';
    echo '<br>';
    echo '        <div class="form_group">';
    echo '            <button class="content_center_form_submit" type="submit">Emprunter</button>';
    echo '        </div>';
    echo '    </form>';
}



?>

<body>
    <div class="header_navigation">
        <!-- <div class="box_button_left"><img class="logo_epsi" src="public/img/logo.png" alt=""></div> -->
        <div class="box_button_left"><a href="/index.php"><img class="logo_epsi" src="public/img/epsi.svg" alt=""></a></div>
        <div class="box_semaine_spinner">

        </div>
        <div class="box_button_right">
            <button class="btn btn-outline-light d-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#myModal">

                <i class="bi bi-person-circle"></i>
            </button>
        </div>
    </div>



    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
                        echo ($_SESSION['prenom'] . ' ' . $_SESSION['nom']);
                    } else {
                        echo "l'utilisateur";
                    } ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Rôle : <?= $_SESSION['role'] ?></h6>
                    <h6>Email : <?= $_SESSION['pseudo_user'] ?></h6>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Retour</button> -->
                    <a href="controller/disconect_controller.php" class="disconnect_a"><button
                            class="disconnect_button">Déconnexion</button></a>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="content_left">
            <div class="content_left_hardware_list">
                <h3 class="content_left_hardware_list_title">Liste du matériel disponible</h3>
                <div class="content_left_hardware_box">
                    <?php afficherMaterielList($pdo); ?>
                </div>
            </div>
        </div>
        <div class="content_center">
            <?php afficherFormulaireEmprunt($pdo); ?>
            <div class="content_center_chart">

                <canvas id="materielChart"></canvas>

                <script>
                    const ctx = document.getElementById('materielChart').getContext('2d');
                    const categories = <?php echo json_encode($categories); ?>;
                    const emprunte = <?php echo json_encode($emprunte); ?>;
                    const disponible = <?php echo json_encode($disponible); ?>;

                    const data = {
                        labels: categories,
                        datasets: [
                            {
                                label: 'Emprunté',
                                data: emprunte,
                                backgroundColor: '#da4759',
                            },
                            {
                                label: 'Disponible',
                                data: disponible,
                                backgroundColor: '#fad053cb', 
                            },
                        ]
                    };

                    const config = {
                        type: 'bar',
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    stacked: true,
                                    title: {
                                        display: true,
                                        text: 'Catégories'
                                    }
                                },
                                y: {
                                    stacked: true,
                                    title: {
                                        display: true,
                                        text: 'Quantité'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Statistiques du Matériel',
                                    font: {
                                        size: 20
                                    }
                                }
                            }
                        },
                    };

                    const materielChart = new Chart(ctx, config);
                </script>
            </div>


        </div>
    </div>
    <div class="content_right">
        <div class="content_right_hardware_list">
            <h3 class="content_right_hardware_list_title">Liste du matériel emprunté</h3>
            <div class="content_right_hardware_box">
                <?php afficherMaterielEmprunte($pdo); ?>
            </div>
        </div>
    </div>
    </div>
</body>