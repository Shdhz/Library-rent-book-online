<?php
include "./database.php";

class Notification {
    private $connect;
    private $table_name = "notifications";

    public $notification_id;
    public $user_id;
    public $message;
    public $created_at;
    public $is_read;
    public $loan_id;

    public function __construct($db) {
        $this->connect = $db;
    }

    public function getDueBooks($currentTime, $endTime) {
        $query = "SELECT loan_id, user_id, book_id, return_date 
                  FROM loans 
                  WHERE return_date BETWEEN ? AND ?";
        $stmt = $this->connect->prepare($query);
        $stmt->execute([$currentTime->format('Y-m-d H:i:s'), $endTime->format('Y-m-d H:i:s')]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createNotification($userId, $message, $loanId) {
        try {
            $query = "INSERT INTO " . $this->table_name . " (user_id, message, created_at, is_read, loan_id) 
                      VALUES (?, ?, NOW(), 0, ?)";
            $stmt = $this->connect->prepare($query);
            $stmt->execute([$userId, $message, $loanId]);
        } catch (PDOException $exception) {
            error_log("Error creating notification: " . $exception->getMessage());
        }
    }

    public function getUserNotifications($userId) {
        $query = "SELECT notification_id, message 
                  FROM " . $this->table_name . " 
                  WHERE user_id = ? AND is_read = 0";
        $stmt = $this->connect->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markNotificationAsRead($notificationId) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_read = 1 
                  WHERE notification_id = ?";
        $stmt = $this->connect->prepare($query);
        $stmt->execute([$notificationId]);
    }
}
?>
