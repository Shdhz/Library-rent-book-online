<?php
include_once '../middleware/authMiddleware.php';
include_once '../include/database.php';
include_once '../include/crud_books.php';

$database = new Database();
$db = $database->getConnection();
$book = new Books($db);

if (isset($_GET['book_id'])) {
    $book->book_id = $_GET['book_id'];
    $bookData = $book->readOne()->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book->book_id = $_POST['book_id'];
    $book->isbn = $_POST['isbn'];
    $book->title = $_POST['title'];
    $book->author = $_POST['author'];
    $book->category_id = $_POST['category_id'];
    $book->publisher = $_POST['publisher'];
    $book->publish_date = $_POST['publish_date'];
    $book->sinopsis = $_POST['sinopsis'];
    $book->available_copies = $_POST['available_copies'];

    // Handle file upload
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "./uploads/";
        $target_file = $target_dir . basename($_FILES["cover"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["cover"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["cover"]["tmp_name"], $target_file)) {
                $book->cover = $target_file;
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message = "File is not an image.";
        }
    } else {
        $book->cover = $bookData['cover'];
    }

    if ($book->update()) {
        $message = "Book updated successfully!";
        $bookData = $book->readOne()->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "Failed to update book.";
    }
}

// Get categories
$query = "SELECT category_id, category_name FROM categories"; 
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if user is logged in and has the role 'staff'
AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('staff');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="../css/output.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5">Edit Book</h1>

        <?php if (isset($message)): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded shadow-md mb-5">
            <a href="./view_books.php" class="bg-gray-500 text-white px-4 py-2 rounded">Back to Book List</a>
        </div>

        <div class="bg-white p-6 rounded shadow-md">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="book_id" value="<?= htmlspecialchars($bookData['book_id']) ?>">

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">ISBN</span>
                    <input type="text" name="isbn" value="<?= htmlspecialchars($bookData['isbn']) ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Judul buku</span>
                    <input type="text" name="title" value="<?= htmlspecialchars($bookData['title']) ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Pengarang</span>
                    <input type="text" name="author" value="<?= htmlspecialchars($bookData['author']) ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Kategori buku</span>
                    <select name="category_id" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['category_id']) ?>" <?= $category['category_id'] == $bookData['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Penerbit</span>
                    <input type="text" name="publisher" value="<?= htmlspecialchars($bookData['publisher']) ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Tanggal terbit</span>
                    <input type="date" name="publish_date" value="<?= htmlspecialchars($bookData['publish_date']) ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Sinopsis</span>
                    <textarea name="sinopsis" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required><?= htmlspecialchars($bookData['sinopsis']) ?></textarea>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Jumlah salinan buku</span>
                    <input type="number" name="available_copies" value="<?= htmlspecialchars($bookData['available_copies']) ?>" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Cover buku</span>
                    <input type="file" name="cover" class="w-full    rounded mt-4 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" accept="image/*">
                    <?php if ($bookData['cover']): ?>
                        <img id="cover-preview" src="<?= htmlspecialchars($bookData['cover']) ?>" alt="Cover" class="w-25 h-auto mt-4 object-cover">
                    <?php endif; ?>
                </label>

                <div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Book</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelector('input[name="cover"]').addEventListener('change', function(event) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('cover-preview').src = e.target.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
</body>
</html>
