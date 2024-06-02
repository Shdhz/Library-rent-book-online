<?php
include_once '../middleware/authMiddleware.php';
include_once '../include/database.php';
include_once '../include/crud_users.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->user_id = $_POST['user_id'];
    if ($user->delete()) {
        $message = "Data User berhasil dihapus!";
        header("Location: view_user.php?status=success&message=" . urlencode($message));
    } else {
        $message = "Data User gagal dihapus.";
        header("Location: view_user.php?status=error&message=" . urlencode($message));
    }
    exit;
}
?>
