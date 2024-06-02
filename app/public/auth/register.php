<?php
include_once '../middleware/authMiddleware.php';
AuthMiddleware::checkNotLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="../css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-8">
        <h2 class="text-2xl font-bold mb-6">Register new account</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-4 text-center text-gray-400 bg-green-600 bg-opacity-25 px-2 py-2 rounded-lg">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Hapus pesan dari session setelah ditampilkan
                ?>
            </div>
        <?php endif; ?>

        <form action="./register_process.php" method="post">
            <div class="mb-4 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-user text-gray-400"></i>
                </span>
                <input type="text" name="nama_lengkap" id="nama_lengkap" placeholder="Full name" class="w-full pl-10 p-2 border border-gray-300 rounded mt-1" required>
            </div>
            <div class="mb-4 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-user text-gray-400"></i>
                </span>
                <input type="text" name="username" id="username" placeholder="Username" class="w-full pl-10 p-2 border border-gray-300 rounded mt-1" required>
            </div>
            <div class="mb-4 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-envelope text-gray-400"></i>
                </span>
                <input type="email" name="email" id="email" placeholder="Email" class="w-full pl-10 p-2 border border-gray-300 rounded mt-1" required>
            </div>
            <div class="mb-4 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-lock text-gray-400"></i>
                </span>
                <input type="password" name="password" id="password" placeholder="Password" class="w-full pl-10 p-2 border border-gray-300 rounded mt-1" required>
            </div>
            <div class="mb-4 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-lock text-gray-400"></i>
                </span>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" class="w-full pl-10 p-2 border border-gray-300 rounded mt-1" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded mb-4">Register</button>
        </form>
        <p class="text-center text-gray-500">Already have an account? <a href="login.php" class="text-blue-500">Login</a></p>
    </div>
</body>
</html>
