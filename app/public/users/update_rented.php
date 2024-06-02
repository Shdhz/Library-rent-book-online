<?php
session_start();
include_once '../include/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['loan_id']) && isset($data['status']) && isset($data['fine'])) {
        $loan_id = intval($data['loan_id']);
        $new_status = htmlspecialchars($data['status']);
        $fine = intval($data['fine']);
        $user_id = $_SESSION['user_id'];

        try {
            // Start transaction
            $db->beginTransaction();

            // Update the loan status and fine
            $updateQuery = "UPDATE loans SET status = ?, fine = ? WHERE loan_id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$new_status, $fine, $loan_id]);

            // Insert the fine details into the 'denda' table if there's a fine
            if ($fine > 0) {
                $insertFineQuery = "INSERT INTO denda (user_id, loan_id, jumlah_denda, tanggal_denda) VALUES (?, ?, ?, NOW())";
                $insertFineStmt = $db->prepare($insertFineQuery);
                $insertFineStmt->execute([$user_id, $loan_id, $fine]);
            }

            // Commit transaction
            $db->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            // Rollback transaction if something goes wrong
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
}
?>
