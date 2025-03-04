<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
?>

<nav id="nav-bar" class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">broTech</a>
        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link me-2" 
                       href="index.php" 
                       style="font-weight: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'index.php' ? 'bold' : 'normal'; ?>; 
                              color: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'index.php' ? '#007bff' : 'inherit'; ?>; 
                              border-bottom: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'index.php' ? '2px solid #007bff' : 'none'; ?>;">
                        Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" 
                       href="data.php" 
                       style="font-weight: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'data.php' ? 'bold' : 'normal'; ?>; 
                              color: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'data.php' ? '#007bff' : 'inherit'; ?>; 
                              border-bottom: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'data.php' ? '2px solid #007bff' : 'none'; ?>;">
                        Dữ liệu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" 
                       href="chat.php" 
                       style="font-weight: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'chat.php' ? 'bold' : 'normal'; ?>; 
                              color: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'chat.php' ? '#007bff' : 'inherit'; ?>; 
                              border-bottom: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'chat.php' ? '2px solid #007bff' : 'none'; ?>;">
                        Báo cáo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2"
                        href="attendance.php"
                        style="font-weight: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'attendance.php' ? 'bold' : 'normal'; ?>; 
                              color: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'attendance.php' ? '#007bff' : 'inherit'; ?>; 
                              border-bottom: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'attendance.php' ? '2px solid #007bff' : 'none'; ?>;">
                        Điểm danh
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2"
                        href="predict.php"
                        style="font-weight: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'predict.php' ? 'bold' : 'normal'; ?>; 
                              color: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'predict.php' ? '#007bff' : 'inherit'; ?>; 
                              border-bottom: <?php echo basename($_SERVER['SCRIPT_NAME']) == 'predict.php' ? '2px solid #007bff' : 'none'; ?>;">
                        Dự đoán
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] === true): ?>
                    <span class="me-3 text-success fw-bold">
                        Chào Admin, <?php echo htmlspecialchars($_SESSION['adminName']); ?>!
                        <i class="bi bi-emoji-smile"></i>
                    </span>
                    <a href="admin/dashboard.php" class="btn btn-warning btn-sm me-2">Quay Lại Admin</a>
                    <a href="logout.php" class="btn btn-outline-danger shadow-none">Đăng xuất</a>
                <?php elseif (isset($_SESSION['user_email'])): ?>
                    <span class="me-3 text-success fw-bold">
                        Chào, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!
                        <i class="bi bi-emoji-smile"></i>
                    </span>
                    <a href="logout.php" class="btn btn-outline-danger shadow-none">Đăng xuất</a>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-dark shadow-none me-lg-3 me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Đăng nhập
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>  
</nav>

<!-- Đăng nhập Modal -->
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="login-form">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-person-circle fs-3 me-2"></i>Đăng nhập người dùng
                    </h5>
                    <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" id="login-email" class="form-control shadow-none" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu:</label>
                        <input type="password" id="login-password" class="form-control shadow-none" required>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <button type="button" onclick="loginUser()" class="btn btn-dark shadow-none">Đăng nhập</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
