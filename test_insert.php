<?php
require __DIR__ . '/../vendor/autoload.php'; // adjust if vendor folder is somewhere else

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->user_profiles_db->profiles;

    $result = $collection->insertOne([
        'name' => 'Harini',
        'email' => 'test@gmail.com',
        'age' => 21,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);

    echo "Inserted successfully. ID: " . $result->getInsertedId();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
