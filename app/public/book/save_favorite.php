<?php
session_start();
include_once '../include/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : null;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

    if ($book_id && $user_id) {
        $database = new Database();
        $db = $database->getConnection();

        // Check if the book is already in favorites
        $query = "SELECT COUNT(*) as count FROM favorites WHERE user_id = ? AND book_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id, $book_id]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($count == 0) {
            // Insert favorite into the database
            $query = "INSERT INTO favorites (user_id, book_id) VALUES (?, ?)";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$user_id, $book_id])) {
                echo json_encode(['success' => true, 'message' => 'Buku telah ditambahkan ke favorit.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan buku ke favorit.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Buku sudah ada di favorit.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode permintaan tidak valid.']);
}
?>
