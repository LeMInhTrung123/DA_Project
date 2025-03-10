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
    </style>
</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <!-- banner -->
    <div class="container-fluid px-lg-4 mt-4">
        <div class="swiper swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="images/banner/1.jpg" class="w-100 d-block">
                </div>
                <div class="swiper-slide">
                    <img src="images/banner/2.jpg" class="w-100 d-block">
                </div>
                <div class="swiper-slide">
                    <img src="images/banner/3.jpg" class="w-100 d-block">
                </div>
                <div class="swiper-slide">
                    <img src="images/banner/4.png" class="w-100 d-block">
                </div>
                <div class="swiper-slide">
                    <img src="images/banner/5.jpg" class="w-100 d-block">
                </div>
                <div class="swiper-slide">
                    <img src="images/banner/6.jpg" class="w-100 d-block">
                </div>
            </div>
        </div>
    </div>


    <h2 class="mt-5 pt-5 mb-4 text-center fw-bold h-font">ESP32 WITH MYSQL DATABASE</h2>
    <div class="h-line bg-dark"></div>


    <div class="container">
        <div class="row justify-content-center">
            <!-- Ô thứ nhất -->
            <div class="col-lg-3 col-md-6 my-3">
                <div class="card border-0 shadow" style="max-width: 350px; margin:auto;">
                    <img src="images/rooms/1.jpg" class="card-img-top" alt="...">
                    <div class="card-body" style="text-align: center;">
                        <h5 class="card-title reading" style="color: #f78130;">
                            <i class="fas fa-thermometer-half"></i> NHIỆT ĐỘ
                        </h5>
                        <p class="temperatureColor card-text">
                            <span class="reading"><span id="ESP32_01_Temp"></span></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ô thứ hai -->
            <div class="col-lg-3 col-md-6 my-3">
                <div class="card border-0 shadow" style="max-width: 350px; margin:auto;">
                    <img src="images/rooms/2.jpg" class="card-img-top" alt="...">
                    <div class="card-body" style="text-align: center;">
                        <h5 class="card-title reading" style="color: #2666b8;">
                            <i class="fas fa-droplet"></i> ĐỘ ẨM KHÔNG KHÍ
                        </h5>
                        <p class="humidityColor card-text">
                            <span class="reading"><span id="ESP32_01_Humd"></span></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ô thứ ba -->
            <div class="col-lg-3 col-md-6 my-3">
                <div class="card border-0 shadow" style="max-width: 350px; margin:auto;">
                    <img src="images/rooms/3.jpg" class="card-img-top" alt="...">
                    <div class="card-body" style="text-align: center;">
                        <h5 class="card-title reading" style="color: #198754;">
                            <i class="fas fa-seedling"></i> ĐỘ ẨM ĐẤT
                        </h5>
                        <p class="card-text" style="color: #198754;">
                            <span class="reading"><span id="ESP32_01_SoilMoisture"></span></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ô thứ tư -->
            <div class="col-lg-3 col-md-6 my-3">
                <div class="card border-0 shadow" style="max-width: 350px; margin:auto;">
                    <img src="images/rooms/4.jpg" class="card-img-top" alt="...">
                    <div class="card-body" style="text-align: center;">
                        <h5 class="card-title reading" style="color: #9966FF;">
                            <i class="fas fa-gas-pump"></i> KHÍ GAS
                        </h5>
                        <p class="card-text" style="color: #9966FF;">
                            <span class="reading"><span id="ESP32_01_Gas"></span></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-line bg-dark"></div>
    <!-- Box giới thiệu -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-center fw-bold h-font">Giới thiệu về Nông trại broTech</h3>
                        <p class="text-center mt-4">
                            broTech là một hệ thống nông trại thông minh áp dụng công nghệ Internet of Things (IoT) để giám sát và quản lý các yếu tố môi trường trong nông nghiệp.
                            Với sự tích hợp của các cảm biến hiện đại như nhiệt độ, độ ẩm không khí, độ ẩm đất, và khí gas, broTech đảm bảo mang lại sự tiện lợi, hiệu quả và
                            an toàn trong sản xuất nông nghiệp.
                        </p>
                        <p class="text-center">
                            Chúng tôi hướng tới việc tối ưu hóa quy trình trồng trọt, giúp người nông dân tiết kiệm chi phí, tăng năng suất và bảo vệ môi trường. broTech không chỉ
                            là một giải pháp công nghệ mà còn là một bước tiến quan trọng trong việc xây dựng nông nghiệp bền vững.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php require('inc/footer.php'); ?>

    <!-- Modal Scripts -->

    <script>
        var socket = new WebSocket('ws://192.168.1.72:81');


        socket.onmessage = function(event) {
            console.log(event.data);
            const data = event.data.split(":");

            const msg = data[0] || "";
            const sensor = data[1] || "";

            if (sensor == "led") {
                var button = document.getElementById("ledButton");
                button.innerHTML = msg == "1" ? "ON" : "OFF";
            } else if (sensor == "dht") {
                var parts = msg.split(",");
                var temperature = parts[0];
                var humidity = parts[1];

                document.getElementById("ESP32_01_Temp").innerHTML = temperature + " °C";
                document.getElementById("ESP32_01_Humd").innerHTML = humidity + " %";
            }
        };

        function toggle() {
            var button = document.getElementById("ledButton");
            var status = button.innerHTML === "OFF" ? "1" : "O";
            socket.send(status + ":led:esp:localhost");
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".swiper-container", {
            spaceBetween: 30,
            effect: "fade",
            loop: true,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            }
        });
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
                        window.location.href = 'index.php'; // Chuyển hướng đến trang chính sau khi đăng nhập thành công
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