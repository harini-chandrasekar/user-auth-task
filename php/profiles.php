<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $userId = isset($_POST['userId']) ? $_POST['userId'] : '';
    $token = isset($_POST['token']) ? $_POST['token'] : '';
    
    if (empty($action) || empty($userId) || empty($token)) {
        throw new Exception("Missing required parameters");
    }
    
    // Verify session in Redis
    try {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $sessionUserId = $redis->get("session:" . $token);
        
        if (!$sessionUserId || $sessionUserId != $userId) {
            throw new Exception("Invalid or expired session");
        }
    } catch (Exception $e) {
        throw new Exception("Session error: " . $e->getMessage());
    }
    
    // Connect to MongoDB
    try {
        $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
        $db = $mongoClient->user_profiles_db;
        $collection = $db->profiles;
    } catch (Exception $e) {
        throw new Exception("MongoDB connection failed: " . $e->getMessage());
    }
    
    if ($action === 'get') {
        $profile = $collection->findOne(['user_id' => (int)$userId]);
        
        echo json_encode([
            'success' => true,
            'data' => $profile ? $profile : []
        ]);
        
    } else if ($action === 'update') {
        $fullName = isset($_POST['fullName']) ? $_POST['fullName'] : '';
        $age = isset($_POST['age']) ? $_POST['age'] : '';
        $dob = isset($_POST['dob']) ? $_POST['dob'] : '';
        $contact = isset($_POST['contact']) ? $_POST['contact'] : '';
        $address = isset($_POST['address']) ? $_POST['address'] : '';
        
        $updateData = [
            'user_id' => (int)$userId,
            'fullName' => $fullName,
            'age' => (int)$age ?: 0,
            'dob' => $dob,
            'contact' => $contact,
            'address' => $address,
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        $result = $collection->updateOne(
            ['user_id' => (int)$userId],
            ['$set' => $updateData],
            ['upsert' => true]
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    } else {
        throw new Exception("Invalid action");
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
require 'vendor/autoload.php'; // Composer autoload
// Connect to MongoDB server
$client = new MongoDB\Client("mongodb://localhost:27017");

// Select database and collection
$db = $client->user_profiles_db;
$profiles = $db->profiles;
// Example: get POST data
$user_id  = $_POST['user_id'] ?? uniqid();
$fullName = $_POST['fullName'] ?? '';
$age      = $_POST['age'] ?? '';
$dob      = $_POST['dob'] ?? '';
$contact  = $_POST['contact'] ?? '';
$address  = $_POST['address'] ?? '';

// Insert into MongoDB
$result = $profiles->insertOne([
    'user_id'    => $user_id,
    'fullName'   => $fullName,
    'age'        => (int)$age,
    'dob'        => $dob,
    'contact'    => $contact,
    'address'    => $address,
    'updated_at' => new MongoDB\BSON\UTCDateTime()
]);

echo "Profile saved with ID: " . $result->getInsertedId();
$profile = $profiles->findOne(['user_id' => $user_id]);
print_r($profile);

?>
