<?php
session_start();
require_once '../include/database.php'; // Sesuaikan jalur ini dengan struktur proyek Anda

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

$response = ['notifications' => []];

try {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Log the notifications
        error_log("Fetched Notifications: " . print_r($notifications, true));

        $response['notifications'] = $notifications;
    }

    echo json_encode($response);
} catch (PDOException $exception) {
    // Handle database errors
    echo json_encode(['error' => 'Database error: ' . $exception->getMessage()]);
}
?>
