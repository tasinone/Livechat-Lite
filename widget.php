<?php
// widget.php 
require_once 'config/database.php';
$db = Database::getInstance();

$banner_text = Database::getSetting('banner_text', 'We usually respond within 2 hours');
$icon_position = Database::getSetting('icon_position', 'bottom-right');
$icon_bg_color = Database::getSetting('icon_bg_color', '#dc3545');
$icon_svg_color = Database::getSetting('icon_svg_color', '#ffffff');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- Chat Widget -->
    <div id="chat-widget" class="chat-widget <?php echo $icon_position; ?>" style="--icon-bg: <?php echo $icon_bg_color; ?>; --icon-color: <?php echo $icon_svg_color; ?>;">
        <div id="chat-bubble" class="chat-bubble">
            <div class="notification-dot" id="notification-dot" style="display: none;"></div>
            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.5 19H8C4 19 2 18 2 13V8C2 4 4 2 8 2H16C20 2 22 4 22 8V13C22 17 20 19 16 19H15.5C15.19 19 14.89 19.15 14.7 19.4L13.2 21.4C12.54 22.28 11.46 22.28 10.8 21.4L9.3 19.4C9.14 19.18 8.77 19 8.5 19Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15.9965 11H16.0054" stroke="#292D32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11.9955 11H12.0045" stroke="#292D32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7.99451 11H8.00349" stroke="#292D32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        
        <div id="chat-popup" class="chat-popup" style="display: none;">
            <div class="chat-header">
                <h3>Live Chat Support</h3>
                <button id="close-chat" class="close-btn">&times;</button>
            </div>
            
            <div class="chat-content">
                <div id="banner-section" class="banner-section">
                    <p><?php echo htmlspecialchars($banner_text); ?></p>
                </div>
                
                <div id="contact-form" class="contact-form">
                    <input type="text" id="user-name" placeholder="Your Name" required>
                    <input type="email" id="user-email" placeholder="Your Email" required>
                    <button id="start-chat">Start Chat</button>
                </div>
                
                <div id="chat-messages" class="chat-messages" style="display: none;"></div>
                
                <div id="message-form" class="message-form" style="display: none;">
                    <input type="text" id="message-input" placeholder="Type your message...">
                    <button id="send-message">Send</button>
                </div>
            </div>
        </div>
    </div>
    
    <audio id="notification-sound" preload="auto">
        <source src="assets/notification.mp3" type="audio/mpeg">
    </audio>
    
    <script src="assets/chat.js"></script>
</body>
</html>