<?php
require __DIR__ . '/vendor/autoload.php'; // Load Composer libraries

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->user_profiles_db->profiles;

    // Read JSON file
    $jsonFile = __DIR__ . '/data/profiles.json';
    if (!file_exists($jsonFile)) {
        throw new Exception("JSON file not found.");
    }

    $profiles = json_decode(file_get_contents($jsonFile), true);
    if (!is_array($profiles)) {
        throw new Exception("Invalid JSON format.");
    }

    // Insert into MongoDB
    $collection->insertMany($profiles);

    echo "Profiles inserted successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
