<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>broTech</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <?php require('inc/links.php');  ?>
    <style>
    </style>
</head>

<body>
    
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


    <!-- script -->
    <div class="container">
        <div class="row">
            <!-- Ô thứ nhất -->
            <div class="col-lg-4 col-md-6 my-3">
                <div class="card border-0 shadow" style="max-width: 350px; margin:auto;">
                    <img src="images/rooms/1.jpg" class="card-img-top" alt="...">
                    <div class="card-body" style="text-align: center;">
                        <h5 class="temperatureColor" class="card-title" class="reading"><i class="fas fa-thermometer-half"></i> NHIỆT ĐỘ</h5>
                        <p class="temperatureColor" class="card-text"><span class="reading"><span id="ESP32_01_Temp"></span></span></p>
                    </div>
                </div>
            </div>
            <!-- Ô thứ hai -->
            <div class="col-lg-4 col-md-6 my-3">
                <div class="card border-0 shadow" style="max-width: 350px; margin:auto;">
                    <img src="images/rooms/2.jpg" class="card-img-top" alt="...">
                    <div class="card-body" style="text-align: center;">
                        <h5 class="humidityColor" class="card-title"><i class="fas fa-thermometer-half"></i> ĐỘ ẨM</h5>
                        <p class="humidityColor" class="card-text"><span class="reading"><span id="ESP32_01_Humd"></span></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php require('inc/footer.php'); ?>
       

    <script>
        var socket = new WebSocket('ws://192.168.110.216:81');


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

</body>

</html>