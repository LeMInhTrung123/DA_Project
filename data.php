<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dữ liệu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-gauge@0.2.4/dist/chartjs-chart-gauge.min.js"></script>
    <?php require('inc/links.php');  ?>
    <style>
        .card {
            border: none;
            background-color: #ffffff;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.15);
        }

        .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h6 {
            font-size: 1rem;
            text-transform: uppercase;
        }

        .h-line {
            height: 3px;
            width: 50px;
            margin: auto;
            background-color: #000;
        }
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
        <h2 class="text-center fw-bold h-font">BIỂU ĐỒ TRUNG BÌNH</h2>
        <div class="h-line bg-dark my-4"></div>

        <!-- Biểu đồ trên cùng một hàng ngang -->
        <div class="row text-center">
            <!-- Biểu đồ Nhiệt độ -->
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card shadow-lg p-3 rounded">
                    <div class="card-body">
                        <canvas id="tempChart" style="height: 200px;"></canvas>
                        <h6 class="mt-3 fw-bold text-primary">Nhiệt Độ</h6>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ Độ ẩm -->
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card shadow-lg p-3 rounded">
                    <div class="card-body">
                        <canvas id="humidityChart" style="height: 200px;"></canvas>
                        <h6 class="mt-3 fw-bold text-info">Độ Ẩm</h6>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ Độ ẩm đất -->
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card shadow-lg p-3 rounded">
                    <div class="card-body">
                        <canvas id="soilChart" style="height: 200px;"></canvas>
                        <h6 class="mt-3 fw-bold text-success">Độ Ẩm Đất</h6>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ Khí gas -->
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card shadow-lg p-3 rounded">
                    <div class="card-body">
                        <canvas id="gasChart" style="height: 200px;"></canvas>
                        <h6 class="mt-3 fw-bold text-danger">Khí Gas</h6>
                    </div>
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

        // Hàm tạo biểu đồ
        function createChart(ctx, label, data, backgroundColor, borderColor) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [label],
                    datasets: [{
                        label: `Trung Bình (${label})`,
                        data: [data],
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true
                            },
                            title: {
                                display: true,
                                text: 'Giá trị',
                                font: {
                                    size: 14
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Biểu đồ Nhiệt độ
        createChart(
            document.getElementById('tempChart').getContext('2d'),
            'Nhiệt Độ',
            avgData.temperature,
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 99, 132, 1)'
        );

        // Biểu đồ Độ ẩm
        createChart(
            document.getElementById('humidityChart').getContext('2d'),
            'Độ Ẩm',
            avgData.humidity,
            'rgba(54, 162, 235, 0.2)',
            'rgba(54, 162, 235, 1)'
        );

        // Biểu đồ Độ ẩm đất
        createChart(
            document.getElementById('soilChart').getContext('2d'),
            'Độ Ẩm Đất',
            avgData.soilMoisture,
            'rgba(75, 192, 192, 0.2)',
            'rgba(75, 192, 192, 1)'
        );

        // Biểu đồ Khí gas
        createChart(
            document.getElementById('gasChart').getContext('2d'),
            'Khí Gas',
            avgData.gas,
            'rgba(153, 102, 255, 0.2)',
            'rgba(153, 102, 255, 1)'
        );
    </script>

    <?php require('inc/footer.php'); ?>
    <script>
        function loginUser() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            fetch('admin/ajax/login_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `login_user=1&email=${email}&password=${password}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        window.location.href = 'data.php'; // Chuyển hướng đến trang chính sau khi đăng nhập thành công
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                });
        }
    </script>



</body>

</html>