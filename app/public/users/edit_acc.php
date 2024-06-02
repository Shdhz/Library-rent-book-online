<?php 
include_once "../include/database.php";
include_once '../middleware/authMiddleware.php';

// Ensure the user is logged in and has the 'anggota' role
AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('anggota');

// Database connection
$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];

if (!$user_id) {
    die('User ID is not set in the session.');
}

// Handle form submission for email change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_email'])) {
    $new_email = htmlspecialchars($_POST['new_email']);
    
    // Update email query
    $query = "UPDATE users SET email = ? WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$new_email, $user_id]);
}

// Handle form submission for password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new_password = password_hash(htmlspecialchars($_POST['new_password']), PASSWORD_BCRYPT);
    
    // Update password query
    $query = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$new_password, $user_id]);
}

$query = "SELECT username, photo, email FROM users WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Failed to fetch user data.');
}

$username = htmlspecialchars($user['username'] ?? '');
$photo = htmlspecialchars($user['photo'] ?? '');
$email = htmlspecialchars($user['email'] ?? '');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/output.css">
    <title>My Account</title>
</head>
<body>
    <?php include "../include/header.php" ?>
    <div class="flex">
        <?php include "./sidebar_profile.php" ?>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow-md rounded-lg overflow-x-auto p-6">
                <h1 class="text-xl font-bold mb-4">Akun Saya</h1>
                <hr class="mt-2 mb-4">
                
                <div class="bg-white shadow-lg rounded-lg overflow-x-auto p-6">
                    <!-- Change Email Form -->
                    <h2 class="text-xl font-bold mb-4">Ubah Email</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="change_email" value="1">
                        <div class="mb-4">
                            <label for="new_email" class="block text-gray-700 font-medium mb-2">Email baru</label>
                            <input type="email" name="new_email" id="new_email" value="<?= $email ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1">
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-primary-10 hover:bg-icon_color text-white font-medium py-2 px-11 rounded focus:outline-none focus:shadow-outline">Ubah Email</button>
                        </div>
                    </form>
                </div>

                <div class="bg-white shadow-lg rounded-lg overflow-x-auto p-6 mt-5">
                    <!-- Change Password Form -->
                    <h2 class="text-xl font-bold mb-4 mt-6">Ubah Password</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="change_password" value="1">
                        <div class="mb-4">
                            <label for="new_password" class="block text-gray-700 font-medium mb-2">Password baru</label>
                            <input type="password" name="new_password" id="new_password" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1">
                        </div>
                        <div class="mb-4">
                            <label for="confirm_new_password" class="block text-gray-700 font-medium mb-2">Konfirmasi password baru</label>
                            <input type="password" name="confirm_new_password" id="confirm_new_password" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1">
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-primary-10 hover:bg-icon_color text-white font-medium py-2 px-8 rounded focus:outline-none focus:shadow-outline">Ubah Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include "../include/footer.php" ?>
</body>
</html>
