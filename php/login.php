<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // MySQL Connection
    $mysqli = new mysqli("localhost", "root", "", "user_auth_db", 3307);
    if ($mysqli->connect_error) {
        throw new Exception("MySQL Connection Error: " . $mysqli->connect_error);
    }

    // Get POST data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        throw new Exception("Email and password are required");
    }

    // Find user
    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Invalid email or password");
    }

    $user = $result->fetch_assoc();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        throw new Exception("Invalid email or password");
    }

    // Create session token
    $token = bin2hex(random_bytes(32));

    // Redis optional (safe)
    try {
        if (class_exists('Redis')) {
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->setex("session:" . $token, 86400, $user['id']);
        }
    } catch (Exception $e) {
        error_log("Redis not running: " . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'token' => $token,
        'userId' => $user['id'],
        'message' => 'Login successful'
    ]);

    $stmt->close();
    $mysqli->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
