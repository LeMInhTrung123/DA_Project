<?php
require('../inc/db_config.php');
require('../inc/essentials.php');

// Kiểm tra nếu yêu cầu đăng nhập được gửi
if (isset($_POST['login_user'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Chuẩn bị và thực thi truy vấn để kiểm tra user
    $stmt = $con->prepare("SELECT id, email, password FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Query preparation failed.']);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu user tồn tại
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Xác thực mật khẩu
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            // Kiểm tra xem user đã được log trong ngày chưa
            $checkLogStmt = $con->prepare("SELECT id FROM login_logs WHERE user_id = ? AND login_date = CURDATE()");
            $checkLogStmt->bind_param("i", $user['id']);
            $checkLogStmt->execute();
            $logResult = $checkLogStmt->get_result();

            // Nếu chưa có log trong ngày, thêm mới
            if ($logResult->num_rows === 0) {
                $insertLogStmt = $con->prepare("INSERT INTO login_logs (user_id, login_date, login_time) VALUES (?, CURDATE(), NOW())");
                $insertLogStmt->bind_param("i", $user['id']);
                $insertLogStmt->execute();
                $insertLogStmt->close();
            }
            $checkLogStmt->close();

            // Đảm bảo session được lưu và trả về thành công
            if (isset($_SESSION['user_email'])) {
                echo json_encode(['status' => 'success', 'message' => 'Đăng nhập thành công.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Không thể lưu session.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu không chính xác.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email không tồn tại.']);
    }

    $stmt->close();
}
?>
