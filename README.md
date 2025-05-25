# LiveChat-Lite
A lightweight, PHP-based live chat system that's simple, powerful, and easy to manage. Fully self-hosted and plug-and-play, no database setup required. Ideal for small businesses.

## Complete File Structure:

    livechat/
    ├── config/database.php
    ├── admin/
    │   ├── index.php (login)
    │   ├── dashboard.php
    │   ├── settings.php
    │   └── logout.php
    ├── api/
    │   ├── send_message.php
    │   ├── get_messages.php
    │   ├── admin_reply.php
    │   └── get_chats.php
    ├── assets/
    │   ├── style.css
    │   ├── admin.css
    │   ├── chat.js
    │   ├── admin.js
    │   ├── notification.mp3 (your sound file)
    │   └── message-icon.svg (your SVG file)
    ├── widget.php
    └── install.php

## Key Features Implemented:

### User Widget:

- ✅ Red floating icon with customizable colors
- ✅ Popup with banner text (editable from admin)
- ✅ Name/email collection before chat
- ✅ Real-time messaging
- ✅ Notification dot for new admin replies
- ✅ Sound notifications
- ✅ Mobile responsive

### Admin Panel:

- ✅ Secure login system
- ✅ WhatsApp-style layout (chat list + messages)
- ✅ Real-time message updates
- ✅ Email notifications with user message + admin reply
- ✅ Sound notifications for new messages
- ✅ Chat sorting (newest messages first)

### Settings Panel:

- ✅ Change admin credentials
- ✅ Customize icon colors and position
- ✅ Edit banner text
- ✅ Full SMTP configuration
- ✅ Support for third-party email services

## Installation Instructions:

### Step 1: Upload Files

1. Create a folder called livechat in your website root
2. Upload all the files maintaining the folder structure
3. Set folder permissions to 755 or 777

### Step 2: Run Installation

1. Visit: yoursite.com/livechat/install.php
2. You should see "Installation complete! Default login: admin/admin123"
3. Delete install.php after running it

### Step 3: Add to Your Website
Add this code before the closing `</body>` tag on pages where you want the chat:
```php
<?php include 'livechat/widget.php'; ?>
```
Or use JavaScript inclusion:
```javascript
<script>
(function() {
    var iframe = document.createElement('iframe');
    iframe.src = '/livechat/widget.php'; // Ensure this path is correct
    iframe.style.cssText = 'position:fixed;bottom:0;right:0;width:100%;height:100%;border:none;pointer-events:none;z-index:9999';
    iframe.onload = function() {
        this.style.pointerEvents = 'auto';
        this.style.maxHeight = '1000px'; // or this.style.height = '1000px';
        this.style.maxWidth = '800px'; // or this.style.width = '800px';
    };
    document.body.appendChild(iframe);
})();
</script>
```
### Step 4: Access Admin Panel

- Visit: `yoursite.com/livechat/admin/`
- Default login: `admin` / `admin123`
- Change these credentials immediately from Settings!

## SMTP Configuration:
### The system supports popular email services:

- Gmail: smtp.gmail.com, Port: 587
- Yahoo: smtp.mail.yahoo.com, Port: 587
- Outlook: smtp-mail.outlook.com, Port: 587
- Third-party services: ServerSMTP.com, SendFox, etc.

### System Features:

- ✅ SQLite database (no MySQL needed)
- ✅ Optimized for free hosting (slow AJAX - 3-5 second intervals)
- ✅ Mobile responsive design
- ✅ Real-time notifications
- ✅ Email integration
- ✅ Secure admin authentication
- ✅ Customizable appearance

The system is fully ready to use. At this time, there are no plans to add new features. However, future updates may include UI enhancements for both the chat widget and the admin panel. Contributions are welcome! Feel free to open issues or submit pull requests.

## License

This project is licensed under the [MIT License](LICENSE).
