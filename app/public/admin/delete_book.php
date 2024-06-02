<?php
include_once '../middleware/authMiddleware.php';
include_once '../include/database.php';
include_once '../include/crud_books.php';

$database = new Database();
$db = $database->getConnection();
$book = new Books($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book->book_id = $_POST['book_id'];
    if ($book->delete()) {
        $message = "Data Buku berhasil dihapus!";
        header("Location: view_books.php?status=success&message=" . urlencode($message));
    } else {
        $message = "Data Buku gagal dihapus.";
        header("Location: view_books.php?status=error&message=" . urlencode($message));
    }
    exit;
}
?>
