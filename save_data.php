<?php
$hname = 'localhost';
$uname = 'root';
$pass = '';
$db = 'demo';

$connection = new mysqli($hname, $uname, $pass, $db);

if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Kiểm tra nếu dữ liệu không hợp lệ
    if (!$data || !isset($data['temperature'], $data['humidity'], $data['light'], $data['soilMoisture'], $data['gas'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }

    // Gán giá trị
    $temperature = (float)$data['temperature'];
    $humidity = (float)$data['humidity'];
    $light = (string)$data['light'];
    $soilMoisture = (float)$data['soilMoisture'];
    $gas = (float)$data['gas'];

    // Câu lệnh SQL
    $sql = "INSERT INTO data_agricultural_smart (temperature, humidity, light, soilMoisture, gas, measurement_time)
            VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $connection->prepare($sql);

    // Kiểm tra nếu chuẩn bị truy vấn thất bại
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => $connection->error]);
        exit;
    }

    // Chỉ cần 5 tham số vì measurement_time được xử lý bằng NOW()
    $stmt->bind_param("ddssd", $temperature, $humidity, $light, $soilMoisture, $gas);

    // Thực thi và kiểm tra lỗi
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }

    // Đóng kết nối
    $stmt->close();
    $connection->close();
}
?>
