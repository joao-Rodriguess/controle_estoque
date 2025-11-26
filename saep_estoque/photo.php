<?php
// photo.php - Script to serve user profile photos from database

// Database connection parameters (adjust as needed)
$host = 'localhost';
$dbname = 'controle_estoque';
$user = 'root';
$password = '';

// Get user_id from GET parameter
if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo "Parâmetro user_id é obrigatório.";
    exit;
}

$user_id = intval($_GET['user_id']);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT photo, mime_type FROM user_photos WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['photo'])) {
            header("Content-Type: " . $row['mime_type']);
            echo $row['photo'];
            exit;
        }
    }
    // Serve default avatar image if exists or error
    $default_avatar = __DIR__ . '/../static/images/users/default-avatar.png';
    if (file_exists($default_avatar)) {
        header("Content-Type: image/png");
        readfile($default_avatar);
        exit;
    } else {
        http_response_code(404);
        echo "Foto de perfil não encontrada.";
        exit;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo "Erro no banco de dados: " . $e->getMessage();
    exit;
}
?>
