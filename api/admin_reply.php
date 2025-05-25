<?php
// api/admin_reply.php 
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$input = json_decode(file_get_contents('php://input'), true);
$chat_id = $input['chat_id'] ?? 0;
$message = trim($input['message'] ?? '');

if (!$chat_id || !$message) {
    http_response_code(400);
    exit(json_encode(['error' => 'Chat ID and message required']));
}

$db = Database::getInstance();

// Insert admin message
$stmt = $db->prepare('INSERT INTO messages (chat_id, message, is_admin) VALUES (?, ?, 1)');
$stmt->bindValue(1, $chat_id);
$stmt->bindValue(2, $message);
$stmt->execute();

// Update chat
$stmt = $db->prepare('UPDATE chats SET last_message_at = CURRENT_TIMESTAMP, has_unread = 0 WHERE id = ?');
$stmt->bindValue(1, $chat_id);
$stmt->execute();

// Get chat details for email
$stmt = $db->prepare('SELECT name, email FROM chats WHERE id = ?');
$stmt->bindValue(1, $chat_id);
$result = $stmt->execute();
$chat = $result->fetchArray(SQLITE3_ASSOC);

// Get user's original message
$stmt = $db->prepare('SELECT message FROM messages WHERE chat_id = ? AND is_admin = 0 ORDER BY created_at DESC LIMIT 1');
$stmt->bindValue(1, $chat_id);
$result = $stmt->execute();
$user_message = $result->fetchArray();

// Send email if SMTP is enabled
if (Database::getSetting('smtp_enabled') && $chat) {
    sendReplyEmail($chat['email'], $chat['name'], $user_message ? $user_message['message'] : '', $message);
}

echo json_encode(['success' => true]);

function sendReplyEmail($to_email, $name, $user_message, $admin_reply) {
    $smtp_host = Database::getSetting('smtp_host');
    $smtp_port = Database::getSetting('smtp_port');
    $smtp_username = Database::getSetting('smtp_username');
    $smtp_password = Database::getSetting('smtp_password');
    $from_name = Database::getSetting('smtp_from_name', 'Support Team');
    $from_email = Database::getSetting('smtp_from_email');
    
    if (!$smtp_host || !$smtp_username || !$from_email) {
        return false;
    }
    
    $subject = "Reply to your support message";
    $body = "
    <html>
    <body>
        <h2>Hello " . htmlspecialchars($name) . ",</h2>
        <p>We've received your message and here's our response:</p>
        
        <div style='background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff;'>
            <strong>Your message:</strong><br>
            " . htmlspecialchars($user_message) . "
        </div>
        
        <div style='background: #e8f5e8; padding: 15px; margin: 10px 0; border-left: 4px solid #28a745;'>
            <strong>Our response:</strong><br>
            " . htmlspecialchars($admin_reply) . "
        </div>
        
        <p>Best regards,<br>" . htmlspecialchars($from_name) . "</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $from_name . " <" . $from_email . ">\r\n";
    $headers .= "Reply-To: " . $from_email . "\r\n";
    
    // Use basic mail() function or implement PHPMailer if needed
    return mail($to_email, $subject, $body, $headers);
}
?>