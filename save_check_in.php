<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['id'])) {
        $id_login = $input['id'];

        // Truy vấn để lấy user_id từ id_login
        $sql = "SELECT id FROM users WHERE id_login = ?";
        $stmt = $con->prepare($sql);
        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query.']);
            exit;
        }
        $stmt->bind_param("s", $id_login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            // Kiểm tra xem user đã có log trong ngày chưa
            $checkLogStmt = $con->prepare("SELECT id FROM login_logs WHERE user_id = ? AND login_date = CURDATE()");
            $checkLogStmt->bind_param("i", $user_id);
            $checkLogStmt->execute();
            $logResult = $checkLogStmt->get_result();

            if ($logResult->num_rows === 0) {
                // Thêm log mới với CURDATE() và NOW()
                $insertLogStmt = $con->prepare("INSERT INTO login_logs (user_id, login_date, login_time) VALUES (?, CURDATE(), NOW())");
                if ($insertLogStmt === false) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare insert log query.']);
                    exit;
                }
                $insertLogStmt->bind_param("i", $user_id);

                if ($insertLogStmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Check-in saved successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to save check-in data.']);
                }
                $insertLogStmt->close();
            } else {
                echo json_encode(['status' => 'info', 'message' => 'Check-in already recorded for today.']);
            }
            $checkLogStmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid id_login. User not found.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
