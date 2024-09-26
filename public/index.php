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
// Redirige vers la page d'accueil
header('Location: /views/home.php');
exit();

