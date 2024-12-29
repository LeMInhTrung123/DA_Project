<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

header('Content-Type: application/json');

// Kiểm tra hành động
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'get_farms') {
        // Lấy danh sách nông trại
        $query = "SELECT * FROM farms";
        $result = mysqli_query($con, $query);

        $farms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $farms[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $farms]);
        exit;

    } elseif ($action === 'add_farm') {
        // Thêm nông trại mới
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);

        if (!$name || !$address) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin']);
            exit;
        }

        $stmt = $con->prepare("INSERT INTO farms (name, address) VALUES (?, ?)");
        $stmt->bind_param('ss', $name, $address);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Nông trại được thêm thành công']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi thêm nông trại']);
        }
        exit;

    } elseif ($action === 'delete_farm') {
        // Xóa nông trại
        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
            exit;
        }

        $stmt = $con->prepare("DELETE FROM farms WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Nông trại được xóa thành công']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa nông trại']);
        }
        exit;
    }
}
?>
