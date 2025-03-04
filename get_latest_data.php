<?php
// Thông tin kết nối cơ sở dữ liệu
$hname = 'localhost';
$uname = 'root';
$pass = '';
$db = 'demo'; // database name
$connection = new mysqli($hname, $uname, $pass, $db);

// Kết nối đến MySQL
$connection = new mysqli($hname, $uname, $pass, $db);

// Kiểm tra kết nối
if ($connection->connect_error) {
    die(json_encode(["status" => "error", "message" => "Kết nối thất bại: " . $connection->connect_error]));
}

// Truy vấn lấy dữ liệu mới nhất
$query = "SELECT temperature, humidity, light, soilMoisture, gas FROM data_agricultural_smart ORDER BY id DESC LIMIT 1";

$result = $connection->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["status" => "success", "data" => $row]);
} else {
    echo json_encode(["status" => "error", "message" => "Không có dữ liệu"]);
}

// Đóng kết nối
$connection->close();
?>
