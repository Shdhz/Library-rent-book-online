<?php
include_once '../include/database.php';
include_once '../include/crud_books.php';

$database = new Database();
$db = $database->getConnection();
$books = new Books($db);

$search = isset($_POST['search']) ? $_POST['search'] : '';
$publisher = isset($_POST['publisher']) ? $_POST['publisher'] : '';
$author = isset($_POST['author']) ? $_POST['author'] : '';
$genre = isset($_POST['genre']) ? $_POST['genre'] : '';

// Debugging log
error_log("Search: $search, Publisher: $publisher, Author: $author, Genre: $genre");

// Lanjutkan dengan pemrosesan data
$booksList = $books->searchBooks($search, $publisher, $author, $genre)->fetchAll(PDO::FETCH_ASSOC);

if ($booksList && count($booksList) > 0) {
    echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
    foreach ($booksList as $book) {
        $coverUrl = '/admin/' . htmlspecialchars($book['cover']);
        echo '<div class="bg-white shadow rounded-lg flex flex-col">
                <a href="/book/detail_books.php?book_id=' . htmlspecialchars($book['book_id']) . '" class="flex flex-col items-start">
                    <img src="' . $coverUrl . '" alt="Book Cover" class="w-full h-64 object-cover transition-all duration-200 hover:scale-95">
                    <div class="p-4 flex flex-col items-start">
                        <h3 class="text-lg font-medium">' . htmlspecialchars($book['title']) . '</h3>
                        <p class="text-s_font">' . htmlspecialchars($book['author']) . '</p>
                    </div>
                </a>
              </div>';
    }
    echo '</div>';
} else {
    echo '<p>Tidak ada buku yang ditemukan.</p>';
}
