<?php
    include_once '../middleware/authMiddleware.php';
    AuthMiddleware::checkNotLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="../css/output.css" rel="stylesheet">
</head>
<body class="h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl flex">
        <div class="w-1/2 p-8">
            <h2 class="text-2xl font-bold mb-6">Reset Password</h2>
            <form action="./forgotPw_process.php" method="post">
                <div class="mb-4 relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-user text-gray-400"></i>
                    </span>
                    <input type="text" name="identifier" placeholder="Masukkan username atau email kamu" class="w-full pl-10 p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4 relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-lock text-gray-400"></i>
                    </span>
                    <input type="password" name="new_password" placeholder="Masukkan password baru" class="w-full pl-10 p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded mb-4">Ubah Password</button>
            </form>
        </div>
        <div class="w-1/2 bg-gray-100 flex items-center justify-center">
            <img src="../assets/Forgot password-amico.svg" alt="Reset Password Image" class="w-full h-full object-cover rounded-br-lg rounded-tr-lg">
        </div>
    </div>
</body>
</html>

