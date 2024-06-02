<?php
include_once '../middleware/authMiddleware.php';
include_once '../include/database.php';
include_once '../include/crud_users.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Tentukan jumlah data per halaman
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil data user
$totalUsers = $user->countAll();
$users = $user->readAll($limit, $offset)->fetchAll(PDO::FETCH_ASSOC);

// Cek apakah user sudah login dan memiliki role 'staff'
AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('staff');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="../../css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <?php include "./sidebar_admin.php" ?>
        <!-- head Content -->
        <div class="flex-1 p-10">
            <!-- main content -->
            <div class="container mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">User List</h1>
                </div>
                <div class="bg-white p-6 rounded shadow-md mb-5 flex justify-between">
                    <input type="search" name="" id="" class="border-2">
                    <a href="./add_user.php" class="bg-primary-10 text-white px-4 py-2 rounded">Add User</a>
                </div>
                <div class="bg-white p-6 rounded shadow-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="text-[14px]">
                                <th class="px-6 py-3 bg-gray-50">No</th>
                                <th class="px-6 py-3 bg-gray-50">Nama Lengkap</th>
                                <th class="px-6 py-3 bg-gray-50">Email</th>
                                <th class="px-6 py-3 bg-gray-50">Username</th>
                                <th class="px-6 py-3 bg-gray-50">No HP</th>
                                <th class="px-6 py-3 bg-gray-50">Role</th>
                                <th class="px-6 py-3 bg-gray-50">Photo</th>
                                <th class="px-6 py-3 bg-gray-50">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php 
                            $no = $offset + 1;
                            foreach ($users as $user): 
                            ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $no++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['username']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['no_hp']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['role']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($user['photo']): ?>
                                            <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Photo" class="h-10 w-10 rounded-full">
                                        <?php else: ?>
                                            No Photo
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="./edit_user.php?user_id=<?= $user['user_id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                                        <button data-user-id="<?= $user['user_id'] ?>" class="delete-user-btn bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <div class="mt-4 px-4 py-4">
                        <?php
                        $totalPages = ceil($totalUsers / $limit);
                        for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="px-3 py-1 border rounded <?php if ($page == $i) echo 'bg-blue-500 text-white'; ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-user-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const userId = this.dataset.userId;

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Kamu tidak bisa mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus saja!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'delete_user.php';

                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'user_id';
                        input.value = userId;

                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message');

            if (status && message) {
                Swal.fire({
                    icon: status === 'success' ? 'success' : 'error',
                    title: status === 'success' ? 'Success' : 'Error',
                    text: message
                });
            }
        });
    </script>
</body>
</html>
