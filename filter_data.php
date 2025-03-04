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
        WITH hours AS (
            SELECT 0 AS hour UNION ALL
            SELECT 1 UNION ALL
            SELECT 2 UNION ALL
            SELECT 3 UNION ALL
            SELECT 4 UNION ALL
            SELECT 5 UNION ALL
            SELECT 6 UNION ALL
            SELECT 7 UNION ALL
            SELECT 8 UNION ALL
            SELECT 9 UNION ALL
            SELECT 10 UNION ALL
            SELECT 11 UNION ALL
            SELECT 12 UNION ALL
            SELECT 13 UNION ALL
            SELECT 14 UNION ALL
            SELECT 15 UNION ALL
            SELECT 16 UNION ALL
            SELECT 17 UNION ALL
            SELECT 18 UNION ALL
            SELECT 19 UNION ALL
            SELECT 20 UNION ALL
            SELECT 21 UNION ALL
            SELECT 22 UNION ALL
            SELECT 23
        )
        SELECT 
            CONCAT(RIGHT(CONCAT('0', h.hour), 2), ':00') AS formatted_date,
            COALESCE(AVG(das.temperature), 0) AS avg_temperature,
            COALESCE(AVG(das.humidity), 0) AS avg_humidity,
            COALESCE(AVG(das.soilMoisture), 0) AS avg_soilMoisture,
            COALESCE(AVG(das.gas), 0) AS avg_gas
        FROM hours h
        LEFT JOIN data_agricultural_smart das
            ON HOUR(das.measurement_time) = h.hour
            AND DATE(das.measurement_time) = CURRENT_DATE()
        GROUP BY h.hour
        ORDER BY h.hour ASC;
    ";
}  elseif ($filter === 'day') {
    $query = "
        WITH days AS (
            SELECT CURDATE() - INTERVAL seq DAY AS day
            FROM (SELECT 0 AS seq UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
                  SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL
                  SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL
                  SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL
                  SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
                  SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL
                  SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL
                  SELECT 28 UNION ALL SELECT 29) seqs
        )
        SELECT 
            DATE_FORMAT(d.day, '%Y-%m-%d') AS formatted_date,
            COALESCE(AVG(das.temperature), 0) AS avg_temperature,
            COALESCE(AVG(das.humidity), 0) AS avg_humidity,
            COALESCE(AVG(das.soilMoisture), 0) AS avg_soilMoisture,
            COALESCE(AVG(das.gas), 0) AS avg_gas
        FROM days d
        LEFT JOIN data_agricultural_smart das
            ON DATE(das.measurement_time) = d.day
        GROUP BY d.day
        ORDER BY d.day ASC;
    ";
} elseif ($filter === 'month') {
    $query = "
        WITH months AS (
            SELECT DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL -seq MONTH), '%Y-%m') AS month
            FROM (SELECT 0 AS seq UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
                  SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL
                  SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11) seqs
        )
        SELECT 
            m.month AS formatted_date,
            COALESCE(AVG(das.temperature), 0) AS avg_temperature,
            COALESCE(AVG(das.humidity), 0) AS avg_humidity,
            COALESCE(AVG(das.soilMoisture), 0) AS avg_soilMoisture,
            COALESCE(AVG(das.gas), 0) AS avg_gas
        FROM months m
        LEFT JOIN data_agricultural_smart das
            ON DATE_FORMAT(das.measurement_time, '%Y-%m') = m.month
        GROUP BY m.month
        ORDER BY m.month ASC;
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
        'label' => $row['formatted_date'], // Đồng bộ nhãn là formatted_date
        'avg_temperature' => round($row['avg_temperature'], 2),
        'avg_humidity' => round($row['avg_humidity'], 2),
        'avg_soilMoisture' => round($row['avg_soilMoisture'], 2),
        'avg_gas' => round($row['avg_gas'], 2),
    ];
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>
