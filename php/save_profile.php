<?php
header('Content-Type: application/json');

$dataDir = __DIR__ . '/../data';
$dataFile = $dataDir . '/profiles.json';


if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}


if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

$userId   = $_POST['userId']   ?? '';
$fullName = $_POST['fullName'] ?? '';
$age      = $_POST['age']      ?? '';
$dob      = $_POST['dob']      ?? '';
$contact  = $_POST['contact']  ?? '';
$address  = $_POST['address']  ?? '';

if ($userId === '') {
    echo json_encode([
        'success' => false,
        'message' => 'User ID missing'
    ]);
    exit;
}


$profiles = json_decode(file_get_contents($dataFile), true);


$profiles[$userId] = [
    'fullName' => $fullName,
    'age'      => $age,
    'dob'      => $dob,
    'contact'  => $contact,
    'address'  => $address,
    'updated'  => date('Y-m-d H:i:s')
];


file_put_contents($dataFile, json_encode($profiles, JSON_PRETTY_PRINT));

echo json_encode([
    'success' => true,
    'message' => 'Profile saved successfully'
]);
