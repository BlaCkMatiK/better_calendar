<?php

include("../config/database.php");

ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
session_start();


if (isset($_POST['valider'])) {
    if ($_POST['csrf_token'] == $_SESSION['csrf_token']) {
        if (!isset($_POST['pseudo_user'], $_POST['password'], $_POST['csrf_token'])) {
            $_SESSION['error'] = "Un des champs n'est pas reconnu.";
            header("Location: $domain_name/");
            exit();
        } else {
            if ((empty($_POST["pseudo_user"])) || (empty($_POST["password"])) || (empty($_POST["csrf_token"]))) {
                // Un des champ et vide
                header('HTTP/1.1 401 Unauthorized');
                exit();
            } else {


                if (filter_var($_POST["pseudo_user"], FILTER_VALIDATE_EMAIL)) {
                    if (strlen($_POST["password"]) < 50) {
                        // L'adresse e-mail est valide, traiter les données
                        $Pseudo = mysqli_real_escape_string($conn_workshop_mysqli, $_POST["pseudo_user"]);
                        $Pseudo = htmlspecialchars($Pseudo, ENT_QUOTES, 'UTF-8');
                        $password = mysqli_real_escape_string($conn_workshop_mysqli, $_POST["password"]);
                        $password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
                        $password = hash('sha256', $password);
                        $stmt = $conn_workshop_mysqli->prepare("SELECT * FROM users WHERE email=? AND password=?");
                        $stmt->bind_param("ss", $Pseudo, $password);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows != 1) {
                            $random_sleep = rand(1, 2);
        
                            $_SESSION['error'] = "Mail ou mot de passe incorrect";
                            unset($_SESSION['csrf_token']);
                            
                            http_response_code(403);
                            header("Location: ../main");
                        } else {
                            // LOGIN OK
                            unset($_SESSION['csrf_token']);
                            session_regenerate_id(true);
                            $row = $result->fetch_assoc();
                            $id_utilisateur = htmlspecialchars($row['id_utilisateur']);
                            $id_role = $row['role'];
                            // Query to get the role label based on the user's role_id
                            $query = "SELECT label FROM roles WHERE id = :role_id";

                            $stmt2 = $pdo->prepare($query);
                            $stmt2->bindParam(':role_id', $id_role, PDO::PARAM_INT);
                            $stmt2->execute();

                            // Fetch the role label
                            $role = $stmt2->fetch(PDO::FETCH_ASSOC);

                            if ($role) {
                                // Store the role label in the session
                                $_SESSION['role'] = $role['label'];
                            } else {
                                $_SESSION['role'] = "BRUH";
                            }
                                                        $_SESSION['nom'] = htmlspecialchars($row['nom']);
                            $_SESSION['prenom'] = htmlspecialchars($row['prenom']);
                            $_SESSION['id_utilisateur'] = htmlspecialchars($id_utilisateur);
                            $_SESSION['pseudo_user'] = htmlspecialchars($Pseudo);
                                                        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                            $_SESSION['heure_login'] = date("Y-m-d H:i:s");
                            $_SESSION['login_status'] = true;
        
                            $_SESSION['error'] = "Vous êtes connecté avec succès $Pseudo ! , redirection en cours";

                            $random_sleep = rand(1, 2);
                            sleep($random_sleep);
                            header("Referrer-Policy: origin");
                            http_response_code(200);
                            header("Location: $domain_name/week");


                        }
                    } else {
                        header('HTTP/1.1 401 Unauthorized');
                        exit();
                    }
                } else {
                    $random_sleep = rand(1, 3);

                    // Adresse e-mail invalide, afficher un message d'erreur
                    header('HTTP/1.1 401 Unauthorized');
                    exit();
                }
            }
        }
    } else {
        // Erreur jeton CSRF
        http_response_code(401);
        exit("Invalid CSRF");
    }
}



