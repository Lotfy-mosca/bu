<?php
// Admin panel to view and manage messages
$jsonFile = 'messages.json';

if (file_exists($jsonFile)) {
    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    $messages = $data['messages'] ?? [];
} else {
    $messages = [];
}

// Handle clear messages
if (isset($_GET['clear'])) {
    file_put_contents($jsonFile, json_encode(['messages' => []], JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat Admin</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 20px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #4CAF50; color: white; }
        .btn { padding: 10px 20px; background: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #d32f2f; }
    </style>
</head>
<body>
    <h1>Chat Admin Panel</h1>
    <p>Total Messages: <?php echo count($messages); ?></p>
    
    <a href="?clear=1" onclick="return confirm('Clear all messages?')">
        <button class="btn">Clear All Messages</button>
    </a>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Message</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (array_reverse($messages) as $msg): ?>
            <tr>
                <td><?php echo htmlspecialchars($msg['id'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($msg['username'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($msg['message'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($msg['timestamp'] ?? ''); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>