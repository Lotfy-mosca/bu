<?php
header('Content-Type: application/json');

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['username']) || !isset($data['message'])) {
    echo json_encode(['success' => false, 'error' => 'Missing username or message']);
    exit;
}

$username = trim($data['username']);
$message = trim($data['message']);

// Validate empty fields
if (empty($username) || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Username and message cannot be empty']);
    exit;
}

// Sanitize input to prevent XSS
$username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Path to JSON file
$jsonFile = 'messages.json';

// Read existing messages
$messages = [];
if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $messages = json_decode($jsonContent, true);
    
    // If decoding fails, initialize empty array
    if (!is_array($messages) || !isset($messages['messages'])) {
        $messages = ['messages' => []];
    }
} else {
    $messages = ['messages' => []];
}

// Create new message
$newMessage = [
    'id' => uniqid(), // Unique ID for each message
    'username' => $username,
    'message' => $message,
    'timestamp' => date('Y-m-d H:i:s')
];

// Add to messages array
$messages['messages'][] = $newMessage;

// Optional: Limit the number of messages to prevent huge file
$maxMessages = 1000;
if (count($messages['messages']) > $maxMessages) {
    $messages['messages'] = array_slice($messages['messages'], -$maxMessages);
}

// Save to JSON file
$jsonData = json_encode($messages, JSON_PRETTY_PRINT);
if (file_put_contents($jsonFile, $jsonData)) {
    echo json_encode(['success' => true, 'message' => $newMessage]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save message']);
}
?>