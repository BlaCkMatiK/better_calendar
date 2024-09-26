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


// ---------------------------------------------------------
// Sécuriser les cookies de session
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
session_start();

if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] != true) {
    header('Location: main');
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
                <i class="bi bi-person-circle">
                </i>
            </button>
            <a href="/">
                <button class="btn btn-outline-light d-flex align-items-center">
                    <i class="bi bi-house">
                    </i>
                </button>
            </a>

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
        <div class="content_center_index">
            <a class="btn btn-block custom-btn b-none" href="/week">Calendrier</a>
            <a class="btn btn-block custom-btn b-none" href="/hardware">Hardware</a>
            <a class="btn btn-block custom-btn b-none" href="/room">Salles</a>
            <a class="btn btn-block custom-btn b-none" href="https://nextcloud.romain-igounet.fr">Fichiers</a>
        </div>
    </div>
</body>

<style>
    .b-none {
        border: none;
    }

    .content {
        justify-content: center;
    }

    .content_center_index {
        height: calc(100vh - 80px);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .content_center_index a {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
</style>