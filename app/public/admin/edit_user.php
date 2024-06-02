<?php
include_once '../middleware/authMiddleware.php';
include_once '../include/database.php';
include_once '../include/crud_users.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Get roles
$query = "SELECT DISTINCT role FROM users"; 
$stmt = $db->prepare($query);
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['user_id'])) {
    $user->user_id = $_GET['user_id'];
    $userData = $user->readOne()->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->user_id = $_POST['user_id'];
    $user->nama_lengkap = $_POST['nama_lengkap'];
    $user->email = $_POST['email'];
    $user->username = $_POST['username'];
    $user->no_hp = $_POST['no_hp'];
    if (!empty($_POST['password'])) {
        $user->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    } else {
        $user->password = $userData['password']; // Keep the existing password
    }
    $user->role = $_POST['role'];

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "./uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $user->photo = $target_file;
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message = "File is not an image.";
        }
    } else {
        $user->photo = $userData['photo'];
    }

    if ($user->update()) {
        $message = "User updated successfully!";
        $userData = $user->readOne()->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "Failed to update user.";
    }
}

// Check if user is logged in and has the role 'staff'
AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('staff');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="../css/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5">Edit User</h1>

        <?php if (isset($message)): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded shadow-md mb-5">
            <a href="./view_user.php" class="bg-gray-500 text-white px-4 py-2 rounded">Back to User List</a>
        </div>

        <div class="bg-white p-6 rounded shadow-md">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($userData['user_id']) ?>">
                <div class="mb-4">
                    <label class="block text-gray-700">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($userData['nama_lengkap']) ?>" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($userData['username']) ?>" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">No HP</label>
                    <input type="text" name="no_hp" value="<?= htmlspecialchars($userData['no_hp']) ?>" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Password (Leave blank to keep current password)</label>
                    <input type="password" name="password" class="w-full p-2 border border-gray-300 rounded mt-1">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Role</label>
                    <select name="role" class="w-full p-2 border border-gray-300 rounded mt-1">
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= htmlspecialchars($role['role']) ?>" <?= $role['role'] === $userData['role'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['role']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Photo</label>
                    <input type="file" name="photo" class="w-full p-2 border border-gray-300 rounded mt-1">
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update User</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
