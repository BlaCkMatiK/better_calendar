
<?php
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
session_start();
if ((!isset($_SESSION['pseudo_user'])) || (!isset($_SESSION['id_utilisateur']))) {
    unset($_SESSION['user_ip']);
    unset($_SESSION['user_agent']);
    unset($_SESSION['pseudo_user']);
    unset($_SESSION['id_utilisateur']);
    session_unset();
    session_destroy();
    http_response_code(401);
    header("Location: $domain_name/main");
    exit(0);
} 
?>


<!DOCTYPE html>


<html lang="en">

<head>
  <link rel="stylesheet" href="../public/css/style.css">
</head>

<body>
  <!-- <button style="--clr:#EA00FF"><span>Button</span><i></i></button>
  <button style="--clr:#FFF01F"><span>Button</span><i></i></button>
  <button style="--clr:#7FFF00"><span>Button</span><i></i></button>
  <button style="--clr:#FF5E00"><span>Button</span><i></i></button> -->
  <button style="--clr:#39FF14"
    onclick="window.location.href='/public/calendar.php';"><span>Calendar</span><i></i></button>

  <button style="--clr:#FF3131" onclick="window.location.href='/public/add_event.php';"><span>Add
      Event</span><i></i></button>
  <button style="--clr:red" onclick="window.location.href='../controller/disconect_controller.php';"><span>Déconnexion</span><i></i></button>

  <!-- <button style="--clr:#1F51FF"><span>Button</span><i></i></button>
  <button style="--clr:#FF44CC"><span>Button</span><i></i></button>
  <button style="--clr:#BC13FE"><span>Button</span><i></i></button>
  <button style="--clr:#0FF0FC"><span>Button</span><i></i></button>
  <button style="--clr:#E7EE4F"><span>Button</span><i></i></button>
  <button style="--clr:#8A2BE2"><span>Button</span><i></i></button>
  <button style="--clr:#FF1493"><span>Button</span><i></i></button>
  <button style="--clr:#CCFF00"><span>Button</span><i></i></button> -->
</body>
<!--  From Online Tutorials YT Channel -->

</html>