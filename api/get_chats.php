<?php
// api/get_chats.php 
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');
require_once '../config/database.php';

$db = Database::getInstance();
$stmt = $db->prepare('SELECT * FROM chats ORDER BY last_message_at DESC');
$result = $stmt->execute();

$chats = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    // Get last message
    $msg_stmt = $db->prepare('SELECT message, created_at FROM messages WHERE chat_id = ? ORDER BY created_at DESC LIMIT 1');
    $msg_stmt->bindValue(1, $row['id']);
    $msg_result = $msg_stmt->execute();
    $last_message = $msg_result->fetchArray(SQLITE3_ASSOC);
    
    $row['last_message'] = $last_message ? $last_message['message'] : '';
    $row['last_message_time'] = $last_message ? $last_message['created_at'] : $row['created_at'];
    
    $chats[] = $row;
}

echo json_encode(['chats' => $chats]);
?>