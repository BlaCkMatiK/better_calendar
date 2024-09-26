<?php
session_start();
if(!isset($_SESSION['login_status']) || $_SESSION['login_status'] != true){
    header('Location: ../controller/disconect_controller.php');
}

include '../src/Calendar.php';
include '../config/database.php';

$calendar = new Calendar(date("Y-m-d"));

$stmt = $pdo->query("
    SELECT courses.*, teachers.lastname, places.name AS lieu, types.name AS type
    FROM courses
    LEFT JOIN teachers ON courses.teacher = teachers.id
    LEFT JOIN places ON courses.place = places.id
    LEFT JOIN types ON courses.type = types.id
");

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $calendar->add_event($row['name'], $row['start_time'], 1, $row['color'], [$row['room'], $row['lastname'], $row['lieu'], $row['type']]);
    }
}

// $calendar->add_event('Birthday', '2024-05-03', 1, 'green');
// $calendar->add_event('Doctors', '2024-05-04', 1, 'red');
// $calendar->add_event('Holiday', '2024-05-16', 7);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Better calendar</title>
    <?php echo $_GET['display'] === 'week' ? '<link href="./css/calendar_week.css" rel="stylesheet" type="text/css">' : '' ?>
    <link href="./css/style.css" rel="stylesheet" type="text/css">
    <link href="./css/calendar.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" />
</head>
<body>
    <nav class="navtop">
        <div>
            <!-- <h1>Event Calendar</h1> -->
             <!-- <a href="/public/calendar"> -->
                <img src="../public/img/logo.png" alt="" srcset="">
            <!-- </a> -->
             
            <button class="ml-auto btn-outline-light" data-bs-toggle="modal" data-bs-target="#myModal">
                <i class="bi bi-person-circle"></i>
            </button>
        </div>
    </nav>
    <div class="content home">
        <?= $calendar ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Infos de <?php if(isset($_SESSION['prenom']) && isset($_SESSION['nom'])){
                        echo($_SESSION['prenom'] . ' ' . $_SESSION['nom']);
                    } else{
                        echo "l'utilisateur";} ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Rôle : <?= $_SESSION['role'] ?></h6>
                    <h6>ID utilisateur : <?= $_SESSION['id_utilisateur'] ?></h6>
                    <h6>email : <?= $_SESSION['pseudo'] ?></h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
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