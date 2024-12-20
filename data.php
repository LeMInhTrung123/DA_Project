<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>broTech</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-gauge@0.2.4/dist/chartjs-chart-gauge.min.js"></script>
    <?php require('inc/links.php');  ?>
    <style>
    </style>
</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>


    <h2 class="mt-5 pt-5 mb-4 text-center fw-bold h-font">ESP32 WITH MYSQL DATABASE</h2>
    <div class="h-line bg-dark"></div>

    <br>
    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Nhiệt độ</th>
                <th>Độ ẩm không khí</th>
                <th>Ánh sáng</th>
                <th>Độ ẩm đất</th>
                <th>Khí gas</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $hname = 'localhost';
            $uname = 'root';
            $pass = '';
            $db = 'demo';
            $connection = new mysqli($hname, $uname, $pass, $db);

            if ($connection->connect_error) {
                die("Kết nối thất bại:" . $connection->connect_error);
            }
            $sql = "SELECT * FROM data_agricultural";
            $result = $connection->query($sql);

            if (!$result) {
                die("Không hợp lệ: " . $connection->error);
            }

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                <td>" . $row["id"] . "</td>
                <td>" . $row["temperature"] . "</td>
                <td>" . $row["humidity"] . "</td>
                <td>" . $row["light"] . "</td>
                <td>" . $row["soilMoisture"] . "</td>
                <td>" . $row["gas"] . "</td>
            </tr>";
            }

            ?>
        </tbody>
    </table>
    <?php
    // Kết nối CSDL
    $hname = 'localhost';
    $uname = 'root';
    $pass = '';
    $db = 'demo';
    $connection = new mysqli($hname, $uname, $pass, $db);

    if ($connection->connect_error) {
        die("Kết nối thất bại:" . $connection->connect_error);
    }

    // Truy vấn giá trị trung bình
    $sql_avg = "SELECT 
                    AVG(temperature) AS avg_temp,
                    AVG(humidity) AS avg_humidity,
                    AVG(soilMoisture) AS avg_soil,
                    AVG(gas) AS avg_gas
                FROM data_agricultural";

    $result_avg = $connection->query($sql_avg);
    $averages = $result_avg->fetch_assoc();

    // Lưu giá trị trung bình vào biến
    $avg_temp = round($averages['avg_temp'], 2);
    $avg_humidity = round($averages['avg_humidity'], 2);
    $avg_soil = round($averages['avg_soil'], 2);
    $avg_gas = round($averages['avg_gas'], 2);
    ?>

    <div class="container mt-5">
        <h2 class="text-center fw-bold">ESP32 WITH MYSQL DATABASE</h2>
        <div class="h-line bg-dark my-3"></div>

        <!-- Biểu đồ đường gấp khúc -->
        <h4 class="text-center mb-4">Biểu đồ Trung Bình</h4>
        <div class="row justify-content-center mb-5">
            <div class="col-md-6 col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <canvas id="lineChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dữ liệu trung bình từ PHP
        const avgData = {
            temperature: <?php echo $avg_temp; ?>,
            humidity: <?php echo $avg_humidity; ?>,
            soilMoisture: <?php echo $avg_soil; ?>,
            gas: <?php echo $avg_gas; ?>
        };

        // Biểu đồ đường gấp khúc
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Nhiệt độ', 'Độ ẩm', 'Độ ẩm đất', 'Khí gas'],
                datasets: [{
                    label: 'Giá trị Trung Bình',
                    data: [avgData.temperature, avgData.humidity, avgData.soilMoisture, avgData.gas],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)', // Màu điểm trên đường
                    pointRadius: 5, // Kích thước điểm
                    fill: true, // Tô nền dưới đường
                    tension: 0.4 // Độ cong của đường
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

    <?php require('inc/footer.php'); ?>




</body>

</html>