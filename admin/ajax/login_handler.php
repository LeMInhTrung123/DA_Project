<?php
require('../inc/db_config.php');
require('../inc/essentials.php');

if (isset($_POST['login_user'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $stmt = $con->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        

        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            

            // Kiểm tra session đã được lưu thành công
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
