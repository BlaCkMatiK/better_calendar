<?php
include '../src/Calendar.php';
include '../config/database.php';

$stmt = $pdo->query("SELECT * FROM courses");

while ($row = $stmt->fetch()) {
    echo $row['name'] . "<br>";
}

$calendar = new Calendar('2024-05-12');
$calendar->add_event('Birthday', '2024-05-03', 1, 'green');
$calendar->add_event('Doctors', '2024-05-04', 1, 'red');
$calendar->add_event('Holiday', '2024-05-16', 7);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Event Calendar</title>
    <link href="./css/style.css" rel="stylesheet" type="text/css">
    <link href="./css/calendar.css" rel="stylesheet" type="text/css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.day_num').forEach(function(day) {
                day.addEventListener('click', function() {
                    const date = this.getAttribute('data-date');
                    document.getElementById('event-date').value = date;
                    document.getElementById('event-form').style.display = 'block';
                });
            });
        });
    </script>
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
    <div id="event-form" style="display:none;">
        <form action="calendar.php" method="post">
            <input type="hidden" id="event-date" name="date">
            <label for="event-text">Event:</label>
            <input type="text" id="event-text" name="text" required>
            <label for="event-color">Color:</label>
            <input type="text" id="event-color" name="color">
            <button type="submit">Add Event</button>
        </form>
    </div>
</body>

</html>