<?php
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
session_start();

// Détruire la session
session_unset();
session_destroy();


$domain = filter_var($_ENV['DOMAIN_NAME'], FILTER_SANITIZE_URL);
header("Refresh: 0; url=$domain");
exit(0);
