<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if (!isset($_SESSION['user_id']) || !isset($_GET['property_id']) || !isset($_GET['receiver_id'])) {
    die("Unauthorized access.");
}

$property_id = intval($_GET['property_id']);
$receiver_id = intval($_GET['receiver_id']);
$sender_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1580587771525-78b9dba3b914?fit=crop&w=1600&q=80') no-repeat center center fixed;
            background-size: cover;
            color: white;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(3px);
            z-index: -1;
        }

        .back-button {
            display: inline-block;
            margin: 20px 0 0 20px;
            padding: 10px 18px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #555;
        }

        .chat-container {
            max-width: 600px;
            margin: 30px auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 16px;
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
        }

        .chat-box {
            height: 350px;
            overflow-y: auto;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.1);
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            background-color: rgba(0,0,0,0.5);
            border-radius: 10px;
            max-width: 80%;
            word-wrap: break-word;
            white-space: pre-wrap;
            overflow-wrap: break-word;
        }

        .sent {
            text-align: right;
            margin-left: auto;
            background-color: #28a745;
            color: white;
        }

        .received {
            text-align: left;
            background-color: #444;
        }

        .chat-form {
            display: flex;
            margin-top: 15px;
        }

        .chat-form input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            border: none;
            margin-right: 10px;
            font-size: 16px;
        }

        .chat-form button {
            padding: 10px 20px;
            background-color: #28a745;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            color: white;
            cursor: pointer;
        }

        .chat-form button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<a href="view_properties.php" class="back-button">← Back to Properties</a>
<div class="chat-container">
    <h2 style="text-align:center;">Chat</h2>
    <div class="chat-box" id="chat-box"></div>
    <form id="chat-form" class="chat-form">
        <input type="text" id="message" placeholder="Type your message..." autocomplete="off" required>
        <button type="submit">Send</button>
    </form>
</div>

<script>
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message');
    const receiverId = <?php echo $receiver_id; ?>;
    const propertyId = <?php echo $property_id; ?>;

    function fetchMessages() {
        fetch(`fetch_messages.php?receiver_id=${receiverId}&property_id=${propertyId}`)
            .then(response => response.text())
            .then(data => {
                chatBox.innerHTML = data;
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const msg = messageInput.value;
        fetch('send_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `message=${encodeURIComponent(msg)}&receiver_id=${receiverId}&property_id=${propertyId}`
        }).then(() => {
            messageInput.value = '';
            fetchMessages();
        });
    });

    setInterval(fetchMessages, 1500);
    window.onload = fetchMessages;
</script>
</body>
</html>
