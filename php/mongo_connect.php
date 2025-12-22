<?php
require __DIR__ . '/../vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->user_profiles_db->profiles;

    $result = $collection->insertOne([
        'name' => $_POST['name'] ?? 'Harini',
        'email' => $_POST['email'] ?? 'test@gmail.com',
        'age' => $_POST['age'] ?? 21,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Profile saved successfully',
        'id' => (string)$result->getInsertedId()
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
