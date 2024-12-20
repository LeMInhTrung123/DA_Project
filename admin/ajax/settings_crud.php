<?php
    require('../inc/db_config.php');
    require('../inc/essentials.php');
    adminLogin();
    

    if(isset($_POST['get_general']))
    {
        $q = "SELECT * FROM `settings` WHERE `sr_no` = ?";
        $value = [1];
        $res = select($q,$value,"i");
        $data = mysqli_fetch_assoc($res);
        $json_data = json_encode($data);
        echo $json_data;
    }

    if(isset($_POST['upd_general']))
    {
        $frm_data = filteration($_POST);

        $q = "UPDATE `settings` SET `site_title`=?,`site_about`=? WHERE `sr_no`=?";
        $values = [$frm_data['site_title'],$frm_data['site_about'],1];
        $res = update($q,$values,'ssi');
        echo $res;
    }

    if(isset($_POST['upd_shutdown']))
    {
        $frm_data = ($_POST['upd_shutdown']==0) ? 1 : 0;

        $q = "UPDATE `settings` SET `shutdown`=? WHERE `sr_no`=?";
        $values = [$frm_data,1];
        $res = update($q,$values,'ii');
        echo $res;
    }
    // Xử lý thêm người dùng
    if (isset($_GET['get_users'])) {
        $query = $con->query("SELECT id, email, name FROM users");
        $users = [];
    
        while ($row = $query->fetch_assoc()) {
            $users[] = $row;
        }
    
        echo json_encode($users);
        exit;
    }
    
    if (isset($_POST['register_user'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
        $check_query = $con->prepare("SELECT * FROM users WHERE email = ?");
        $check_query->bind_param("s", $email);
        $check_query->execute();
        $result = $check_query->get_result();
    
        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email này đã tồn tại.']);
            exit;
        }
    
        $insert_query = $con->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
        $insert_query->bind_param("sss", $email, $name, $password);
    
        if ($insert_query->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Người dùng đã được thêm thành công.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi thêm người dùng.']);
        }
        exit;
    }
    if (isset($_POST['delete_user'])) {
        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT); // Lọc dữ liệu đầu vào
    
        $query = $con->prepare("DELETE FROM users WHERE id = ?"); // Chuẩn bị truy vấn SQL
        $query->bind_param("i", $id); // Gắn tham số
    
        if ($query->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Người dùng đã được xóa thành công.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa người dùng.']);
        }
        exit;
    }
    
?>