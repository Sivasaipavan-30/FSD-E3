/* chatbot.js - handle the bot interactions */
const chatbotKnowledge = {
    greetings: ["hello", "hi", "hey", "hola"],
    booking: ["book", "ticket", "register", "how to book"],
    receipt: ["receipt", "download", "qr", "qr code", "ticket download", "download receipt"],
    events: ["events", "trending", "fest", "concert", "technical", "cultural", "upcoming events"],
    admin: ["admin", "organize", "management", "staff", "admin help"],
    help: ["help", "support", "contact", "issue"],
};

// What the bot says for each category
const chatbotResponses = {
    greetings: "Hello there! 🏛️ I'm your Smart Campus Assistant. How can I help you today?",
    booking: "To book a ticket, simply browse the events on the home page, click 'Book Now', and follow the steps to enter student details. It's that easy!",
    receipt: "You can find your receipts and QR codes in your 'Booking History' section on the home page once you're logged in. Just click 'Download'!",
    events: "We have a variety of technical and cultural events! Check out the 'Trending Events' section to see what's happening on campus right now.",
    admin: "Admins can manage events, view all bookings, and even broadcast messages from the Admin Dashboard. If you're an admin, you'll see these features after logging in.",
    help: "Need help? You can reach our technical support team at support@college.university or call +91 80 1234 5678.",
    default: "I'm not sure I understand. Could you try rephrasing? You can ask about bookings, events, or how to download your receipt! 🎓",
};

// Open/close the chat window
function toggleChatbot() {
    const window = document.getElementById('chatbot-window');
    window.classList.toggle('active');
    
    if (window.classList.contains('active')) {
        const input = document.getElementById('chatbot-input');
        input.focus();
        
        // Add welcome message if empty
        const messages = document.getElementById('chatbot-messages');
        if (messages.children.length === 0) {
            addMessage("Hello! 🎓 I'm your Smart Campus Assistant. Ask me anything about events or bookings!", 'bot');
            renderSuggestions(['How to book?', 'Download Receipt', 'Upcoming Events', 'Admin Help']);
        }
    }
}

function renderSuggestions(suggestions) {
    const container = document.getElementById('chatbot-suggestions');
    container.innerHTML = suggestions.map(s => `<div class="suggestion-chip" onclick="handleSuggestionClick('${s}')">${s}</div>`).join('');
}

function handleSuggestionClick(text) {
    document.getElementById('chatbot-input').value = text;
    handleChatSubmit();
    document.getElementById('chatbot-suggestions').innerHTML = ''; // Clear suggestions after use
}


// Add a message bubble to the screen
function addMessage(text, sender) {
    const messagesContainer = document.getElementById('chatbot-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender}`;
    messageDiv.textContent = text;
    
    // Typing indicator logic
    const typing = document.getElementById('chatbot-typing');
    if (sender === 'bot') {
        typing.style.display = 'flex';
        setTimeout(() => {
            typing.style.display = 'none';
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 800);
    } else {
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

function getBotResponse(input) {
    const lowerInput = input.toLowerCase();
    
    for (const [key, keywords] of Object.entries(chatbotKnowledge)) {
        if (keywords.some(keyword => lowerInput.includes(keyword))) {
            return chatbotResponses[key];
        }
    }
    return chatbotResponses.default;
}

function handleChatSubmit() {
    const input = document.getElementById('chatbot-input');
    const text = input.value.trim();
    
    if (text) {
        addMessage(text, 'user');
        input.value = '';
        
        setTimeout(() => {
            const response = getBotResponse(text);
            addMessage(response, 'bot');
            
            // If default response, show suggestions again to guide user
            if (response === chatbotResponses.default) {
                setTimeout(() => renderSuggestions(['How to book?', 'Download Receipt', 'Upcoming Events']), 1000);
            }
        }, 500);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');
    
    if (input) {
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') handleChatSubmit();
        });
    }
    
    if (sendBtn) {
        sendBtn.addEventListener('click', handleChatSubmit);
    }
});
