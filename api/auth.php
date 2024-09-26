<?php
header('Content-Type: application/json');

ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');

session_start();

require_once 'config/database.php';

// Helper function to generate API token
function generateApiToken() {
    return bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données JSON envoyées par l'application
    $data = json_decode(file_get_contents("php://input"), true);

    // Log des données reçues pour faciliter le débogage
    file_put_contents('php://stderr', print_r($data, true));

    // Vérifiez que toutes les informations sont présentes dans la requête
    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing email or password"]);
        exit();
    }

    // Traitez la connexion
    $email = htmlspecialchars($data['email']);
    $password = htmlspecialchars($data['password']);
    $hashed_password = hash('sha256', $password);

    // Préparez la requête SQL pour vérifier l'utilisateur
    $stmt = $conn_workshop_mysqli->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss", $email, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Utilisateur trouvé


        $row = $result->fetch_assoc();
        $id_utilisateur = htmlspecialchars($row['id_utilisateur']);
        $role = $row['role'];
        $role_id = $row['role_id'];

        // Régénération de l'ID de session pour des raisons de sécurité
        session_regenerate_id(true);

        // Generate an API token and store it in the database
        $api_token = generateApiToken();
        $stmt_update = $conn_workshop_mysqli->prepare("UPDATE users SET api_token=? WHERE id_utilisateur=?");
        $stmt_update->bind_param("ss", $api_token, $id_utilisateur);
        $stmt_update->execute();

        // Stockage des informations utilisateur dans la session
        $_SESSION['id_utilisateur'] = $id_utilisateur;
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email;
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['login_status'] = true;

        // Réponse JSON en cas de succès
        echo json_encode([
            "message" => "Login successful",
            "role" => $role,
            "api_token" => $api_token 
        ]);
    
    } else {
        // Utilisateur non trouvé ou mot de passe incorrect
        http_response_code(403);
        echo json_encode(["error" => "Invalid email or password"]);
    }
} else {
    // Requête non-POST
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
