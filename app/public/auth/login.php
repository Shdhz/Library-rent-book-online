<?php
    include_once '../middleware/authMiddleware.php';
    AuthMiddleware::checkNotLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="../css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl flex">
        <div class="w-1/2 p-8">
            <h2 class="text-2xl font-bold mb-6">Welcome Back</h2>
            <form action="./login_process.php" method="post">
                <div class="mb-4 relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-user text-gray-400"></i>
                    </span>
                    <input type="text" name="username" id="username" placeholder="Username" class="w-full pl-10 p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4 relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-lock text-gray-400"></i>
                    </span>
                    <input type="password" name="password" id="password" placeholder="Password" class="w-full pl-10 p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <a href="./forgot_password.php" class="text-sm text-blue-500">Forgot password?</a>
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded mb-4">Login</button>
            </form>
            <p class="text-center text-gray-500">Don't have an account? <a href="register.php" class="text-blue-500">Register</a></p>
        </div>
        <div class="w-1/2 bg-gray-100 flex items-center justify-center">
            <img src="../assets/Asset 1.png" alt="Login Image" class="w-full h-full object-cover rounded-br-lg rounded-tr-lg">
        </div>
    </div>
</body>
</html>
