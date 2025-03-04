<?php
require 'vendor/autoload.php'; // Tải thư viện PHPMailer
require 'admin/inc/db_config.php'; // Kết nối cơ sở dữ liệu

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy danh sách email từ cơ sở dữ liệu
$query = "SELECT email FROM users";
$result = $con->query($query);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Không thể truy vấn cơ sở dữ liệu.']);
    exit;
}

// Chuẩn bị dữ liệu email
$emails = [];
while ($row = $result->fetch_assoc()) {
    $emails[] = $row['email'];
}

// Giá trị trung bình từ bảng dữ liệu
$sql_avg_temp = "SELECT AVG(temperature) AS avg_temperature FROM data_agricultural_smart";
$sql_avg_humidity = "SELECT AVG(humidity) AS avg_humidity FROM data_agricultural_smart";
$sql_avg_soilMoisture = "SELECT AVG(soilMoisture) AS avg_soilMoisture FROM data_agricultural_smart";
$sql_avg_gas = "SELECT AVG(gas) AS avg_gas FROM data_agricultural_smart";

$result_avg_temp = $con->query($sql_avg_temp);
$result_avg_humidity = $con->query($sql_avg_humidity);
$result_avg_soilMoisture = $con->query($sql_avg_soilMoisture);
$result_avg_gas = $con->query($sql_avg_gas);

$avg_temperature = round($result_avg_temp->fetch_assoc()['avg_temperature'], 2);
$avg_humidity = round($result_avg_humidity->fetch_assoc()['avg_humidity'], 2);
$avg_soilMoisture = round($result_avg_soilMoisture->fetch_assoc()['avg_soilMoisture'], 2);
$avg_gas = round($result_avg_gas->fetch_assoc()['avg_gas'], 2);

// Nội dung email
$email_body = "
    <h1>THÔNG BÁO GIÁ TRỊ TRUNG BÌNH</h1>
    <p><strong>Nhiệt độ trung bình:</strong> {$avg_temperature}°C</p>
    <p><strong>Độ ẩm trung bình:</strong> {$avg_humidity}%</p>
    <p><strong>Độ ẩm đất trung bình:</strong> {$avg_soilMoisture}</p>
    <p><strong>Khí gas trung bình:</strong> {$avg_gas} ppm</p>
";

try {
    foreach ($emails as $email) {
        $mail = new PHPMailer(true);

        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'trungminh203@gmail.com'; // Thay bằng email của bạn
        $mail->Password = 'erbf tkqb sbkd izoj';   // Thay bằng mật khẩu ứng dụng của bạn
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Thiết lập charset để hỗ trợ tiếng Việt
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('trungminh203@gmail.com', 'Hệ Thống');
        $mail->addAddress($email);

        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = 'Thông báo từ hệ thống';
        $mail->Body = $email_body;

        $mail->send();
    }

    echo json_encode(['status' => 'success', 'message' => 'Thông báo đã được gửi đến tất cả người dùng.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi gửi email: ' . $mail->ErrorInfo]);
}
