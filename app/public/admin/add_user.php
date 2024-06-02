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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->nama_lengkap = $_POST['nama_lengkap'];
    $user->email = $_POST['email'];
    $user->username = $_POST['username'];
    $user->no_hp = $_POST['no_hp'];
    $user->password = $_POST['password'];
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
    }

    if ($user->create()) {
        $message = "User added successfully!";
    } else {
        $message = "Failed to add user.";
    }
}

// Cek apakah user sudah login dan memiliki role 'staff'
AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('staff');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add User</title>
    <link href="../css/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5">Add User</h1>

        <?php if (isset($message)): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded shadow-md mb-5">
            <a href="./view_user.php" class="bg-gray-500 text-white px-4 py-2 rounded">Back to User List</a>
        </div>

        <div class="bg-white p-6 rounded shadow-md">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Email</label>
                    <input type="email" name="email" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Username</label>
                    <input type="text" name="username" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">No HP</label>
                    <input type="text" name="no_hp" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Password</label>
                    <input type="password" name="password" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Role</label>
                    <select name="role" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                        <option value="">---- Select Role ----</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo htmlspecialchars($role['role']); ?>">
                                <?php echo htmlspecialchars($role['role']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Photo</label>
                    <input type="file" name="photo" class="w-full p-2 border border-gray-300 rounded mt-1">
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create User</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
