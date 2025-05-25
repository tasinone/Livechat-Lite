<?php
// admin/settings.php 
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'credentials':
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($username && $password) {
                Database::setSetting('admin_username', $username);
                Database::setSetting('admin_password', password_hash($password, PASSWORD_DEFAULT));
                $success = 'Credentials updated successfully';
            }
            break;
            
        case 'appearance':
            Database::setSetting('icon_bg_color', $_POST['icon_bg_color'] ?? '#dc3545');
            Database::setSetting('icon_svg_color', $_POST['icon_svg_color'] ?? '#ffffff');
            Database::setSetting('icon_position', $_POST['icon_position'] ?? 'bottom-right');
            Database::setSetting('banner_text', $_POST['banner_text'] ?? '');
            $success = 'Appearance settings updated';
            break;
            
        case 'smtp':
            Database::setSetting('smtp_enabled', $_POST['smtp_enabled'] ?? '0');
            Database::setSetting('smtp_host', $_POST['smtp_host'] ?? '');
            Database::setSetting('smtp_port', $_POST['smtp_port'] ?? '587');
            Database::setSetting('smtp_username', $_POST['smtp_username'] ?? '');
            Database::setSetting('smtp_password', $_POST['smtp_password'] ?? '');
            Database::setSetting('smtp_from_name', $_POST['smtp_from_name'] ?? '');
            Database::setSetting('smtp_from_email', $_POST['smtp_from_email'] ?? '');
            $success = 'SMTP settings updated';
            break;
    }
}

// Get current settings
$settings = [
    'admin_username' => Database::getSetting('admin_username'),
    'banner_text' => Database::getSetting('banner_text'),
    'icon_position' => Database::getSetting('icon_position'),
    'icon_bg_color' => Database::getSetting('icon_bg_color'),
    'icon_svg_color' => Database::getSetting('icon_svg_color'),
    'smtp_enabled' => Database::getSetting('smtp_enabled'),
    'smtp_host' => Database::getSetting('smtp_host'),
    'smtp_port' => Database::getSetting('smtp_port'),
    'smtp_username' => Database::getSetting('smtp_username'),
    'smtp_password' => Database::getSetting('smtp_password'),
    'smtp_from_name' => Database::getSetting('smtp_from_name'),
    'smtp_from_email' => Database::getSetting('smtp_from_email')
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <div class="admin-header">
        <h1>Settings</h1>
        <div class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="settings-container">
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="settings-section">
            <h3>Admin Credentials</h3>
            <form method="POST">
                <input type="hidden" name="action" value="credentials">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($settings['admin_username']); ?>" required>
                <input type="password" name="password" placeholder="New Password" required>
                <button type="submit">Update Credentials</button>
            </form>
        </div>
        
        <div class="settings-section">
            <h3>Widget Appearance</h3>
            <form method="POST">
                <input type="hidden" name="action" value="appearance">
                <label>Banner Text:</label>
                <input type="text" name="banner_text" value="<?php echo htmlspecialchars($settings['banner_text']); ?>" placeholder="We usually respond within 2 hours">
                
                <label>Icon Position:</label>
                <select name="icon_position">
                    <option value="bottom-right" <?php echo $settings['icon_position'] === 'bottom-right' ? 'selected' : ''; ?>>Bottom Right</option>
                    <option value="bottom-left" <?php echo $settings['icon_position'] === 'bottom-left' ? 'selected' : ''; ?>>Bottom Left</option>
                </select>
                
                <label>Icon Background Color:</label>
                <input type="color" name="icon_bg_color" value="<?php echo $settings['icon_bg_color']; ?>">
                
                <label>Icon SVG Color:</label>
                <input type="color" name="icon_svg_color" value="<?php echo $settings['icon_svg_color']; ?>">
                
                <button type="submit">Update Appearance</button>
            </form>
        </div>
        
        <div class="settings-section">
            <h3>SMTP Settings</h3>
            <form method="POST">
                <input type="hidden" name="action" value="smtp">
                
                <label>
                    <input type="checkbox" name="smtp_enabled" value="1" <?php echo $settings['smtp_enabled'] ? 'checked' : ''; ?>>
                    Enable SMTP
                </label>
                
                <label>SMTP Host:</label>
                <input type="text" name="smtp_host" value="<?php echo htmlspecialchars($settings['smtp_host']); ?>" placeholder="smtp.gmail.com">
                
                <label>SMTP Port:</label>
                <input type="number" name="smtp_port" value="<?php echo $settings['smtp_port']; ?>" placeholder="587">
                
                <label>SMTP Username:</label>
                <input type="text" name="smtp_username" value="<?php echo htmlspecialchars($settings['smtp_username']); ?>">
                
                <label>SMTP Password:</label>
                <input type="password" name="smtp_password" value="<?php echo htmlspecialchars($settings['smtp_password']); ?>">
                
                <label>From Name:</label>
                <input type="text" name="smtp_from_name" value="<?php echo htmlspecialchars($settings['smtp_from_name']); ?>" placeholder="Support Team">
                
                <label>From Email:</label>
                <input type="email" name="smtp_from_email" value="<?php echo htmlspecialchars($settings['smtp_from_email']); ?>">
                
                <button type="submit">Update SMTP Settings</button>
            </form>
        </div>
        
        <div class="settings-section">
            <h3>Instructions</h3>
            <p><strong>Third-party SMTP Services:</strong></p>
            <ul>
                <li><strong>Gmail:</strong> smtp.gmail.com, Port: 587</li>
                <li><strong>Yahoo:</strong> smtp.mail.yahoo.com, Port: 587</li>
                <li><strong>Outlook:</strong> smtp-mail.outlook.com, Port: 587</li>
                <li><strong>ServerSMTP.com:</strong> Use their provided settings</li>
                <li><strong>SendFox:</strong> Use their SMTP credentials</li>
            </ul>
        </div>
    </div>
</body>
</html>