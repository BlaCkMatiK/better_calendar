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
    <link rel="shortcut icon" href="/public/img/favicon-32x32.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="public/css/root.css">
    <link rel="stylesheet" href="public/css/select_week.css">
    <link href="public/css/style.css" rel="stylesheet" type="text/css">
    <link href="public/css/calendar_month.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<?php
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
session_start();

if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] != true) {
    header('Location: main');
}

if (!isset($_SESSION['semaine_selectionnee'])) {
    $date = new DateTime();
    $_SESSION['semaine_selectionnee'] = $date->format("W"); // Numéro de la semaine actuelle
}

if (!isset($_SESSION['mois_selectionnee'])) {
    $date = new DateTime();
    $_SESSION['mois_selectionnee'] = $date->format("n"); // Numéro du mois actuel (1 à 12)
}

if (!isset($_SESSION['view_method'])) {
    $_SESSION['view_method'] = "week";
}

if (!isset($_SESSION['date_selectionnee'])) {
    $_SESSION['date_selectionnee'] = date('Y-m-d');
}
function generateDropdown(PDO $pdo, string $label, string $table, string $column, string $selectName): string
{
    $query = 'SELECT id, ' . $column . ' FROM ' . $table;
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $dropdown = '<label for="' . $selectName . '" class="form-label">' . htmlspecialchars($label) . '</label>';

    $dropdown .= "<select name=\"$selectName\" class=\"form-input\">\n";
    $dropdown .= "<option value=''> Choisir " . htmlspecialchars($label) . "</option>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dropdown .= '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row[$column]) . "</option>\n";
    }

    $dropdown .= "</select>\n";

    return $dropdown;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['pseudo_user'])) {

        if (isset($_POST['mettre_a_jour_selection'])) {
            if ($_SESSION['view_method'] === 'week') {
                if (isset($_POST['semaine']) && is_numeric($_POST['semaine'])) {
                    $semaine_courante = (int) $_POST['semaine'];
                    $_SESSION['semaine_selectionnee'] = $semaine_courante;
                }
            } else {
                if (isset($_POST['mois']) && is_numeric($_POST['mois'])) {
                    $mois_courant = (int) $_POST['mois'];
                    $_SESSION['mois_selectionnee'] = $mois_courant;
                }
            }
        }

        if (isset($_POST['view_choice'])) {
            if ($_POST['view_choice'] === 'month' || $_POST['view_choice'] === 'week') {
                $_SESSION['view_method'] = $_POST['view_choice'];
            }
        }
    } else {
        header('HTTP/1.1 401 Unauthorized');
        exit();
    }
}

echo $_SESSION['view_method'] === 'week'
    ? '<link href="public/css/calendar_week.css" rel="stylesheet" type="text/css">'
    : '<link href="public/css/calendar_month.css" rel="stylesheet" type="text/css">';

include 'src/Calendar.php';
include 'config/database.php';

if (!isset($_POST['mois'])) {
    $post_mois = date('m');
} else {
    $post_mois = $_POST['mois'];
}

if (!isset($_POST['semaine'])) {
    $post_semaine = date('W');
} else {
    $post_semaine = $_POST['semaine'];
}

$_SESSION['date_selectionnee'] = date('Y-m-d', strtotime(date('Y') . $post_mois . date('d')));

$week = $post_semaine;
$calendar = new Calendar($_SESSION['date_selectionnee'], $week);

if (isset($_SESSION['role']) && $_SESSION['role']) {
    if ($_SESSION['role'] === 'Admin') {
        $where = 'WHERE roles.label = "DEV" or roles.label = "INFRA"';
    } else {
        $where = 'WHERE roles.label = "' . htmlspecialchars($_SESSION['role'], ENT_QUOTES) . '"';
    }
} else {
    $where = '';
}

$sql = '
    SELECT courses.*, teachers.lastname, places.name AS lieu, types.name AS type
    FROM courses
    LEFT JOIN teachers ON courses.teacher = teachers.id
    LEFT JOIN places ON courses.place = places.id
    LEFT JOIN types ON courses.type = types.id
    LEFT JOIN roles ON courses.group_id = roles.id
    ' . ($where ? $where : '') . '
    ORDER BY courses.start_time ASC
';
$stmt = $pdo->query($sql);


if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $calendar->add_event($row['name'], $row['start_time'], 1, $row['color'], [$row['room'], $row['lastname'], $row['lieu'], $row['type'], $row['start_time'], $row['end_time'], $row['group_id']]);
    }
}

?>

<body>
    <div class="header_navigation">
        <div class="box_button_left"><a href="/index.php"><img class="logo_epsi" src="public/img/epsi.svg" alt=""></div></a>
        <div class="date_select">
            <div class="box_semaine_spinner">
                <form class="form_navigation_semaine" id="form_navigation_semaine" action="" method="post">

                    <?php if ($_SESSION['view_method'] === 'week'): ?>
                        <select name="semaine" id="semaine" class="selecteur_semaine">
                            <?php
                            echo '<optgroup label="' . $annee_actuelle . '">';
                            for ($i = 1; $i <= 52; $i++) {
                                $date_debut = new DateTime();
                                $date_debut->setISODate($annee_actuelle, $i);
                                $date_fin = clone $date_debut;
                                $date_fin->modify('+4 days');
                                $selected = ($i == $post_semaine) ? ' selected' : '';
                                echo '<option value="' . $i . '"' . $selected . '>Semaine ' . $i . ' </option>';
                            }
                            echo '</optgroup>';
                            ?>
                        </select>

                    <?php elseif ($_SESSION['view_method'] === 'month'): ?>
                        <select name="mois" id="mois" class="selecteur_mois">
                            <?php
                            $mois = [
                                '01' => 'Janvier',
                                '02' => 'Février',
                                '03' => 'Mars',
                                '04' => 'Avril',
                                '05' => 'Mai',
                                '06' => 'Juin',
                                '07' => 'Juillet',
                                '08' => 'Août',
                                '09' => 'Septembre',
                                '10' => 'Octobre',
                                '11' => 'Novembre',
                                '12' => 'Décembre',
                            ];
                            foreach ($mois as $key => $value) {
                                $selected = (isset($post_mois) && $post_mois == $key) ? ' selected' : ''; // Utilise mois_selectionnee
                                echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    <?php endif; ?>

                    <input type="submit" name="mettre_a_jour_selection" id="mettre_a_jour_selection" value="Mettre à jour"
                        class="btn_mettre_a_jour_semaine" style="display: none;">
                </form>
            </div>
            <div class="box_button_right">
                <form action="" method="POST">
                    <select name="view_choice" id="view_choice" class="view_choice">
                        <option value="month" <?php echo $_SESSION['view_method'] === 'month' ? 'selected' : ''; ?>>Mois
                        </option>
                        <option value="week" <?php echo $_SESSION['view_method'] === 'week' ? 'selected' : ''; ?>>Semaine
                        </option>
                    </select>
                    <input type="submit" name="choice_view" id="choice_view" style="display: none;">
                </form>
            </div>
        </div>
        <div class="home">
            <button class="btn btn-outline-light d-flex align-items-center" style="margin-right: 20px;" data-bs-toggle="modal"
                data-bs-target="#ModalProfil">
                <i class="bi bi-person-circle"></i>
            </button>
            <a href="/"><button class="btn btn-outline-light d-flex align-items-center">
                    <i class="bi bi-house"></i>
                </button></a>
        </div>
    </div>
    </div>
    <div class="content_global">
        <div class="content">
            <?= $calendar ?>
        </div>
        <div class="modal fade" id="ModalProfil" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin"): ?>
                            <button class="btn btn-primary" style="background-color: var(--color-violet-epsi);"
                                data-bs-toggle="modal" data-bs-target="#Modal_Add_Event ">
                                Ajouter un
                                cours</button>
                        <?php endif; ?>
                        <a href="controller/disconect_controller.php" class="disconnect_a"><button
                                class="disconnect_button">Déconnexion</button></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="Modal_Add_Event" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: var(--color-violet-epsi);">
                        <h5 class="modal-title" id="exampleModalLabel" style="color: white;">Ajouter un cours</h5>
                        <button type="button" class="btn-close" style="background-color: white;" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="background-color: var(--color-violet-epsi);">
                        <form method="POST" action="controller/add_event_controller.php" class="add-event-form">
                            <label for="name" class="form-label">Nom du cours:</label>
                            <input type="text" name="name" id="name" required class="form-input"><br>

                            <label for="color" class="form-label">Couleur du cours:</label>
                            <input type="color" name="color" id="color" required class="form-input"><br>

                            <label for="start_time" class="form-label">Heure de début:</label>
                            <input type="datetime-local" name="start_time" id="start_time" required
                                class="form-input"><br>

                            <label for="end_time" class="form-label">Heure de fin:</label>
                            <input type="datetime-local" name="end_time" id="end_time" required class="form-input"><br>

                            <?php
                            echo generateDropdown($pdo, 'Lieu', 'places', 'name', 'place');
                            echo generateDropdown($pdo, 'Prof', 'teachers', 'lastname', 'teacher');
                            echo generateDropdown($pdo, 'Type', 'types', 'name', 'type');
                            echo generateDropdown($pdo, 'Promo', 'roles', 'label', 'role');
                            ?>

                            <label for="room" class="form-label">Salle:</label>
                            <input type="text" name="room" id="room" required class="form-input"><br>

                            <input type="submit" value="Ajouter le cours" class="form-submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('mousemove', (e) => {
            const hoverDiv = document.querySelector('.event_content');
            if (hoverDiv && hoverDiv.classList.contains('follow-mouse')) {
                hoverDiv.style.left = e.clientX + 'px';
                hoverDiv.style.top = e.clientY + 'px';
            }
        });

        document.addEventListener('mouseenter', (e) => {
            const hoverDiv = document.querySelector('.event_content');
            if (hoverDiv) {
                hoverDiv.classList.add('follow-mouse');
            }
        });

        document.addEventListener('mouseleave', (e) => {
            const hoverDiv = document.querySelector('.event_content');
            if (hoverDiv) {
                hoverDiv.classList.remove('follow-mouse');
            }
        });
    </script>
</body>

</html>
<script>
    document.getElementById('view_choice').addEventListener('change', function() {
        document.getElementById('choice_view').click();
    });
    var moisElement = document.getElementById('mois');
    var semaineElement = document.getElementById('semaine');

    if (moisElement) {
        moisElement.addEventListener('change', function() {
            document.getElementById('mettre_a_jour_selection').click();
            console.log("Mois sélectionné");
        });
    }

    if (semaineElement) {
        semaineElement.addEventListener('change', function() {
            document.getElementById('mettre_a_jour_selection').click();
            console.log("Semaine sélectionnée");
        });
    }
</script>