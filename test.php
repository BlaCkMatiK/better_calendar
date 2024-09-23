<?php
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
} else {
    echo 'test';
}

?>
