<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Chat System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .chat-container {
            width: 600px;
            max-width: 95%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .chat-header {
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            background: #f9f9f9;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 10px;
            max-width: 80%;
            word-wrap: break-word;
        }
        .message.sent {
            background: #4CAF50;
            color: white;
            margin-left: auto;
        }
        .message.received {
            background: #e9e9e9;
            color: #333;
            margin-right: auto;
        }
        .message .timestamp {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 5px;
            display: block;
        }
        .message .username {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .chat-input {
            display: flex;
            padding: 15px;
            background: white;
            border-top: 1px solid #ddd;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
            font-size: 14px;
        }
        .chat-input button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .chat-input button:hover {
            background: #45a049;
        }
        .user-controls {
            display: flex;
            padding: 10px 15px;
            gap: 10px;
            background: #f5f5f5;
            border-top: 1px solid #ddd;
        }
        .user-controls input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .error {
            color: red;
            padding: 10px;
            text-align: center;
        }
        .success {
            color: green;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">💬 Simple Chat</div>
        
        <div id="userControls" class="user-controls">
            <input type="text" id="usernameInput" placeholder="Enter your username" value="User<?php echo rand(100, 999); ?>">
            <button onclick="setUsername()">Set Name</button>
        </div>
        
        <div id="chatMessages" class="chat-messages"></div>
        
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Type a message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        let currentUser = localStorage.getItem('chatUsername') || 'User' + Math.floor(Math.random() * 900 + 100);
        
        // Set username from input
        function setUsername() {
            const input = document.getElementById('usernameInput');
            if (input.value.trim()) {
                currentUser = input.value.trim();
                localStorage.setItem('chatUsername', currentUser);
                document.getElementById('usernameInput').value = currentUser;
                alert('Username set to: ' + currentUser);
            }
        }

        // Load username on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('usernameInput').value = currentUser;
            loadMessages();
            setInterval(loadMessages, 3000); // Auto-refresh every 3 seconds
        });

        // Send message
        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message) {
                alert('Please enter a message');
                return;
            }
            
            if (!currentUser) {
                alert('Please set a username first');
                return;
            }

            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    username: currentUser,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    loadMessages();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to send message');
            });
        }

        // Load messages
        function loadMessages() {
            fetch('get_messages.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('chatMessages');
                    container.innerHTML = '';
                    
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            const messageDiv = document.createElement('div');
                            messageDiv.className = 'message ' + (msg.username === currentUser ? 'sent' : 'received');
                            
                            const usernameSpan = document.createElement('span');
                            usernameSpan.className = 'username';
                            usernameSpan.textContent = msg.username + ':';
                            
                            const textSpan = document.createElement('span');
                            textSpan.textContent = msg.message;
                            
                            const timestampSpan = document.createElement('span');
                            timestampSpan.className = 'timestamp';
                            timestampSpan.textContent = new Date(msg.timestamp).toLocaleString();
                            
                            messageDiv.appendChild(usernameSpan);
                            messageDiv.appendChild(document.createElement('br'));
                            messageDiv.appendChild(textSpan);
                            messageDiv.appendChild(timestampSpan);
                            
                            container.appendChild(messageDiv);
                        });
                        
                        // Scroll to bottom
                        container.scrollTop = container.scrollHeight;
                    } else {
                        container.innerHTML = '<div style="text-align:center;color:#999;padding:20px;">No messages yet. Start chatting!</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                });
        }

        // Send on Enter key
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>