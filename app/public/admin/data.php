<?php
include_once '../include/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Query untuk mendapatkan jumlah buku
    $query = "SELECT COUNT(*) as total_books FROM books";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $total_books = $stmt->fetch(PDO::FETCH_ASSOC)['total_books'];

    // Query untuk mendapatkan jumlah buku yang dipinjam
    $query = "SELECT COUNT(*) as borrowed_books FROM loans";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $borrowed_books = $stmt->fetch(PDO::FETCH_ASSOC)['borrowed_books'];

    // Query untuk mendapatkan jumlah anggota
    $query = "SELECT COUNT(*) as total_members FROM users";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $total_members = $stmt->fetch(PDO::FETCH_ASSOC)['total_members'];

    $data = [
        'total_books' => $total_books,
        'borrowed_books' => $borrowed_books,
        'total_members' => $total_members
    ];

    // Debugging: Log data yang akan dikirim ke frontend
    error_log(json_encode($data));

    header('Content-Type: application/json');
    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
