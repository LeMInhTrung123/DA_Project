<?php
    $hname = 'localhost';
    $uname = 'root';
    $pass = '';
    $db = 'demo';

    // Kết nối MySQL
    $con = mysqli_connect($hname, $uname, $pass, $db);

    // Kiểm tra kết nối
    if (!$con) {
        die("Cannot Connect to Database: " . mysqli_connect_error());
    }else {
        error_log("Connected to database successfully");
    }
    


    if (!function_exists('filteration')) {
        function filteration($data) {
            foreach ($data as $key => $value) {
                $value = trim($value);
                $value = stripslashes($value);
                $value = strip_tags($value);
                $value = htmlspecialchars($value);
                $data[$key] = $value;
            }
            return $data;
        }
    }
    
    if (!function_exists('select')) {
        function select($sql, $values, $datatypes) {
            $con = $GLOBALS['con'];
            if ($stmt = mysqli_prepare($con, $sql)) {
                // Chỉ gọi bind_param nếu có tham số
                if (!empty($datatypes) && !empty($values)) {
                    mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
                }
                if (mysqli_stmt_execute($stmt)) {
                    $res = mysqli_stmt_get_result($stmt);
                    mysqli_stmt_close($stmt);
                    return $res;
                } else {
                    mysqli_stmt_close($stmt);
                    die("Query cannot be executed - Select");
                }
            } else {
                die("Query cannot be prepared - Select");
            }
        }
    }
    

    if (!function_exists('update')) {
        function update($sql, $values, $datatypes) {
            $con = $GLOBALS['con'];
            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
                if (mysqli_stmt_execute($stmt)) {
                    $res = mysqli_stmt_affected_rows($stmt);
                    mysqli_stmt_close($stmt);
                    return $res;
                } else {
                    mysqli_stmt_close($stmt);
                    die("Query cannot be executed - Update");
                }
            } else {
                die("Query cannot be prepared - Update");
            }
        }
    }
?>