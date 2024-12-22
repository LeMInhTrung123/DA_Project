<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_id'])) {
    echo "
    <div style='
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f8f9fa;
    '>
        <div style='
            text-align: center;
            background: white;
            padding: 40px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        '>
            <h2 style='
                font-size: 24px;
                font-weight: bold;
                color: #333;
                margin-bottom: 20px;
            '>Bạn cần đăng nhập để sử dụng chức năng này</h2>
            <a href='index.php' style='
                text-decoration: none;
                background-color: #007bff;
                color: white;
                padding: 12px 30px;
                border-radius: 50px;
                font-size: 18px;
                font-weight: bold;
                display: inline-block;
                transition: background-color 0.3s ease;
            ' onmouseover=\"this.style.backgroundColor='#0056b3'\" onmouseout=\"this.style.backgroundColor='#007bff'\">
                Đăng nhập ngay
            </a>
        </div>
    </div>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <?php require('inc/links.php');  ?>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .chat-container {
            max-width: 900px;
            margin: 50px auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            height: 80vh;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .chat-header {
            text-align: center;
            background-color: #007bff;
            color: white;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .chat-messages::-webkit-scrollbar {
            width: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 10px;
        }

        .message {
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 20px;
            max-width: 75%;
            position: relative;
            display: inline-block;
            clear: both;
        }

        .user-message {
            background-color: #e3f2fd;
            align-self: flex-end;
            border-radius: 15px 15px 5px 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            float: right;
        }

        .other-message {
            background-color: #ffe7e7;
            align-self: flex-start;
            border-radius: 15px 15px 15px 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            float: left;
        }

        .chat-input {
            display: flex;
            border-top: 1px solid #ddd;
            padding: 10px;
            background-color: #f0f0f0;
        }

        .chat-input textarea {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 10px 15px;
            resize: none;
            height: 50px;
            outline: none;
        }

        .chat-input textarea:focus {
            border-color: #007bff;
        }

        .chat-input button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            margin-left: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .chat-input button:hover {
            background-color: #0056b3;
        }

        .chat-timestamp {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
            text-align: right;
        }
    </style>
</head>

<body>
    <?php require('inc/header.php'); ?>

    <div class="chat-container">
        <div class="chat-header">Chat Hệ Thống</div>
        <div class="chat-messages" id="chat-messages"></div>
        <div class="chat-input">
            <textarea id="chat-input" placeholder="Nhập tin nhắn..."></textarea>
            <button id="send-btn" onclick="sendMessage()">Gửi</button>
        </div>
    </div>

    <script>
        function loadMessages() {
            fetch('admin/ajax/chat_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=load_messages'
            })
            .then(response => response.json())
            .then(data => {
                const chatMessages = document.getElementById('chat-messages');
                const userId = "<?php echo $_SESSION['user_id']; ?>";
                chatMessages.innerHTML = data.messages.map(msg => `
                    <div class="message ${msg.user_id == userId ? 'user-message' : 'other-message'}">
                        <strong>${msg.sender_name}:</strong> ${msg.message}
                        <div class="chat-timestamp">${new Date(msg.created_at).toLocaleString()}</div>
                    </div>
                `).join('');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(error => console.error('Lỗi:', error));
        }

        function sendMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (!message) return;

            fetch('admin/ajax/chat_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=send_message&message=${encodeURIComponent(message)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    input.value = '';
                    loadMessages();
                } else {
                    alert('Không thể gửi tin nhắn');
                }
            })
            .catch(error => console.error('Lỗi:', error));
        }

        setInterval(loadMessages, 2000);
        loadMessages();
        //Bấm Enter để gửi
        document.getElementById('chat-input').addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) { // Kiểm tra nếu Enter được nhấn (không kèm Shift)
            event.preventDefault(); // Ngăn xuống dòng
            document.getElementById('send-btn').click(); // Kích hoạt nút gửi
        }
    });
    </script>
    <script>
        function loginUser() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            fetch('admin/ajax/login_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `login_user=1&email=${email}&password=${password}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        window.location.href = 'chat.php'; // Chuyển hướng đến trang chính sau khi đăng nhập thành công
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                });
        }
    </script>
    
    <?php require('inc/footer.php'); ?>
</body>

</html>