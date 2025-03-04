<?php
require('inc/essentials.php');
adminLogin();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dashboard</title>
    <?php require('inc/links.php'); ?>
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            transition: 0.3s ease-in-out;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background-color: #28a745;
            color: #fff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .btn-close {
            color: #fff;
        }

        .btn-close:hover {
            color: #f8f9fa;
        }

        .btn-danger {
            transition: 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #d33;
        }
    </style>
</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <div class="container py-5">
        <h3 class="text-center mb-4">DANH SÁCH NÔNG TRẠI</h3>

        <!-- Table Container -->
        <div class="table-container">
            <table class="table table-striped table-hover">
                <thead class="table-success">
                    <tr>
                        <th>STT</th>
                        <th>Tên nông trại</th>
                        <th>Địa chỉ</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="farm-list">
                    <!-- Dữ liệu sẽ được thêm qua JavaScript -->
                </tbody>
            </table>

            <div class="text-end">
                <button class="btn btn-custom" onclick="showAddFarmModal()">+ Thêm Nông Trại</button>
            </div>
        </div>
    </div>

    <!-- Modal Thêm Nông Trại -->
    <div class="modal fade" id="addFarmModal" tabindex="-1" aria-labelledby="addFarmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFarmModalLabel">Thêm Nông Trại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addFarmForm">
                        <div class="mb-3">
                            <label for="farmName" class="form-label">Tên Nông Trại</label>
                            <input type="text" class="form-control" id="farmName" name="farmName" placeholder="Nhập tên nông trại" required>
                        </div>
                        <div class="mb-3">
                            <label for="farmAddress" class="form-label">Địa Chỉ</label>
                            <input type="text" class="form-control" id="farmAddress" name="farmAddress" placeholder="Nhập địa chỉ nông trại" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Thêm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fetchFarms() {
            fetch('ajax/farm_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=get_farms'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const farmList = document.getElementById('farm-list');
                        farmList.innerHTML = '';
                        data.data.forEach((farm, index) => {
                            farmList.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${farm.name}</td>
                        <td>${farm.address}</td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="observeFarm(${farm.id}, '${farm.name}')">Quan sát</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteFarm(${farm.id})">Xóa</button>
                        </td>
                    </tr>
                `;
                        });
                    } else {
                        alert('Lỗi khi tải danh sách nông trại.');
                    }
                });
        }

        // Hàm xử lý khi nhấn nút "Quan sát"
        function observeFarm(farmId, farmName) {
            if (farmName !== 'broTech') {
                alert('Bạn không được phép quan sát nông trại này!');
                return;
            }
            // Điều hướng tới trang data.php với tham số ID nông trại
            window.location.href = `../data.php?farm_id=${farmId}`;
        }


        function showAddFarmModal() {
            const modal = new bootstrap.Modal(document.getElementById('addFarmModal'));
            modal.show();
        }

        document.getElementById('addFarmForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('farmName').value;
            const address = document.getElementById('farmAddress').value;

            fetch('ajax/farm_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=add_farm&name=${encodeURIComponent(name)}&address=${encodeURIComponent(address)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        fetchFarms();
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addFarmModal'));
                        modal.hide();
                    } else {
                        alert(data.message);
                    }
                });
        });

        function deleteFarm(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa nông trại này?')) return;

            fetch('ajax/farm_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=delete_farm&id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        fetchFarms();
                    } else {
                        alert(data.message);
                    }
                });
        }

        // Tải danh sách nông trại khi trang được load
        document.addEventListener('DOMContentLoaded', fetchFarms);
    </script>

    <?php require('inc/scripts.php'); ?>

</body>

</html>