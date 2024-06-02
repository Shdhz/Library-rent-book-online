<?php
include_once '../include/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT user_id, password FROM users";
$stmt = $db->prepare($query);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $user_id = $row['user_id'];
    $plain_password = $row['password'];

    // Check if password is already hashed
    if (!password_get_info($plain_password)['algo']) {
        // If not hashed, hash the password
        $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);
        
        // Update the password in the database
        $update_query = "UPDATE users SET password = :password WHERE user_id = :user_id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(':password', $hashed_password);
        $update_stmt->bindParam(':user_id', $user_id);
        $update_stmt->execute();
    }
}
echo "Passwords updated successfully.";
?>
