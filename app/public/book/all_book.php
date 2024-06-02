<?php
session_start();
include_once '../include/database.php';
include_once '../include/crud_books.php';

// Setting error reporting and log configuration
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// ini_set('log_errors', 1);
// ini_set('error_log', 'C:\laragon\bin\apache\httpd-2.4.54-win64-VS16\logs\error.log'); // Adjust this path to match your PHP version
// // Log distinct values for debugging
// error_log("Publishers: " . json_encode($publishers));
// error_log("Authors: " . json_encode($authors));
// error_log("Categories: " . json_encode($categories));

$database = new Database();
$db = $database->getConnection();
$books = new Books($db);

// Get distinct values for filters
$publishers = $books->getDistinctValues('publisher');
$authors = $books->getDistinctValues('author');
$categories = $books->getDistinctCategories();


// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$publisher = isset($_GET['publisher']) ? $_GET['publisher'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';

// Log the received parameters
error_log("Search: " . $search);
error_log("Publisher: " . $publisher);
error_log("Author: " . $author);
error_log("Genre: " . $genre);

// Fetch the book list based on the search parameters
try {
    $booksList = $books->searchBooks($search, $publisher, $author, $genre)->fetchAll(PDO::FETCH_ASSOC);
    // Log the fetched book list
    error_log("Books List: " . json_encode($booksList));
} catch (Exception $e) {
    // Log any exceptions
    error_log("Error fetching books: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/output.css">
    <title>All Books</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
    <script>
        $(document).ready(function(){
            function fetchBooks() {
                var search = $('#search').val();
                var publisher = $('#publisher').val();
                var author = $('#author').val();
                var genre = $('#genre').val();
                $.ajax({
                    url: 'all_book.php',
                    method: 'GET',
                    data: {
                        search: search,
                        publisher: publisher,
                        author: author,
                        genre: genre
                    },
                    success: function(data) {
                        $('#books-container').html($(data).find('#books-container').html());
                        $("img.lazyload").lazyload(); // Initialize lazy loading for images
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: " + status + ": " + error);
                    }
                });
            }
            $('#search').on('input', fetchBooks);
            $('#publisher, #author, #genre').on('change', fetchBooks);
        });
    </script>
</head>
<body class="bg-bg_color">
    <?php include "../include/header.php" ?>

    <div class="container mx-auto mt-8 my-6">
        <div class="grid grid-cols-8 gap-10">
            <div class="bg-white col-span-2 px-2 py-2 rounded-xl">
                <h2 class="text-p_font text-center mt-10 font-bold text-2xl">All books</h2>
                <form id="filter-form">
                    <select name="publisher" id="publisher" class="w-full px-2 py-1 rounded-lg mt-5 bg-black bg-opacity-10">
                        <option value="">Published</option>
                        <?php foreach ($publishers as $pub): ?>
                            <option value="<?= htmlspecialchars($pub ?? '') ?>"><?= htmlspecialchars($pub ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="author" id="author" class="w-full px-2 py-1 rounded-lg mt-2">
                        <option value="">Author</option>
                        <?php foreach ($authors as $auth): ?>
                            <option value="<?= htmlspecialchars($auth ?? '') ?>"><?= htmlspecialchars($auth ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="genre" id="genre" class="w-full px-2 py-1 rounded-lg mt-2">
                        <option value="">Genre</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['category_id'] ?? '') ?>"><?= htmlspecialchars($category['category_name'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <div class="col-span-6 bg-primary-10 bg-opacity-65 rounded-xl">
                <div class="flex justify-between px-2 py-2 mt-10">
                    <div class="tag text-white flex space-x-2">
                        <h2 class="bg-slate-800 rounded-md px-2 py-1">Fantasy</h2>
                        <h2 class="bg-slate-800 rounded-md px-2 py-1">Rating</h2>
                        <h2 class="bg-slate-800 rounded-md px-2 py-1">Most rented</h2>
                    </div>
                    <div class="search mr-10">
                        <input type="search" name="search" id="search" placeholder="Cari ISBN, pengarang, judul buku" class="px-4 py-1 rounded-lg">
                    </div>
                </div>
                <!-- content -->
                <div class="container mx-auto px-4 mb-10 mt-8" id="books-container">
                    <?php if ($booksList && count($booksList) > 0): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <?php foreach ($booksList as $book): ?>
                            <?php
                                $coverUrl = '/admin/' . htmlspecialchars($book['cover']);
                            ?>
                            <div class="bg-white shadow rounded-lg flex flex-col">
                                <a href="/book/hmm.php?book_id=<?= $book['book_id'] ?>" class="flex flex-col items-start">
                                    <img src="<?= $coverUrl ?>" alt="Book Cover" class="w-full h-64 object-cover transition-all duration-200 hover:scale-95 lazyload">
                                    <div class="p-4 flex flex-col items-start">
                                        <h3 class="text-lg font-medium"><?= htmlspecialchars($book['title']) ?></h3>
                                        <p class="text-s_font"><?= htmlspecialchars($book['author']) ?></p>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Tidak ada buku yang ditemukan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include "../include/footer.php" ?>
</body>
</html>
