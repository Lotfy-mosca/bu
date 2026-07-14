<?php
header('Content-Type: application/json');

$jsonFile = 'messages.json';

// Check if file exists
if (!file_exists($jsonFile)) {
    echo json_encode(['messages' => []]);
    exit;
}

// Read and decode JSON
$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

// Validate data
if (!is_array($data) || !isset($data['messages'])) {
    echo json_encode(['messages' => []]);
    exit;
}

// Optional: Filter messages if needed
$messages = $data['messages'];

// Return messages
echo json_encode(['messages' => $messages]);
?>