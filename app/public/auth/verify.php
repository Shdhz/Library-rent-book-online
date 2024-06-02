<?php
include_once '../include/database.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_code = ?");
    $stmt->execute([$code]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $stmt = $conn->prepare("UPDATE users SET verified = 1, verification_code = NULL WHERE verification_code = ?");
        $stmt->execute([$code]);
        echo "<p>Email verified successfully! You will be redirected to the login page shortly.</p>";
        echo "<p>If you are not redirected, <a href='../auth/login.php'>click here</a>.</p>";
        header("refresh:5;url=../auth/login.php"); // Redirect after 5 seconds
        exit(); // Ensure no further processing is done
    } else {
        echo "Invalid verification code.";
    }
} else {
    echo "No verification code provided.";
}
?>
