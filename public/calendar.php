<?php
session_start();
if(!isset($_SESSION['login_status']) || $_SESSION['login_status'] != true){
    header('Location: index.php');
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
    <title>Event Calendar</title>
    <link href="./css/style.css" rel="stylesheet" type="text/css">
    <link href="./css/calendar.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>
<style>
    * {
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, "segoe ui", roboto, oxygen, ubuntu, cantarell, "fira sans", "droid sans", "helvetica neue", Arial, sans-serif;
        font-size: 16px;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    body {
        background-color: #FFFFFF;
        margin: 0;
    }

    .navtop {
        background-color: #3b4656;
        height: 60px;
        width: 100%;
        border: 0;
    }

    .navtop div {
        display: flex;
        margin: 0 auto;
        width: 800px;
        height: 100%;
    }

    .navtop div h1,
    .navtop div a {
        display: inline-flex;
        align-items: center;
    }

    .navtop div h1 {
        flex: 1;
        font-size: 24px;
        padding: 0;
        margin: 0;
        color: #ebedee;
        font-weight: normal;
    }

    .navtop div a {
        padding: 0 20px;
        text-decoration: none;
        color: #c4c8cc;
        font-weight: bold;
    }

    .navtop div a i {
        padding: 2px 8px 0 0;
    }

    .navtop div a:hover {
        color: #ebedee;
    }

    .content {
        width: 800px;
        margin: 0 auto;
    }

    .content h2 {
        margin: 0;
        padding: 25px 0;
        font-size: 22px;
        border-bottom: 1px solid #ebebeb;
        color: #666666;
    }
</style>

<body>
    <nav class="navtop">
        <div>
            <h1>Event Calendar</h1>
        </div>

    </nav>
    <div class="content home">
        <?= $calendar ?>
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