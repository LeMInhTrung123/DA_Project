<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - UserManagement</title>
    <?php require('inc/links.php'); ?>
    <style>
        body {
            background-color: #f4f6f9;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        

        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #343a40;
            color: #fff;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
        }

        .card-body {
            padding: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #495057;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .btn-dark {
            background-color: #343a40;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            padding: 10px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease-in-out;
        }

        .btn-dark:hover {
            background-color: #23272b;
            transform: scale(1.05);
        }

        .table {
            margin-top: 20px;
            border-collapse: collapse;
        }

        .table thead {
            background-color: #343a40;
            color: #fff;
        }

        .table th,
        .table td {
            padding: 10px;
            text-align: center;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }
    </style>
</head>

<body class="bg-light">
    
     <?php require('inc/header.php'); ?>
        
    <div class="container mt-5">
        <h3 class="text-center mb-4">QUẢN LÝ NGƯỜI DÙNG</h3>

        <!-- Form thêm người dùng -->
        <div class="card mb-5">
            <div class="card-header">Thêm Người Dùng Mới</div>
            <div class="card-body">
                <form id="add-user-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" class="form-control" placeholder="Nhập email..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Họ và Tên:</label>
                            <input type="text" id="name" class="form-control" placeholder="Nhập họ và tên..." required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Mật khẩu:</label>
                            <input type="password" id="password" class="form-control" placeholder="Nhập mật khẩu..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm-password" class="form-label">Xác nhận mật khẩu:</label>
                            <input type="password" id="confirm-password" class="form-control" placeholder="Xác nhận mật khẩu..." required>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-dark w-100" onclick="addUser()">Thêm Người Dùng</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách người dùng -->
        <div class="card">
            <div class="card-header">Danh Sách Người Dùng</div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Họ và Tên</th>
                            <th>ID Login</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="user-table-body">
                        <!-- Dữ liệu được tải bằng AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php require('inc/scripts.php'); ?>
    <script>
        function addUser() {
            const email = document.getElementById('email').value;
            const name = document.getElementById('name').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (password !== confirmPassword) {
                alert("Mật khẩu không khớp!");
                return;
            }

            fetch('ajax/settings_crud.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `register_user=1&email=${email}&name=${name}&password=${password}`

                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        document.getElementById('add-user-form').reset();
                        loadUsers();
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                });
        }

        function loadUsers() {
    fetch('ajax/settings_crud.php?get_users=1')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('user-table-body');
            tableBody.innerHTML = ''; 

            data.forEach(user => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.email}</td>
                        <td>${user.name}</td>
                        <td>${user.id_login ? user.id_login : 'NULL'}</td>
                        <td>
                            <button class="btn btn-warning btn-sm me-2" onclick="editUser(${user.id}, '${user.email}', '${user.name}', '${user.id_login}')">Sửa</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">Xóa</button>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            console.error('Lỗi:', error);
            alert('Không thể tải danh sách người dùng.');
        });
}

        function editUser(id, email, name, idLogin) {
    document.getElementById('edit-user-id').value = id;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-id-login').value = idLogin ? idLogin : '';
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}
function updateUser() {
    const id = document.getElementById('edit-user-id').value;
    const email = document.getElementById('edit-email').value;
    const name = document.getElementById('edit-name').value;
    const idLogin = document.getElementById('edit-id-login').value;

    fetch('ajax/settings_crud.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `update_user=1&id=${id}&email=${email}&name=${name}&id_login=${idLogin}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            loadUsers();
            bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại.');
    });
}



        function deleteUser(userId) {
            if (!confirm("Bạn có chắc chắn muốn xóa người dùng này?")) return;

            fetch('ajax/settings_crud.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `delete_user=1&id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert("Người dùng đã được xóa thành công!");
                        loadUsers(); // Tải lại danh sách người dùng sau khi xóa
                    } else {
                        alert("Không thể xóa người dùng: " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                });
        }

        document.addEventListener('DOMContentLoaded', loadUsers);
    </script>
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-user-form">
                    <input type="hidden" id="edit-user-id">
                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" id="edit-email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Họ và Tên:</label>
                        <input type="text" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Login:</label>
                        <input type="text" id="edit-id-login" class="form-control">
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary" onclick="updateUser()">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>

</html>