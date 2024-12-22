<?php

if (!function_exists('adminLogin')) {
    function adminLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] === true)) {
            echo "<script>window.location.href='index.php';</script>";
            exit;
        }
    }
}


if (!function_exists('redirect')) {
    function redirect($url) {
        echo "<script>window.location.href='$url';</script>";
        exit;
    }
}


if (!function_exists('alert')) {
    function alert($type, $msg) {
        $bs_class = ($type == 'success') ? 'alert-success' : 'alert-danger';
        echo <<<alert
            <div class="alert $bs_class alert-dismissible fade show" role="alert">
                <strong class="me-3">$msg</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        alert;
    }
}



?>