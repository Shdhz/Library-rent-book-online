<?php
require_once '../include/database.php'; // Adjust the path as necessary

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Mark notification as read
if (isset($_POST['id'])) {
    $notificationId = $_POST['id'];
    $database->markNotificationAsRead($notificationId);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
