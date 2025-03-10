<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['adminLogin']) && !isset($_SESSION['user_email'])) {
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
            /* Bên phải */
            align-self: flex-end;
            border-radius: 15px 15px 5px 15px;
            float: right;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .other-message {
            background-color: #ffe7e7;
            /* Bên trái */
            align-self: flex-start;
            border-radius: 15px 15px 15px 5px;
            float: left;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .chat-icons {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            gap: 20px;
        }

        .chat-icons button {
            border: none;
            background: none;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            color: #333;
            transition: transform 0.2s ease;
        }

        .chat-icons button:hover {
            transform: scale(1.1);
        }

        .chat-icons i {
            display: block;
            margin-bottom: 5px;
        }
        
        .status-item {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }
        
        .toggle-btn {
            border: none;
            background-color: #f0f0f0;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .toggle-btn:hover {
            transform: scale(1.05);
        }
        
        .toggle-btn[data-state="on"] {
            background-color: #28a745;
            color: white;
        }
        
        .toggle-btn[data-state="off"] {
            background-color: #dc3545;
            color: white;
        }

        
        .control-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #007bff;
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
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
    <div class="chat-icons">
        <button onclick="sendQuickMessage('Phân bón đã được bón cho cây')">
            <i class="fas fa-seedling" style="font-size: 24px; color: #28a745;"></i> Phân Bón
        </button>
        <button onclick="sendQuickMessage('Đã trồng thêm cây mới')">
            <i class="fas fa-tree" style="font-size: 24px; color: #007bff;"></i> Trồng Cây
        </button>
        <button onclick="sendQuickMessage('Hệ thống tưới nước đã hoạt động')">
            <i class="fas fa-water" style="font-size: 24px; color: #17a2b8;"></i> Tưới Nước
        </button>
        <button onclick="sendQuickMessage('Quạt đã bật')">
            <i class="fas fa-fan" style="font-size: 24px; color: #ffc107;"></i> Bật Quạt
        </button>
        <button onclick="sendQuickMessage('Cửa đã mở')">
            <i class="fas fa-door-open" style="font-size: 24px; color: #ff5722;"></i> Mở Cửa
        </button>
        <button onclick="sendQuickMessage('Rèm đã được kéo')">
            <i class="fas fa-window-maximize" style="font-size: 24px; color: #6c757d;"></i> Kéo Rèm
        </button>
    </div>
    <div class="control-title">
        Điều khiển
    </div>
    
    <div class="status-item">
        <button class="toggle-btn" data-state="off" onclick="toggleStatus(this, 'water')">
            <i class="fas fa-water" style="font-size: 24px; color: #17a2b8;"></i> Tưới Nước (Tắt)
        </button>
        <button class="toggle-btn" data-state="off" onclick="toggleStatus(this, 'fan')">
            <i class="fas fa-fan" style="font-size: 24px; color: #ffc107;"></i> Bật Quạt (Tắt)
        </button>
        <button class="toggle-btn" data-state="off" onclick="toggleStatus(this, 'door')">
            <i class="fas fa-door-open" style="font-size: 24px; color: #ff5722;"></i> Mở Cửa (Tắt)
        </button>
        <button class="toggle-btn" data-state="off" onclick="toggleStatus(this, 'curtain')">
            <i class="fas fa-window-maximize" style="font-size: 24px; color: #6c757d;"></i> Kéo Rèm (Tắt)
        </button>
    </div>



    <script>
    
        const deviceStates = {
            water: false,
            fan: false,
            door: false,
            curtain: false
        };
        
        async function toggleStatus(button, device) {
            const currentState = button.getAttribute("data-state");
        
            // Chuyển đổi trạng thái thiết bị
            const newState = currentState === "off";
            deviceStates[device] = newState;
        
            // Cập nhật giao diện nút bấm
            button.setAttribute("data-state", newState ? "on" : "off");
            button.innerHTML = `<i class="${button.querySelector('i').classList.value}"></i> ${getDeviceLabel(device)} (${newState ? "Bật" : "Tắt"})`;
            button.style.backgroundColor = newState ? "#28a745" : "#dc3545"; // Xanh khi bật, đỏ khi tắt
            console.log(deviceStates)
        
            // Gửi API cập nhật trạng thái
            try {
                const response = await fetch("http://localhost:8080/control", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(deviceStates)
                });
        
                const result = await response.json();
                console.log("API Response:", result);
            } catch (error) {
                console.error("Lỗi khi gửi yêu cầu API:", error);
                alert("Có lỗi xảy ra khi cập nhật trạng thái!");
            }
        }
        
        // Chuyển đổi tên hiển thị của thiết bị
        function getDeviceLabel(device) {
            switch (device) {
                case "water": return "Tưới Nước";
                case "fan": return "Bật Quạt";
                case "door": return "Mở Cửa";
                case "curtain": return "Kéo Rèm";
                default: return "";
            }
        }


        function loadMessages() {
            fetch('admin/ajax/chat_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=load_messages'
                })
                .then(response => response.json())
                .then(data => {
                    const chatMessages = document.getElementById('chat-messages');
                    const currentUser = "<?php echo $_SESSION['user_email'] ?? 'Admin'; ?>"; // Lấy email user hoặc "Admin"
                    chatMessages.innerHTML = data.messages.map(msg => `
            <div class="message ${msg.sender_name === currentUser ? 'user-message' : 'other-message'}">
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
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
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

        // Gọi hàm loadMessages định kỳ
        setInterval(loadMessages, 2000);
        loadMessages();
        //Bấm Enter để gửi
        document.getElementById('chat-input').addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) { // Kiểm tra nếu Enter được nhấn (không kèm Shift)
                event.preventDefault(); // Ngăn xuống dòng
                document.getElementById('send-btn').click(); // Kích hoạt nút gửi
            }
        });

        function sendQuickMessage(message) {
            fetch('admin/ajax/chat_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=send_message&message=${encodeURIComponent(message)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadMessages(); // Cập nhật tin nhắn
                    } else {
                        alert('Không thể gửi tin nhắn');
                    }
                })
                .catch(error => console.error('Lỗi:', error));
        }
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