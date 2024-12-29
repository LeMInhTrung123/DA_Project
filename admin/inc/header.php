<div class="container-fluid bg-dark text-light p-3 d-flex align-items-center justify-content-between sticky-top">
    <h3 class="mb-0 h-font">broTech</h3>
    <div>
        <a href="../index.php" class="btn btn-light btn-sm me-2">Vào Trang Chủ</a>
        <a href="logout.php" class="btn btn-light btn-sm">Đăng Xuất</a>
    </div>
</div>

<div class="col-lg-2 bg-dark border-top border-3 border-secondary" style="height:100%; position: fixed" id="dashboard_menu">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid flex-lg-column align-items-stretch">
            <h4 class="mt-2 text-light">ADMIN PANEL</h4>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#adminDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="adminDropdown">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Bảng điều khiển</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'user_management.php' ? 'active' : '' ?>" href="user_management.php">Người dùng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>" href="settings.php">Cài đặt</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
</div>