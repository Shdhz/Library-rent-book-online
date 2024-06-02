<?php
session_start();
include_once '../include/database.php';
include_once '../include/user.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Sanitize input
$user->username = htmlspecialchars(strip_tags($_POST['username']));
$user->password = htmlspecialchars(strip_tags($_POST['password']));

$stmt = $user->login();
$num = $stmt->rowCount();

if ($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging output
    echo "<pre>";
    var_dump($row);
    var_dump($user->password);
    var_dump(password_verify($user->password, $row['password']));
    echo "</pre>";

    if (password_verify($user->password, $row['password'])) {
        // Password is correct
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['nama_lengkap'] = $row['nama_lengkap'];

        if ($row['role'] === 'staff') {
            header("Location: ../admin/home_admin.php");
            exit;
        } elseif ($row['role'] === 'anggota') {
            header("Location: ../users/home_user.php");
            exit;
        } else {
            header("Location: login.php?error=invalid_role");
            exit;
        }
    } else {
        echo "Invalid credentials: password does not match.";
        exit;
    }
} else {
    echo "Invalid credentials: no user found.";
    exit;
}
?>
