<?php
// api/send_message.php 
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$input = json_decode(file_get_contents('php://input'), true);
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$message = trim($input['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    exit(json_encode(['error' => 'All fields are required']));
}

$db = Database::getInstance();

// Check if chat exists
$stmt = $db->prepare('SELECT id FROM chats WHERE email = ?');
$stmt->bindValue(1, $email);
$result = $stmt->execute();
$chat = $result->fetchArray();

if ($chat) {
    $chat_id = $chat['id'];
    // Update last message time
    $stmt = $db->prepare('UPDATE chats SET last_message_at = CURRENT_TIMESTAMP, has_unread = 1 WHERE id = ?');
    $stmt->bindValue(1, $chat_id);
    $stmt->execute();
} else {
    // Create new chat
    $stmt = $db->prepare('INSERT INTO chats (name, email, has_unread) VALUES (?, ?, 1)');
    $stmt->bindValue(1, $name);
    $stmt->bindValue(2, $email);
    $stmt->execute();
    $chat_id = $db->lastInsertRowID();
}

// Insert message
$stmt = $db->prepare('INSERT INTO messages (chat_id, message, is_admin) VALUES (?, ?, 0)');
$stmt->bindValue(1, $chat_id);
$stmt->bindValue(2, $message);
$stmt->execute();

echo json_encode(['success' => true, 'chat_id' => $chat_id]);
?>