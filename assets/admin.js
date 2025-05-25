class AdminChat {
    constructor() {
        this.currentChatId = null;
        this.lastMessageId = 0;
        this.chats = [];
        this.pollInterval = null;
        
        this.initializeElements();
        this.loadChats();
        this.startPolling();
    }
    
    initializeElements() {
        this.chatsContainer = document.getElementById('chats-container');
        this.chatHeader = document.getElementById('chat-header');
        this.messagesContainer = document.getElementById('messages-container');
        this.replyForm = document.getElementById('reply-form');
        this.replyInput = document.getElementById('reply-input');
        this.sendReplyBtn = document.getElementById('send-reply');
        this.notificationSound = document.getElementById('notification-sound');
        
        this.sendReplyBtn?.addEventListener('click', () => this.sendReply());
        this.replyInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendReply();
        });
    }
    
    async loadChats() {
        try {
            const response = await fetch('../api/get_chats.php');
            const data = await response.json();
            
            if (data.chats) {
                this.chats = data.chats;
                this.renderChats();
            }
        } catch (error) {
            console.error('Error loading chats:', error);
        }
    }
    
    renderChats() {
        if (!this.chatsContainer) return;
        
        this.chatsContainer.innerHTML = '';
        
        this.chats.forEach(chat => {
            const chatDiv = document.createElement('div');
            chatDiv.className = `chat-item ${chat.has_unread ? 'unread' : ''}`;
            chatDiv.onclick = () => this.selectChat(chat.id);
            
            const time = new Date(chat.last_message_time).toLocaleTimeString([], {
                hour: '2-digit', 
                minute: '2-digit'
            });
            
            chatDiv.innerHTML = `
                <div class="chat-name">${this.escapeHtml(chat.name)}</div>
                <div class="chat-email">${this.escapeHtml(chat.email)}</div>
                <div class="chat-preview">${this.escapeHtml(chat.last_message).substring(0, 50)}... • ${time}</div>
            `;
            
            this.chatsContainer.appendChild(chatDiv);
        });
    }
    
    async selectChat(chatId) {
        // Remove active class from all chat items
        document.querySelectorAll('.chat-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add active class to selected chat
        event.target.closest('.chat-item').classList.add('active');
        event.target.closest('.chat-item').classList.remove('unread');
        
        this.currentChatId = chatId;
        const chat = this.chats.find(c => c.id === chatId);
        
        if (chat) {
            this.chatHeader.textContent = `${chat.name} (${chat.email})`;
            this.replyForm.style.display = 'flex';
            await this.loadMessages();
            
            // Mark as read
            const chatIndex = this.chats.findIndex(c => c.id === chatId);
            if (chatIndex !== -1) {
                this.chats[chatIndex].has_unread = 0;
            }
        }
    }
    
    async loadMessages() {
        if (!this.currentChatId) return;
        
        try {
            const response = await fetch(`../api/get_messages.php?chat_id=${this.currentChatId}`);
            const data = await response.json();
            
            if (data.messages) {
                this.messagesContainer.innerHTML = '';
                data.messages.forEach(message => {
                    this.displayMessage(message);
                    this.lastMessageId = Math.max(this.lastMessageId, message.id);
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
        
        const time = new Date(message.created_at).toLocaleTimeString([], {
            hour: '2-digit', 
            minute: '2-digit'
        });
        
        messageDiv.innerHTML = `
            <div class="message-bubble">${this.escapeHtml(message.message)}</div>
            <div class="message-time">${time}</div>
        `;
        
        this.messagesContainer.appendChild(messageDiv);
    }
    
    async sendReply() {
        const message = this.replyInput.value.trim();
        if (!message || !this.currentChatId) return;
        
        try {
            const response = await fetch('../api/admin_reply.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    chat_id: this.currentChatId,
                    message: message
                })
            });
            
            if (response.ok) {
                this.replyInput.value = '';
                this.loadMessages();
                this.loadChats(); // Refresh chat list to update order
            }
        } catch (error) {
            console.error('Error sending reply:', error);
        }
    }
    
    async checkForNewMessages() {
        try {
            const response = await fetch('../api/get_chats.php');
            const data = await response.json();
            
            if (data.chats) {
                const oldChats = [...this.chats];
                this.chats = data.chats;
                
                // Check for new messages
                let hasNewMessage = false;
                this.chats.forEach(chat => {
                    const oldChat = oldChats.find(c => c.id === chat.id);
                    if (!oldChat || new Date(chat.last_message_at) > new Date(oldChat.last_message_at)) {
                        hasNewMessage = true;
                    }
                });
                
                if (hasNewMessage) {
                    this.playNotificationSound();
                    this.renderChats();
                    
                    // Reload current chat messages if viewing a chat
                    if (this.currentChatId) {
                        this.loadMessages();
                    }
                }
            }
        } catch (error) {
            console.error('Error checking for new messages:', error);
        }
    }
    
    playNotificationSound() {
        try {
            this.notificationSound?.play().catch(e => console.log('Could not play sound'));
        } catch (e) {
            console.log('Sound not available');
        }
    }
    
    startPolling() {
        this.pollInterval = setInterval(() => {
            this.checkForNewMessages();
        }, 5000); // Poll every 5 seconds
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

// Initialize admin chat when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AdminChat();
});