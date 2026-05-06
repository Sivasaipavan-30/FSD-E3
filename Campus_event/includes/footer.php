    <!-- Chatbot Component -->
    <div class="chatbot-container">
        <div id="chatbot-window" class="chatbot-window">
            <div class="chatbot-header">
                <h3><span class="chatbot-status"></span> Smart Assistant</h3>
                <span style="cursor: pointer; font-size: 1.5rem;" onclick="toggleChatbot()">&times;</span>
            </div>
            <div id="chatbot-messages" class="chatbot-messages">
                <!-- Messages will be injected here -->
            </div>
            <div id="chatbot-suggestions" class="chatbot-suggestions">
                <!-- Suggestions will be injected here -->
            </div>
            <div id="chatbot-typing" class="typing-indicator">
                <span></span><span></span><span></span>
            </div>
            <div class="chatbot-input-area">
                <input type="text" id="chatbot-input" class="chatbot-input" placeholder="Type a message...">
                <button id="chatbot-send" class="chatbot-send">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
        </div>
        <button class="chatbot-fab" onclick="toggleChatbot()">
            <span>💬</span>
        </button>
    </div>

    <link rel="stylesheet" href="assets/css/chatbot.css">
    <script src="assets/js/chatbot.js"></script>

    <footer style="text-align: center; padding: 3rem; color: var(--text-muted); border-top: 1px solid var(--glass-border); margin-top: 5rem;">
        <p>&copy; 2026 Smart Campus Event Management System. All rights reserved.</p>
    </footer>

    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
