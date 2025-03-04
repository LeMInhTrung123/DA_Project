<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <?php require('inc/links.php');  ?>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
            margin-top: 50px;
        }

        .card-header {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
        }

        .form-label {
            font-weight: bold;
            color: #495057;
        }

        .form-control {
            text-align: center;
            border-radius: 5px;
            padding: 10px;
        }

        button {
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }

        button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            Dự đoán nhu cầu tưới nước và dinh dưỡng
        </div>
        <div class="card-body">
            <form id="prediction-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="current-time" class="form-label">Thời gian hiện tại</label>
                        <input type="text" id="current-time" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="prediction-time" class="form-label">Thời gian dự đoán</label>
                        <select id="prediction-time" class="form-control">
                            <option value="1">1 tiếng</option>
                            <option value="2">2 tiếng</option>
                            <option value="3">3 tiếng</option>
                            <option value="4">4 tiếng</option>
                            <option value="5">5 tiếng</option>
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label for="temperature" class="form-label">Nhiệt độ (°C)</label>
                        <input type="text" id="temperature" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="humidity" class="form-label">Độ ẩm (%)</label>
                        <input type="text" id="humidity" class="form-control">
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label for="light" class="form-label">Ánh sáng</label>
                        <input type="text" id="light" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="soilMoisture" class="form-label">Độ ẩm đất</label>
                        <input type="text" id="soilMoisture" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="gas" class="form-label">Khí gas</label>
                        <input type="text" id="gas" class="form-control">
                    </div>
                </div>
                
                <div class="row g-3 mt-3 d-flex justify-content-center">
                    <div class="col-md-4 text-center">
                        <label for="predict_tag" class="form-label fw-bold">Kết quả</label>
                        <input type="text" id="predict_tag" class="form-control text-center" readonly>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-primary" onclick="predict()">Dự đoán</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('prediction-time').value = "1";  // Đặt giá trị mặc định là 1 tiếng
        });
        document.addEventListener("DOMContentLoaded", updateCurrentTime);
        
        // Gọi fetchLatestData khi trang tải xong
        document.addEventListener("DOMContentLoaded", () => {
            updateCurrentTime();
            fetchLatestData();
        });
        
        // Tự động cập nhật dữ liệu mới nhất từ server
        function fetchLatestData() {
            fetch('get_latest_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        document.getElementById('temperature').value = data.data.temperature;
                        document.getElementById('humidity').value = data.data.humidity;
                        document.getElementById('light').value = data.data.light;
                        document.getElementById('soilMoisture').value = data.data.soilMoisture;
                        document.getElementById('gas').value = data.data.gas;
                    } else {
                        alert("Không thể lấy dữ liệu mới nhất.");
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi lấy dữ liệu:", error);
                    alert("Có lỗi xảy ra khi tải dữ liệu.");
                });
        }
        
        function updateCurrentTime() {
            const now = new Date();
            const formattedTime = now.toLocaleString('vi-VN', {
                hour12: false,  // Định dạng 24 giờ
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            document.getElementById('current-time').value = formattedTime;
        }


        function predict() {
            // Lấy dữ liệu từ các ô input
            let temperature = parseFloat(document.getElementById('temperature').value);
            let humidity = parseFloat(document.getElementById('humidity').value);
        
            // Chuyển đổi ánh sáng từ "dark" -> 0 và "bright" -> 1
            let lightInput = document.getElementById('light').value.toLowerCase();
            let light = lightInput === "bright" ? 1 : 0;
        
            let soilMoisture = parseFloat(document.getElementById('soilMoisture').value);
            let gas = parseFloat(document.getElementById('gas').value);
        
            // Lấy thông tin thời gian hiện tại và thời gian dự đoán
            let now = new Date();
            let predictionHours = parseInt(document.getElementById('prediction-time').value);
            let predictionTime = new Date(now.getTime() + predictionHours * 60 * 60 * 1000); // Thêm giờ
        
            // Lấy thông tin giờ, ngày trong tuần, tháng từ thời gian dự đoán
            let hour = predictionTime.getHours();
            let dayOfWeek = predictionTime.getDay();  // 0: Chủ nhật, 1: Thứ Hai, ..., 6: Thứ Bảy
            let month = predictionTime.getMonth() + 1; // Tháng trong JS bắt đầu từ 0
        
            // Chuẩn bị dữ liệu dạng query string
            const queryParams = `features=[${temperature},${humidity},${light},${soilMoisture},${gas},${hour},${dayOfWeek},${month}]`;
            
            console.log(hour)
        
            // Gửi dữ liệu đến API bằng phương thức GET
            fetch(`https://game-introduce.onrender.com/predict?${queryParams}`, {
                method: 'GET',
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    let prediction = data.prediction;
        
                    // Chuyển đổi kết quả dự đoán thành dạng mô tả ý nghĩa
                    let predictionText = "";
                    switch (prediction) {
                        case 0:
                            predictionText = "Kéo lưới";
                            break;
                        case 1:
                            predictionText = "Tưới nước";
                            break;
                        case 2:
                            predictionText = "Bật quạt";
                            break;
                        default:
                            predictionText = "Không xác định";
                            break;
                    }
        
                    // Hiển thị kết quả dự đoán lên giao diện
                    document.getElementById('predict_tag').value = predictionText;
                } else {
                    alert("Dự đoán thất bại. Vui lòng thử lại.");
                }
            })
            .catch(error => {
                console.error("Lỗi khi dự đoán:", error);
                alert("Có lỗi xảy ra khi gửi yêu cầu đến API.");
            });
        }


    </script>

    <?php require('inc/footer.php'); ?>

</body>

</html>