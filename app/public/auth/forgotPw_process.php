<?php
session_start();
include_once '../include/database.php';
include_once '../include/user.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Sanitize input
$identifier = htmlspecialchars(strip_tags($_POST['identifier']));
$new_password = htmlspecialchars(strip_tags($_POST['new_password']));

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Find user by identifier (username or email)
$stmt = $user->findByIdentifier($identifier);
$num = $stmt->rowCount();

$message = "";

if ($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update password
    $user->updatePassword($row['user_id'], $hashed_password);
    $message = "PASSWORD BERHASIL DIRESET !";
    header("refresh:5;url=./login.php");
} else {
    echo "User tidak ditemukan.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reset pw</title>
    <link rel="stylesheet" href="../css/output.css">
</head>
<body class="h-screen flex items-center justify-center bg-slate-900">
    <div class="container text-center w-1/4 px-2 py-2">
        <div class=" text-bg_color">
            <h1 class="text-xl font-bold"><?= $message ?></h1>
            <h1 class="text-slate-300 mt-2">*Tunggu beberapa saat, anda akan diarahkan kembali kehalaman login</h1>
            <img class="w-full mt-5 rounded-full" width="220" height="220" src="https://img.icons8.com/fluency/240/ok--v1.png" alt="ok--v1"/>
        </div>
    </div>
</body>
</html>
