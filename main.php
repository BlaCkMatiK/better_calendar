<?php

$db_host = $_ENV['MYSQL_HOST'];
$db_user_user = $_ENV['MYSQL_USER'];
$db_password_user = $_ENV['MYSQL_PASSWORD'];
try {
    $conn_workshop_pdo = new PDO("mysql:host=$db_host;dbname=workshop", $db_user_user, $db_password_user);
    $conn_workshop_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(1);
session_set_cookie_params(['secure' => true]);
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
session_start();




$_SESSION['ip_crsf'] = $_SERVER['REMOTE_ADDR'];



if (isset($_SESSION['pseudo_user'])) {
    header("Location: $domain_name/");
    exit("Utilisateur déja connecté");
} else {
    $_SESSION['login_status'] = false;
}

if (!isset($_SESSION['csrf_token'])) {
    $token = hash('sha256', uniqid());
    $_SESSION['csrf_token'] = $token;
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Workshop</title>
    <html lang="fr">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/login.css">
    <meta https-equiv="X-Frame-Options" content="DENY">
</head>

<body>
    <div class="big_container" id="customCursor">
        <div class="content">
            <div class="container noselect">
                <form method="post" action="controller/login_controler.php"
                    id="form_login">
                    <div class="logo_text noselect">Workshop</div>
                    <br><br><br>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <input type="email" maxlength="100" class="noselect" name="pseudo_user" placeholder="Votre mail..." required>

                    <input type="password" id="password" class="password-toggle noselect" maxlength="100"
                        name="password" placeholder="Votre mot de passe..." required>

                    <br><br>

                    <input type="submit" class="button-pulse noselect" name="valider" id="button_login"
                        value="Connexion !">
                    <span id="loader_login" class="loader" style="display: none;"></span>
                    <br>
                </form>

                <button id="password-toggle" class="noselect password-toggle"
                    onclick="togglePasswordVisibility()">Afficher
                    le
                    mot de passe</button>
                <br><br><br>

                <br><br>
            </div>
            <div class="show_error_message">
                <?php
                if (isset($_SESSION['message'])) {
                    echo "<p class='message'>" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "</p>";
                    unset($_SESSION['message']);
                }
                if (isset($_SESSION['error'])) {
                    echo "<p class='error'>" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "</p>";
                    unset($_SESSION['error']);
                }
                ?>
            </div>

        </div>
    </div>
</body>

</html>


<script>
    
function togglePasswordVisibility() {
    var passwordInput = document.getElementById("password");
    var passwordToggleBtn = document.getElementById("password-toggle");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        passwordToggleBtn.textContent = "Masquer le mot de passe";
    } else {
        passwordInput.type = "password";
        passwordToggleBtn.textContent = "Afficher le mot de passe";
    }
}
function showLoading() {
    var submitButton = document.getElementById('button_login');
    var loader_login = document.getElementById('loader_login');
    submitButton.classList.add('display_none');
    loader_login.style.display = 'inline-block';

    setTimeout(function () {
        submitButton.classList.remove('display_none');
        loader_login.style.display = 'none';
    }, 1900);
}

document.getElementById('form_login').addEventListener('submit', function (event) {
    if (this.checkValidity()) {
        showLoading();
    } else {
        event.preventDefault();
    }
});

document.addEventListener("DOMContentLoaded", function () {
    function showMessages() {
        var messages = document.querySelectorAll('.show_error_message p');
        messages.forEach(function (message) {
            message.classList.add('show');
        });
    }

    function hideMessages() {
        setTimeout(function () {
            var messages = document.querySelectorAll('.show_error_message p');
            messages.forEach(function (message) {
                message.classList.remove('show');
            });
        }, 3000);
    }
    showMessages();
    hideMessages();
});








</script>