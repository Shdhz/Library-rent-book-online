<?php
include_once '../middleware/authMiddleware.php';
include_once '../include/database.php';
include_once '../include/crud_books.php';

$database = new Database();
$db = $database->getConnection();
$book = new Books($db);

// Get categories
$query = "SELECT category_id, category_name FROM categories"; 
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book->title = $_POST['title'];
    $book->author = $_POST['author'];
    $book->category_id = $_POST['category_id'];
    $book->isbn = $_POST['isbn'];
    $book->publish_date = $_POST['publish_date'];
    $book->available_copies = $_POST['available_copies'];
    $book->publisher = $_POST['publisher'];
    $book->sinopsis = $_POST['sinopsis'];

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "./uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $book->cover = $target_file;
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message = "File is not an image.";
        }
    }

    if ($book->create()) {
        $message = "Book added successfully!";
    } else {
        $message = "Failed to add Book.";
    }
}

// Cek apakah user sudah login dan memiliki role 'staff'
AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('staff');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff - Add Book</title>
    <link href="../css/output.css" rel="stylesheet">
    <style>
        #imagePreview {
            display: none;
            width: 100px;
            height: 100px;
            margin-top: 10px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5">Add Book</h1>

        <?php if (isset($message)): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded shadow-md mb-5">
            <a href="./view_books.php" class="bg-gray-500 text-white px-4 py-2 rounded">Back to Book list</a>
        </div>

        <div class="bg-white p-6 rounded shadow-md">
            <form action="" method="POST" enctype="multipart/form-data">
                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">ISBN</span>
                    <input type="number" name="isbn" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Judul Buku</span>
                    <input type="text" name="title" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Sinopsis buku</span>
                    <textarea name="sinopsis" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required></textarea>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Pengarang</span>
                    <input type="text" name="author" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Penerbit</span>
                    <input type="text" name="publisher" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Jumlah salinan buku</span>
                    <input type="number" name="available_copies" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Tanggal terbit</span>
                    <input type="date" name="publish_date" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Kategori Buku</span>
                    <select name="category_id" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" required>
                        <option value="">---- Pilih kategori buku ----</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="block mb-4">
                    <span class="block text-sm font-medium text-slate-700">Cover buku</span>
                    <input type="file" name="photo" class="border-slate-200 placeholder-slate-400 contrast-more:border-slate-400 contrast-more:placeholder-slate-500 w-full p-2 border rounded mt-1" onchange="previewImage(event)">
                    <img id="imagePreview" src="" alt="Image Preview" class="mt-2">
                </label>

                <div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Book</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const imagePreview = document.getElementById('imagePreview');
                imagePreview.src = reader.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
