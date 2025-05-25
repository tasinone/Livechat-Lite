<?php
// api/get_messages.php 
header('Content-Type: application/json');
require_once '../config/database.php';

$chat_id = $_GET['chat_id'] ?? 0;
$last_id = $_GET['last_id'] ?? 0;

if (!$chat_id) {
    http_response_code(400);
    exit(json_encode(['error' => 'Chat ID required']));
}

$db = Database::getInstance();
$stmt = $db->prepare('SELECT * FROM messages WHERE chat_id = ? AND id > ? ORDER BY created_at ASC');
$stmt->bindValue(1, $chat_id);
$stmt->bindValue(2, $last_id);
$result = $stmt->execute();

$messages = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $messages[] = $row;
}

echo json_encode(['messages' => $messages]);
?>