<?php
$hname = 'localhost';
$uname = 'root';
$pass = '';
$db = 'demo';

$connection = new mysqli($hname, $uname, $pass, $db);

if ($connection->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Kết nối thất bại: ' . $connection->connect_error]));
}

// Lấy tham số bộ lọc từ URL
$filter = $_GET['filter'] ?? 'hour';

// Truy vấn dữ liệu dựa trên bộ lọc
$query = '';

if ($filter === 'hour') {
    $query = "
        SELECT 
            DATE_FORMAT(measurement_time, '%H:00') AS formatted_time,
            AVG(temperature) AS avg_temperature,
            AVG(humidity) AS avg_humidity,
            AVG(soilMoisture) AS avg_soilMoisture,
            AVG(gas) AS avg_gas
        FROM data_agricultural_smart
        GROUP BY formatted_time
        ORDER BY MAX(measurement_time) DESC;
    ";
} elseif ($filter === 'day') {
    $query = "
        SELECT 
            DATE(measurement_time) AS formatted_date,
            AVG(temperature) AS avg_temperature,
            AVG(humidity) AS avg_humidity,
            AVG(soilMoisture) AS avg_soilMoisture,
            AVG(gas) AS avg_gas
        FROM data_agricultural_smart
        GROUP BY formatted_date
        ORDER BY MAX(measurement_time) DESC;
    ";
} elseif ($filter === 'month') {
    $query = "
        SELECT 
            DATE_FORMAT(measurement_time, '%Y-%m') AS formatted_month,
            AVG(temperature) AS avg_temperature,
            AVG(humidity) AS avg_humidity,
            AVG(soilMoisture) AS avg_soilMoisture,
            AVG(gas) AS avg_gas
        FROM data_agricultural_smart
        GROUP BY formatted_month
        ORDER BY MAX(measurement_time) DESC;
    ";
}

$result = $connection->query($query);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi truy vấn: ' . $connection->error]);
    exit;
}

// Chuẩn bị dữ liệu để trả về JSON
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'label' => $row['formatted_time'] ?? $row['formatted_date'] ?? $row['formatted_month'], // Chọn nhãn phù hợp
        'avg_temperature' => round($row['avg_temperature'], 2),
        'avg_humidity' => round($row['avg_humidity'], 2),
        'avg_soilMoisture' => round($row['avg_soilMoisture'], 2),
        'avg_gas' => round($row['avg_gas'], 2),
    ];
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>
