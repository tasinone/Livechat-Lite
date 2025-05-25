class LiveChat {
    constructor() {
        this.chatId = null;
        this.lastMessageId = 0;
        this.pollInterval = null;
        this.hasNewMessage = false;
        
        // Determine the correct API path based on how the widget is loaded
        this.apiPath = this.getApiPath();
        
        this.initializeElements();
        this.bindEvents();
        this.checkForNewMessages();
    }
    
    getApiPath() {
        // Check if we're in an iframe (JS embed) or directly included (PHP embed)
        if (window.self !== window.top) {
            // We're in an iframe, use relative paths
            return '';
        } else {
            // We're included directly, need to add livechat prefix
            return '/livechat/';
        }
    }
    
    initializeElements() {
        this.chatBubble = document.getElementById('chat-bubble');
        this.chatPopup = document.getElementById('chat-popup');
        this.closeBtn = document.getElementById('close-chat');
        this.contactForm = document.getElementById('contact-form');
        this.nameInput = document.getElementById('user-name');
        this.emailInput = document.getElementById('user-email');
        this.startChatBtn = document.getElementById('start-chat');
        this.messagesContainer = document.getElementById('chat-messages');
        this.messageForm = document.getElementById('message-form');
        this.messageInput = document.getElementById('message-input');
        this.sendBtn = document.getElementById('send-message');
        this.notificationDot = document.getElementById('notification-dot');
        this.notificationSound = document.getElementById('notification-sound');
    }
    
    bindEvents() {
        this.chatBubble.addEventListener('click', () => this.toggleChat());
        this.closeBtn.addEventListener('click', () => this.closeChat());
        this.startChatBtn.addEventListener('click', () => this.startChat());
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
    }
    
    toggleChat() {
        const isVisible = this.chatPopup.style.display !== 'none';
        this.chatPopup.style.display = isVisible ? 'none' : 'block';
        
        if (!isVisible && this.hasNewMessage) {
            this.hasNewMessage = false;
            this.notificationDot.style.display = 'none';
        }
    }
    
    closeChat() {
        this.chatPopup.style.display = 'none';
    }
    
    async startChat() {
        const name = this.nameInput.value.trim();
        const email = this.emailInput.value.trim();
        
        if (!name || !email) {
            alert('Please fill in all fields');
            return;
        }
        
        try {
            const response = await fetch(this.apiPath + 'api/send_message.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    name: name,
                    email: email,
                    message: 'Chat started'
                })
            });
            
            const data = await response.json();
            if (data.success) {
                this.chatId = data.chat_id;
                this.showChatInterface();
                this.startPolling();
            }
        } catch (error) {
            console.error('Error starting chat:', error);
        }
    }
    
    showChatInterface() {
        this.contactForm.style.display = 'none';
        this.messagesContainer.style.display = 'block';
        this.messageForm.style.display = 'flex';
        document.getElementById('banner-section').style.display = 'none';
    }
    
    async sendMessage() {
        const message = this.messageInput.value.trim();
        if (!message || !this.chatId) return;
        
        try {
            const response = await fetch(this.apiPath + 'api/send_message.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    name: this.nameInput.value,
                    email: this.emailInput.value,
                    message: message
                })
            });
            
            if (response.ok) {
                this.messageInput.value = '';
                this.loadNewMessages();
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }
    
    async loadNewMessages() {
        if (!this.chatId) return;
        
        try {
            const response = await fetch(`${this.apiPath}api/get_messages.php?chat_id=${this.chatId}&last_id=${this.lastMessageId}`);
            const data = await response.json();
            
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(message => {
                    this.displayMessage(message);
                    this.lastMessageId = Math.max(this.lastMessageId, message.id);
                    
                    if (message.is_admin && this.chatPopup.style.display === 'none') {
                        this.showNotification();
                    }
                });
                
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }
    
    displayMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${message.is_admin ? 'admin' : 'user'}`;
        
        const time = new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        messageDiv.innerHTML = `
            <div class="message-bubble">${this.escapeHtml(message.message)}</div>
            <div class="message-time">${time}</div>
        `;
        
        this.messagesContainer.appendChild(messageDiv);
    }
    
    showNotification() {
        this.hasNewMessage = true;
        this.notificationDot.style.display = 'block';
        
        try {
            this.notificationSound.play().catch(e => console.log('Could not play sound'));
        } catch (e) {
            console.log('Sound not available');
        }
    }
    
    checkForNewMessages() {
        const email = localStorage.getItem('chat_email');
        if (email) {
            // Check for existing chat and new messages
            // This would require additional API endpoint
        }
    }
    
    startPolling() {
        this.pollInterval = setInterval(() => {
            this.loadNewMessages();
        }, 3000); // Poll every 3 seconds to avoid hosting bans
    }
    
    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize chat when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new LiveChat();
});