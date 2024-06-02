<?php
session_start();
require_once '../include/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Get the current time and the time an hour from now
$current_time = new DateTime();
$one_hour_later = (new DateTime())->modify('+1 hour');

try {
    // Prepare the query to find loans that are due within the next hour
    $query = "SELECT user_id, book_id FROM loans WHERE due_date BETWEEN ? AND ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$current_time->format('Y-m-d H:i:s'), $one_hour_later->format('Y-m-d H:i:s')]);

    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($loans)) {
        // Prepare the query to insert notifications
        $insert_query = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $insert_stmt = $db->prepare($insert_query);

        foreach ($loans as $loan) {
            // Create a notification message
            $message = "Your loan for book ID " . $loan['book_id'] . " is due within an hour.";

            // Insert the notification into the notifications table
            $insert_stmt->execute([$loan['user_id'], $message]);
        }
    }

    echo json_encode(['status' => 'success']);
} catch (PDOException $exception) {
    // Handle database errors
    echo json_encode(['status' => 'error', 'message' => $exception->getMessage()]);
}
?>
