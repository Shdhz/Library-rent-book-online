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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = htmlspecialchars($_POST['nama_lengkap']);
    $username = htmlspecialchars($_POST['username']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    
    // Handle file upload if a file is provided
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "../admin/uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        
        // Check if the file was moved successfully
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo = $target_file;

            $query = "UPDATE users SET nama_lengkap = ?, username = ?, no_hp = ?, photo = ? WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$nama_lengkap, $username, $no_hp, $photo, $user_id]);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        $query = "UPDATE users SET nama_lengkap = ?, username = ?, no_hp = ? WHERE user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$nama_lengkap, $username, $no_hp, $user_id]);
    }
}

$query = "SELECT nama_lengkap, username, email, no_hp, photo FROM users WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Debug: Check if user data is fetched
if (!$user) {
    die('Failed to fetch user data.');
}

$nama_lengkap = htmlspecialchars($user['nama_lengkap'] ?? '');
$username = htmlspecialchars($user['username'] ?? '');
$email = htmlspecialchars($user['email'] ?? '');
$no_hp = htmlspecialchars($user['no_hp'] ?? '');
$photo = htmlspecialchars($user['photo'] ?? '');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/output.css">
    <title>My Profile</title>
</head>
<body>
    <?php include "../include/header.php" ?>
    <div class="flex">
        <?php include "./sidebar_profile.php" ?>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow-md rounded-lg overflow-x-auto p-6">
                <h1 class="text-xl font-bold mb-4">User Profile</h1>
                <hr class="mt-2 mb-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="photo" class="block text-gray-700 font-medium mb-2">User Photo</label>
                        <div class="flex">
                            <?php if ($photo): ?>
                                <img src="<?= htmlspecialchars($photo) ?>" alt="Profile Photo" class="mr-4 w-16 h-auto rounded-full mb-4">
                            <?php endif; ?>
                            <input type="file" name="photo" id="photo" class="w-full rounded mt-4 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-primary-10 hover:file:bg-violet-100">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="nama_lengkap" class="block text-gray-700 font-medium mb-2">Full Name</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?= $nama_lengkap ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1">
                    </div>
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                        <input type="text" name="username" id="username" value="<?= $username ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" name="email" id="email" value="<?= $email ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" readonly>
                    </div>
                    <div class="mb-4">
                        <label for="no_hp" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                        <input type="text" name="no_hp" id="no_hp" value="<?= $no_hp ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1">
                    </div>
                    <div class="flex items-center justify-end ">
                        <button type="submit" class="bg-primary-10 hover:bg-icon_color text-white font-medium py-2 px-8 rounded focus:outline-none focus:shadow-outline">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include "../include/footer.php" ?>
</body>
</html>
