<?php
require('../../admin/inc/db_config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'load_messages') {
        $result = mysqli_query($con, "SELECT * FROM messages ORDER BY created_at ASC");
        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        echo json_encode(['status' => 'success', 'messages' => $messages]);
        exit;
    }
    
    if ($action === 'send_message') {
        $message = mysqli_real_escape_string($con, $_POST['message']);
        $userId = $_SESSION['user_id'];
        $senderName = $_SESSION['user_email'];

        $query = "INSERT INTO messages (user_id, sender_name, message) VALUES ('$userId', '$senderName', '$message')";
        if (mysqli_query($con, $query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi gửi tin nhắn']);
        }
        exit;
    }
}
?>