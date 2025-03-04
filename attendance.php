<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

// Truy vấn danh sách nhân viên và thời gian đăng nhập hôm nay
$sql = "SELECT u.id, u.email, u.name, 
               (SELECT login_time FROM login_logs WHERE user_id = u.id AND login_date = CURDATE()) AS last_login 
        FROM users u";
$result = select($sql, [], "");

// Số lượng bản ghi trên mỗi trang
$records_per_page = 3; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Truy vấn toàn bộ lịch sử đăng nhập của tất cả nhân viên, sắp xếp giảm dần theo thời gian
$sql = "
    SELECT u.id AS user_id, u.email, u.name, ll.login_time
    FROM users u
    INNER JOIN login_logs ll ON u.id = ll.user_id
    ORDER BY ll.login_time DESC
    LIMIT ?, ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $start_from, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Đếm tổng số bản ghi trong bảng login_logs để tính tổng số trang
$count_sql = "SELECT COUNT(*) AS total FROM login_logs";
$count_result = $con->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điểm danh</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <?php require('inc/links.php');  ?>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: auto;
            margin-top: 50px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .table {
            margin-top: 20px;
        }
        .pagination {
            justify-content: center;
        }
    </style>
</head>

<body>

    <?php require('inc/header.php'); ?>

    
    <div class="container mt-5">
        <h3 class="text-center">Danh sách lịch sử đăng nhập</h3>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Họ và tên</th>
                    <th>Thời gian vào</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$row['user_id']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['login_time']}</td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>Không có dữ liệu đăng nhập</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <nav>
    <ul class="pagination">
        <!-- Nút Trước -->
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="attendance.php?page=<?php echo $page - 1; ?>">&laquo; Trước</a>
            </li>
        <?php endif; ?>

        <!-- Hiển thị các trang -->
        <?php
        $visiblePages = 5; // Số trang hiển thị xung quanh trang hiện tại
        $startPage = max(1, $page - floor($visiblePages / 2));
        $endPage = min($total_pages, $page + floor($visiblePages / 2));

        if ($startPage > 1) {
            echo '<li class="page-item"><a class="page-link" href="attendance.php?page=1">1</a></li>';
            if ($startPage > 2) {
                echo '<li class="page-item"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                    <a class="page-link" href="attendance.php?page=' . $i . '">' . $i . '</a>
                  </li>';
        }

        if ($endPage < $total_pages) {
            if ($endPage < $total_pages - 1) {
                echo '<li class="page-item"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="attendance.php?page=' . $total_pages . '">' . $total_pages . '</a></li>';
        }
        ?>

        <!-- Nút Tiếp -->
        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="attendance.php?page=<?php echo $page + 1; ?>">Tiếp &raquo;</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

    </div>
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
                        window.location.href = 'attendance.php'; // Chuyển hướng đến trang chính sau khi đăng nhập thành công
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                });
        }
    </script>
    <script>
        // Kết nối WebSocket
        let socket = new WebSocket('wss://game-introduce.onrender.com');
    
        socket.onopen = () => {
            console.log('WebSocket connection established.');
        };
    
        socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log('Received data:', data);
    
            // Nếu dữ liệu có type là check_in
            if (data.type === 'check_in') {
                const idLogin = data.data.id; // Lấy id từ dữ liệu check_in
    
                // Gửi id_login tới server PHP để lưu vào bảng login_logs
                fetch('save_check_in.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data.data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        console.log('Check-in saved successfully:', result);
                        alert('Check-in thành công cho ID Login: ' + idLogin);
                    } else {
                        console.error('Error saving check-in:', result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                console.warn('Unknown data type:', data.type);
            }
        };
    
        socket.onerror = (error) => {
            console.error('WebSocket error:', error);
        };
    
        socket.onclose = () => {
            console.log('WebSocket connection closed.');
        };
    </script>

    
</body>

</html>