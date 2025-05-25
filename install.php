<?php
// Create database and initial setup
$db = new SQLite3('livechat.db');

// Create tables
$db->exec('
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY,
    setting_key TEXT UNIQUE,
    setting_value TEXT
)');

$db->exec('
CREATE TABLE IF NOT EXISTS chats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    email TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_message_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    has_unread BOOLEAN DEFAULT 0
)');

$db->exec('
CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    chat_id INTEGER,
    message TEXT,
    is_admin BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats (id)
)');

// Insert default settings
$defaults = [
    ['admin_username', 'admin'],
    ['admin_password', password_hash('admin123', PASSWORD_DEFAULT)],
    ['banner_text', 'Please use the same email to continue the previous conversation.'],
    ['icon_position', 'bottom-right'],
    ['icon_bg_color', '#dc3545'],
    ['icon_svg_color', '#ffffff'],
    ['smtp_enabled', '0'],
    ['smtp_host', ''],
    ['smtp_port', '587'],
    ['smtp_username', ''],
    ['smtp_password', ''],
    ['smtp_from_name', 'Support Team'],
    ['smtp_from_email', '']
];

foreach($defaults as $setting) {
    $db->exec("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES ('{$setting[0]}', '{$setting[1]}')");
}

echo "Installation complete! Default login: admin/admin123";
?>
