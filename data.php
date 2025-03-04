<?php
$hname = 'localhost';
$uname = 'root';
$pass = '';
$db = 'demo';

$connection = new mysqli($hname, $uname, $pass, $db);

if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Lấy số trang hiện tại từ URL, mặc định là 1 nếu không có
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 10; // Số bản ghi trên mỗi trang
$offset = ($page - 1) * $recordsPerPage; // Vị trí bắt đầu lấy dữ liệu

// Đếm tổng số bản ghi trong bảng
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM data_agricultural_smart";
$totalRecordsResult = $connection->query($totalRecordsQuery);

if (!$totalRecordsResult) {
    die("Không thể đếm tổng số bản ghi: " . $connection->error);
}

$totalRecords = $totalRecordsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage); // Tính tổng số trang

// Lấy dữ liệu cho trang hiện tại
$sql = "SELECT * FROM data_agricultural_smart ORDER BY measurement_time DESC LIMIT $recordsPerPage OFFSET $offset";
$result = $connection->query($sql);

if (!$result) {
    die("Không thể lấy dữ liệu: " . $connection->error);
}

// Lấy số trang hiện tại từ URL, mặc định là 1 nếu không có
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 10; // Số bản ghi trên mỗi trang
$offset = ($page - 1) * $recordsPerPage; // Vị trí bắt đầu lấy dữ liệu

// Đếm tổng số bản ghi trong bảng
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM data_agricultural_smart";
$totalRecordsResult = $connection->query($totalRecordsQuery);

if (!$totalRecordsResult) {
    die("Không thể đếm tổng số bản ghi: " . $connection->error);
}

$totalRecords = $totalRecordsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage); // Tính tổng số trang

// Lấy dữ liệu cho trang hiện tại
$sql = "SELECT * FROM data_agricultural_smart ORDER BY measurement_time DESC LIMIT $recordsPerPage OFFSET $offset";
$result = $connection->query($sql);

if (!$result) {
    die("Không thể lấy dữ liệu: " . $connection->error);
}

// Lấy dữ liệu cho 5 lần đo mới nhất
$sql_latest = "SELECT * FROM data_agricultural_smart ORDER BY measurement_time DESC LIMIT 5";
$result_latest = $connection->query($sql_latest);

if (!$result_latest) {
    die("Không thể lấy dữ liệu mới nhất: " . $connection->error);
}

// Chuẩn bị dữ liệu cho biểu đồ
$chartLabels = [];
$temperatureData = [];
$humidityData = [];
$soilMoistureData = [];
$gasData = [];

while ($row = $result_latest->fetch_assoc()) {
    $chartLabels[] = date('H:i:s', strtotime($row['measurement_time'])); // Định dạng thời gian
    $temperatureData[] = $row['temperature'];
    $humidityData[] = $row['humidity'];
    $soilMoistureData[] = $row['soilMoisture'];
    $gasData[] = $row['gas'];
}

// Đảo ngược thứ tự dữ liệu để hiển thị từ cũ đến mới trong biểu đồ
$chartLabels = array_reverse($chartLabels);
$temperatureData = array_reverse($temperatureData);
$humidityData = array_reverse($humidityData);
$soilMoistureData = array_reverse($soilMoistureData);
$gasData = array_reverse($gasData);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dữ liệu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-gauge@3.0.0"></script>
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

        .filter-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .chart-container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        .h-line {
            height: 3px;
            width: 50px;
            margin: auto;
            background-color: #000;
        }

        .gauge-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 30px;
        }

        .gauge {
            width: 150px;
            font-family: "Roboto", sans-serif;
            font-size: 18px;
            color: #004033;
        }

        .gauge__body {
            width: 100%;
            height: 0;
            padding-bottom: 50%;
            background: #b4c0be;
            position: relative;
            border-top-left-radius: 100% 200%;
            border-top-right-radius: 100% 200%;
            overflow: hidden;
        }

        .gauge__cover {
            width: 75%;
            height: 150%;
            background: #ffffff;
            border-radius: 50%;
            position: absolute;
            top: 25%;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding-bottom: 25%;
            box-sizing: border-box;
            font-weight: bold;
            color: #004033;
        }

        .gauge__label {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }

        .filter-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .chart-container {
            width: 50%;
            margin: 0 auto 40px;
        }

        canvas {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <h2 class="mt-5 pt-5 mb-4 text-center fw-bold h-font">NÔNG TRẠI broTech </h2>
    <div class="h-line bg-dark"></div>

    <br>
    <table class="table" style="margin: 0 auto; width: 90%; text-align: center; border-collapse: collapse; background-color: #f9f9f9; box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);">
        <thead style="background-color: #004033; color: #ffffff;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd;">Nhiệt độ</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Độ ẩm không khí</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Ánh sáng</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Độ ẩm đất</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Khí gas</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Thời gian đo</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['temperature']); ?></td>
                    <td><?php echo htmlspecialchars($row['humidity']); ?></td>
                    <td><?php echo htmlspecialchars($row['light']); ?></td>
                    <td><?php echo htmlspecialchars($row['soilMoisture']); ?></td>
                    <td><?php echo htmlspecialchars($row['gas']); ?></td>
                    <td><?php echo htmlspecialchars($row['measurement_time']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
        <div>
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>"
                    style="margin: 0 5px; padding: 10px 15px; text-decoration: none; color: #004033; border: 1px solid #ddd; border-radius: 5px; transition: all 0.3s ease;">
                    &laquo; Trước
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>"
                    style="margin: 0 5px; padding: 10px 15px; text-decoration: none; color: #004033; border: 1px solid #ddd; border-radius: 5px; transition: all 0.3s ease;
                      <?php echo $i === $page ? 'font-weight: bold; background-color: #004033; color: white; border-color: #004033;' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>"
                    style="margin: 0 5px; padding: 10px 15px; text-decoration: none; color: #004033; border: 1px solid #ddd; border-radius: 5px; transition: all 0.3s ease;">
                    Tiếp &raquo;
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // Truy vấn giá trị trung bình
    $sql_avg_temp = "SELECT AVG(temperature) AS avg_temperature FROM data_agricultural_smart";
    $sql_avg_humidity = "SELECT AVG(humidity) AS avg_humidity FROM data_agricultural_smart";
    $sql_avg_soilMoisture = "SELECT AVG(soilMoisture) AS avg_soilMoisture FROM data_agricultural_smart";
    $sql_avg_gas = "SELECT AVG(gas) AS avg_gas FROM data_agricultural_smart";

    $result_avg_temp = $connection->query($sql_avg_temp);
    $result_avg_humidity = $connection->query($sql_avg_humidity);
    $result_avg_soilMoisture = $connection->query($sql_avg_soilMoisture);
    $result_avg_gas = $connection->query($sql_avg_gas);

    $row_avg_temp = $result_avg_temp->fetch_assoc();
    $row_avg_humidity = $result_avg_humidity->fetch_assoc();
    $row_avg_soilMoisture = $result_avg_soilMoisture->fetch_assoc();
    $row_avg_gas = $result_avg_gas->fetch_assoc();

    $avg_temperature = round($row_avg_temp['avg_temperature'], 2); // Giá trị nhiệt độ trung bình
    $avg_humidity = round($row_avg_humidity['avg_humidity'], 2);   // Giá trị độ ẩm trung bình
    $avg_soilMoisture = round($row_avg_soilMoisture['avg_soilMoisture'], 2); // Giá trị độ ẩm đấtđất trung bình
    $avg_gas = round($row_avg_gas['avg_gas'], 2); // Giá trị khí gasgas trung bình

    ?>



    <div class="container mt-5">
        <h2 class="text-center fw-bold h-font">BIỂU ĐỒ </h2>
        <div class="h-line bg-dark my-4"></div>

        <!-- Biểu đồ Nhiệt độ -->
        <div class="row text-center mb-4">
            <div class="col-12">
                <div class="card shadow-lg p-3 rounded">
                    <div class="card-body">
                        <canvas id="tempChart" style="height: 400px; width: 100%;"></canvas>
                        <h6 class="mt-3 fw-bold" style="color: rgba(255, 99, 132, 1);">Nhiệt Độ</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Độ ẩm -->
        <div class="row text-center mb-4">
            <div class="col-12">
                <div class="card shadow-lg p-3 rounded">
                    <div class="card-body">
                        <canvas id="humidityChart" style="height: 400px; width: 100%;"></canvas>
                        <h6 class="mt-3 fw-bold" style="color: rgba(54, 162, 235, 1);">Độ Ẩm</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Độ ẩm đất -->
        <div class="row text-center mb-4">
            <div class="col-12">
                <div class="card shadow-lg p-3 rounded">
                    <div class="card-body">
                        <canvas id="soilChart" style="height: 400px; width: 100%;"></canvas>
                        <h6 class="mt-3 fw-bold" style="color: rgba(75, 192, 192, 1);">Độ Ẩm Đất</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Khí gas -->
        <div class="row text-center mb-4">
            <div class="col-12">
                <div class="card shadow-lg p-3 rounded">
                    <div class="card-body">
                        <canvas id="gasChart" style="height: 400px; width: 100%;"></canvas>
                        <h6 class="mt-3 fw-bold" style="color: rgba(153, 102, 255, 1);">Khí Gas</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-container">
        <label for="filter-select">Lọc theo:</label>
        <select id="filter-select">
            <option value="hour">Giờ (Hôm nay)</option>
            <option value="day">Ngày (30 ngày gần nhất)</option>
            <option value="month">Tháng (12 tháng gần nhất)</option>
        </select>
        <button id="reset-button">Reset</button>
    </div>

    <div class="chart-container">
        <canvas id="tempChart1"></canvas>
    </div>

    <div class="chart-container">
        <canvas id="humidityChart1"></canvas>
    </div>

    <div class="chart-container">
        <canvas id="soilChart1"></canvas>
    </div>

    <div class="chart-container">
        <canvas id="gasChart1"></canvas>
    </div>

    <!-- BIEU DO LOC DU LIEU -->
    <script>
        const tempCtx = document.getElementById('tempChart1').getContext('2d');
        const humidityCtx = document.getElementById('humidityChart1').getContext('2d');
        const soilCtx = document.getElementById('soilChart1').getContext('2d');
        const gasCtx = document.getElementById('gasChart1').getContext('2d');

        let tempChart = new Chart(tempCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Nhiệt Độ',
                    data: [],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            text: 'Nhiệt Độ',
                            display: true
                        }
                    },
                    x: {
                        title: {
                            text: 'Thời gian đo',
                            display: true
                        }
                    }
                }
            }
        });

        let humidityChart = new Chart(humidityCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Độ Ẩm',
                    data: [],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            text: 'Độ Ẩm',
                            display: true
                        }
                    },
                    x: {
                        title: {
                            text: 'Thời gian đo',
                            display: true
                        }
                    }
                }
            }
        });

        let soilChart = new Chart(soilCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Độ Ẩm Đất',
                    data: [],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            text: 'Độ Ẩm Đất',
                            display: true
                        }
                    },
                    x: {
                        title: {
                            text: 'Thời gian đo',
                            display: true
                        }
                    }
                }
            }
        });

        let gasChart = new Chart(gasCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Khí Gas',
                    data: [],
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            text: 'Khí Gas',
                            display: true
                        }
                    },
                    x: {
                        title: {
                            text: 'Thời gian đo',
                            display: true
                        }
                    }
                }
            }
        });

        function updateChart(chart, labels, data) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.update();
        }

        document.getElementById('filter-select').addEventListener('change', function() {
            const filter = this.value;
            fetch(`filter_data.php?filter=${filter}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        const labels = result.data.map(item => item.label);
                        updateChart(tempChart, labels, result.data.map(item => item.avg_temperature));
                        updateChart(humidityChart, labels, result.data.map(item => item.avg_humidity));
                        updateChart(soilChart, labels, result.data.map(item => item.avg_soilMoisture));
                        updateChart(gasChart, labels, result.data.map(item => item.avg_gas));
                    } else {
                        console.error('Lỗi khi lấy dữ liệu:', result.message);
                    }
                })
                .catch(error => console.error('Lỗi kết nối:', error));
        });

        document.getElementById('reset-button').addEventListener('click', function() {
            document.getElementById('filter-select').value = 'hour';
            fetch('filter_data.php?filter=hour')
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        const labels = result.data.map(item => item.label);
                        updateChart(tempChart, labels, result.data.map(item => item.avg_temperature));
                        updateChart(humidityChart, labels, result.data.map(item => item.avg_humidity));
                        updateChart(soilChart, labels, result.data.map(item => item.avg_soilMoisture));
                        updateChart(gasChart, labels, result.data.map(item => item.avg_gas));
                    } else {
                        console.error('Lỗi khi lấy dữ liệu:', result.message);
                    }
                })
                .catch(error => console.error('Lỗi kết nối:', error));
        });

        fetch('filter_data.php?filter=hour')
            .then(response => response.json())
            .then(result => {
                console.log('Full JSON Response:', result);

                if (result.status === 'success') {
                    const labels = result.data.map(item => item.label);
                    updateChart(tempChart, labels, result.data.map(item => item.avg_temperature));
                    updateChart(humidityChart, labels, result.data.map(item => item.avg_humidity));
                    updateChart(soilChart, labels, result.data.map(item => item.avg_soilMoisture));
                    updateChart(gasChart, labels, result.data.map(item => item.avg_gas));
                } else {
                    console.error('Lỗi khi lấy dữ liệu:', result.message);
                }
            })
            .catch(error => console.error('Lỗi kết nối:', error));
    </script>

    <div class="gauge-container">
        <!-- Gauge cho nhiệt độ -->
        <div class="gauge">
            <div class="gauge__body" style="width: 100%; height: 0; padding-bottom: 50%; background: #b4c0be; position: relative; border-top-left-radius: 100% 200%; border-top-right-radius: 100% 200%; overflow: hidden;">
                <div class="gauge__fill gauge__fill--temperature" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: #FF6384; transform-origin: center top; transform: rotate(0.25turn); transition: transform 0.2s ease-out;"></div>
                <div class="gauge__cover" style="width: 75%; height: 150%; background: #ffffff; border-radius: 50%; position: absolute; top: 25%; left: 50%; transform: translateX(-50%); display: flex; align-items: center; justify-content: center; padding-bottom: 25%; box-sizing: border-box; font-weight: bold; color: #004033;">0°C</div>
            </div>
            <div class="gauge__label" style="text-align: center; font-weight: bold; margin-top: 10px;">Giá trị trung bình Nhiệt độ</div>
        </div>

        <!-- Gauge cho độ ẩm -->
        <div class="gauge">
            <div class="gauge__body" style="width: 100%; height: 0; padding-bottom: 50%; background: #b4c0be; position: relative; border-top-left-radius: 100% 200%; border-top-right-radius: 100% 200%; overflow: hidden;">
                <div class="gauge__fill gauge__fill--humidity" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: #36a2eb; transform-origin: center top; transform: rotate(0.25turn); transition: transform 0.2s ease-out;"></div>
                <div class="gauge__cover" style="width: 75%; height: 150%; background: #ffffff; border-radius: 50%; position: absolute; top: 25%; left: 50%; transform: translateX(-50%); display: flex; align-items: center; justify-content: center; padding-bottom: 25%; box-sizing: border-box; font-weight: bold; color: #004033;">0%</div>
            </div>
            <div class="gauge__label" style="text-align: center; font-weight: bold; margin-top: 10px;">Giá trị trung bình Độ ẩm</div>
        </div>
        <!-- Gauge cho độ ẩm đất -->
        <div class="gauge">
            <div class="gauge__body" style="width: 100%; height: 0; padding-bottom: 50%; background: #b4c0be; position: relative; border-top-left-radius: 100% 200%; border-top-right-radius: 100% 200%; overflow: hidden;">
                <div class="gauge__fill gauge__fill--soil" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: rgba(75, 192, 192, 1); transform-origin: center top; transform: rotate(0.25turn); transition: transform 0.2s ease-out;"></div>
                <div class="gauge__cover" style="width: 75%; height: 150%; background: #ffffff; border-radius: 50%; position: absolute; top: 25%; left: 50%; transform: translateX(-50%); display: flex; align-items: center; justify-content: center; padding-bottom: 25%; box-sizing: border-box; font-weight: bold; color: #004033;">0%</div>
            </div>
            <div class="gauge__label" style="text-align: center; font-weight: bold; margin-top: 10px;">Giá trị trung bình Độ ẩm đất</div>
        </div>

        <!-- Gauge cho khí gas -->
        <div class="gauge">
            <div class="gauge__body" style="width: 100%; height: 0; padding-bottom: 50%; background: #b4c0be; position: relative; border-top-left-radius: 100% 200%; border-top-right-radius: 100% 200%; overflow: hidden;">
                <div class="gauge__fill gauge__fill--gas" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: rgba(153, 102, 255, 1); transform-origin: center top; transform: rotate(0.25turn); transition: transform 0.2s ease-out;"></div>
                <div class="gauge__cover" style="width: 75%; height: 150%; background: #ffffff; border-radius: 50%; position: absolute; top: 25%; left: 50%; transform: translateX(-50%); display: flex; align-items: center; justify-content: center; padding-bottom: 25%; box-sizing: border-box; font-weight: bold; color: #004033;">0ppm</div>
            </div>
            <div class="gauge__label" style="text-align: center; font-weight: bold; margin-top: 10px;">Giá trị trung bình Khí Gas</div>
        </div>
    </div>
    <div style="text-align: center; margin: 20px;">
        <button id="send-email-btn" class="btn btn-primary" style="padding: 10px 20px; font-size: 18px;">
            Gửi thông báo qua email
        </button>
    </div>
    <script>
        document.getElementById('send-email-btn').addEventListener('click', function() {
            fetch('send_email.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                })
                .catch(error => {
                    console.error('Lỗi kết nối:', error);
                    alert('Không thể gửi thông báo.');
                });
        });
    </script>



    <script>
        const socket = new WebSocket('wss://game-introduce.onrender.com'); // Kết nối WebSocket tới server

        socket.onopen = () => {
            console.log('WebSocket connection established.');
        };

        socket.onmessage = (event) => {
            const data = JSON.parse(event.data); // Dữ liệu nhận từ WebSocket
            console.log('Received data:', data);

            // Cập nhật bảng dữ liệu
            // updateTable(data);

            // Cập nhật biểu đồ
            // updateCharts(data);
            fetch('save_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.text()) // Dùng `.text()` để kiểm tra toàn bộ nội dung phản hồi
                .then(result => {
                    console.log('Server response:', result);
                    const parsedResult = JSON.parse(result); // Chỉ parse nếu chắc chắn là JSON
                    if (parsedResult.status === 'success') {
                        console.log('Dữ liệu đã được lưu vào database.');
                    } else {
                        console.error('Lỗi khi lưu dữ liệu:', parsedResult.message);
                    }
                })
                .catch(error => {
                    console.error('Lỗi kết nối tới server:', error);
                });

        };

        socket.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        socket.onclose = () => {
            console.log('WebSocket connection closed.');
        };

        // Hàm cập nhật bảng
        // function updateTable(newData) {
        //     const tableBody = document.querySelector('table tbody');
        //     const newRow = `
        //         <tr>
        //             <td>${newData.temperature}</td>
        //             <td>${newData.humidity}</td>
        //             <td>${newData.light}</td>
        //             <td>${newData.soilMoisture}</td>
        //             <td>${newData.gas}</td>
        //         </tr>
        //     `;
        //     tableBody.insertAdjacentHTML('beforeend', newRow);
        // }

        // Hàm cập nhật biểu đồ
        // function updateCharts(newData) {
        //     // Cập nhật dữ liệu cho biểu đồ nhiệt độ
        //     // tempChart.data.labels.push(newData.measurement_time);
        //     // tempChart.data.datasets[0].data.push(newData.temperature);

        //     // Cập nhật dữ liệu cho biểu đồ độ ẩm
        //     // humidityChart.data.labels.push(newData.measurement_time);
        //     // humidityChart.data.datasets[0].data.push(newData.humidity);

        //     // Cập nhật dữ liệu cho biểu đồ độ ẩm đất
        //     // soilChart.data.labels.push(newData.measurement_time);
        //     // soilChart.data.datasets[0].data.push(newData.soilMoisture);

        //     // Cập nhật dữ liệu cho biểu đồ khí gas
        //     // gasChart.data.labels.push(newData.measurement_time);
        //     // gasChart.data.datasets[0].data.push(newData.gas);

        //     // Render lại các biểu đồ
        //     // tempChart.update();
        //     // humidityChart.update();
        //     // soilChart.update();
        //     // gasChart.update();
        // }
    </script>

    <!-- tao bieu do -->
    <script>
        const chartLabels = <?php echo json_encode($chartLabels); ?>;
        const temperatureData = <?php echo json_encode($temperatureData); ?>;
        const humidityData = <?php echo json_encode($humidityData); ?>;
        const soilMoistureData = <?php echo json_encode($soilMoistureData); ?>;
        const gasData = <?php echo json_encode($gasData); ?>;

        function createLineChart(ctx, label, data, color) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: color,
                        backgroundColor: color,
                        fill: false,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Giá trị'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Thời gian đo'
                            }
                        }
                    }
                }
            });
        }

        createLineChart(document.getElementById('tempChart').getContext('2d'), 'Nhiệt Độ', temperatureData, 'rgba(255, 99, 132, 1)');
        createLineChart(document.getElementById('humidityChart').getContext('2d'), 'Độ Ẩm', humidityData, 'rgba(54, 162, 235, 1)');
        createLineChart(document.getElementById('soilChart').getContext('2d'), 'Độ Ẩm Đất', soilMoistureData, 'rgba(75, 192, 192, 1)');
        createLineChart(document.getElementById('gasChart').getContext('2d'), 'Khí Gas', gasData, 'rgba(153, 102, 255, 1)');

        const avgTemperature = <?php echo $avg_temperature; ?>; // Giá trị trung bình nhiệt độ
        const avgHumidity = <?php echo $avg_humidity; ?>; // Giá trị trung bình độ ẩm
        const avgSoilMoisture = <?php echo $avg_soilMoisture; ?>; // Giá trị trung bình độ ẩm đất
        const avgGas = <?php echo $avg_gas; ?>; // Giá trị trung bình khí gas
        const maxTemperature = 100; // Giới hạn tối đa của Gauge (nhiệt độ)
        const maxHumidity = 100; // Giới hạn tối đa của Gauge (độ ẩm)
        const maxSoilMoisture = 10000; // Giới hạn tối đa của độ ẩm đất
        const maxGas = 1000; // Giới hạn tối đa của khí gas (đơn vị ppm)

        // Tính giá trị tỷ lệ cho Gauge
        const normalizedTemperature = avgTemperature / maxTemperature;
        const normalizedHumidity = avgHumidity / maxHumidity;
        const normalizedSoilMoisture = avgSoilMoisture / maxSoilMoisture;
        const normalizedGas = avgGas / maxGas;

        // Gán giá trị cho Gauge
        function setGaugeValue(gauge, value, displayValue) {
            if (value < 0 || value > 1) {
                return;
            }

            gauge.querySelector(".gauge__fill").style.transform = `rotate(${value / 2}turn)`;
            gauge.querySelector(".gauge__cover").textContent = displayValue;
        }

        // Gán giá trị nhiệt độ
        const tempGauge = document.querySelector(".gauge__fill--temperature").closest(".gauge");
        setGaugeValue(tempGauge, normalizedTemperature, `${avgTemperature}°C`);

        // Gán giá trị độ ẩm
        const humidityGauge = document.querySelector(".gauge__fill--humidity").closest(".gauge");
        setGaugeValue(humidityGauge, normalizedHumidity, `${avgHumidity}%`);

        // Độ ẩm đất
        const soilGauge = document.querySelector(".gauge__fill--soil").closest(".gauge");
        setGaugeValue(soilGauge, normalizedSoilMoisture, `${avgSoilMoisture}%`);

        // Khí gas
        const gasGauge = document.querySelector(".gauge__fill--gas").closest(".gauge");
        setGaugeValue(gasGauge, normalizedGas, `${avgGas}ppm`);
    </script>
    

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
    <?php require('inc/footer.php'); ?>
</body>

</html>