<?php
session_start(); // Mulai sesi
include_once '../middleware/authMiddleware.php';
include_once '../include/database.php';
include_once '../include/mail.php';

AuthMiddleware::checkNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $photo = '';
    $no_hp = '';

    if ($password !== $confirm_password) {
        $_SESSION['message'] = 'Passwords do not match.';
        header("Location: register.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $verification_code = md5(uniqid("yourrandomstring", true));

    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, username, password, verification_code, photo, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$nama_lengkap, $email, $username, $hashed_password, $verification_code, $photo, $no_hp])) {
        $verification_link = "http://localhost:8000/auth/verify.php?code=$verification_code";

        $subject = "Email Verification";
        $message = "Please click the link below to verify your email address: <a href='$verification_link'>$verification_link</a>";
        if (sendMail($email, $subject, $message)) {
            $_SESSION['message'] = 'Registrasi berhasil, silahkan cek email anda untuk verifikasi!';
        } else {
            $_SESSION['message'] = 'Failed to send verification email.';
        }
    } else {
        $_SESSION['message'] = 'Failed to register user.';
    }

    header("Location: register.php");
    exit();
}
?>
