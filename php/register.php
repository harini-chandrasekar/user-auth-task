<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Only POST method allowed'
    ]);
    exit;
}

try {
    $mysqli = new mysqli("localhost", "root", "", "user_auth_db", 3307);

    if ($mysqli->connect_error) {
        throw new Exception("Database connection failed");
    }

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        throw new Exception("Email and password are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    if (strlen($password) < 6) {
        throw new Exception("Password must be at least 6 characters");
    }

    // CHECK EMAIL (FIXED)
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        throw new Exception("Email already registered");
    }

    $stmt->close();

    // INSERT USER
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashedPassword);

    if (!$stmt->execute()) {
        throw new Exception("Registration failed");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful'
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
