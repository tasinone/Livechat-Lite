<?php
// admin/dashboard.php 
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <div class="admin-header">
        <h1>Live Chat Admin</h1>
        <div class="admin-nav">
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="admin-container">
        <div class="chat-list">
            <h3>Conversations</h3>
            <div id="chats-container"></div>
        </div>
        
        <div class="chat-view">
            <div id="chat-header" class="chat-header">
                <span>Select a conversation</span>
            </div>
            <div id="messages-container" class="messages-container"></div>
            <div id="reply-form" class="reply-form" style="display: none;">
                <input type="text" id="reply-input" placeholder="Type your reply...">
                <button id="send-reply">Send</button>
            </div>
        </div>
    </div>
    
    <audio id="notification-sound" preload="auto">
        <source src="../assets/notification.mp3" type="audio/mpeg">
    </audio>
    
    <script src="../assets/admin.js"></script>
</body>
</html>
?>