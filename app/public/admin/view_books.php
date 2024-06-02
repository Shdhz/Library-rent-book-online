<?php
include_once '../middleware/authMiddleware.php';
include_once '../include/database.php';
include_once '../include/crud_books.php';

$database = new Database();
$db = $database->getConnection();
$books = new Books($db);

// Tentukan jumlah data per halaman
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Cek apakah user sudah login dan memiliki role 'staff'
AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('staff');

// Ambil data buku
$booksTotal = $books->countAll($search);
$booksList = $books->readAll($limit, $offset, $search)->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['ajax'])) {
    echo json_encode(['books' => $booksList, 'totalData' => $booksTotal]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="../css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./js/manage_book.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex flex-auto">
        <?php include "./sidebar_admin.php"; ?>
        <!-- head Content -->
        <div class="flex-1 p-10">
            <!-- main content -->
            <div class="container">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold">Book List</h1>
                </div>
                <div class="bg-white px-4 py-5 rounded shadow-md mb-5 flex justify-between items-center">
                    <input type="search" id="search" placeholder="Search..." class="border-2 rounded p-2 mr-4">
                    <a href="./add_book.php" class="bg-green-500 text-white px-4 py-2 rounded"><span><i class="fa-solid fa-plus"></i></span> Add Book</a>
                </div>
                <div class="bg-white p-6 rounded shadow-md overflow-x-auto">
                    <table id="bookTable" class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead>
                            <tr>
                                <th class="w-1/12 px-4 py-2 bg-gray-50">No</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">ISBN</th>
                                <th class="w-1/4 px-4 py-2 bg-gray-50">Judul Buku</th>
                                <th class="w-1/4 px-4 py-2 bg-gray-50">Pengarang</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Kategori</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Penerbit</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Tanggal Terbit</th>
                                <th class="w-1/12 px-4 py-2 bg-gray-50">Cover Buku</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Jumlah Salinan</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bookTableBody" class="bg-white divide-y divide-gray-200">
                            <?php 
                            $no = $offset + 1;
                            foreach ($booksList as $b): 
                            ?>
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= $no++ ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($b['isbn']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($b['title']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($b['author']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($b['category_id']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($b['publisher']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($b['publish_date']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <?php if ($b['cover']): ?>
                                            <img src="<?= htmlspecialchars($b['cover']) ?>" alt="Cover" class="w-full object-fill">
                                        <?php else: ?>
                                            No Cover
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($b['available_copies']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <a href="./edit_book.php?book_id=<?= $b['book_id'] ?>" class="bg-yellow-500 text-white px-2 py-2 rounded"><span><i class="fa-regular fa-pen-to-square mx-2"></i></span> Edit</a>
                                        <button data-book-id="<?= $b['book_id'] ?>" class="delete-book-btn bg-red-500 text-white px-2 py-2 rounded"><span><i class="fa-solid fa-trash-can mx-2" style="color: #ffffff;"></i></span> Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <hr>
                    <div id="pagination" class="mt-4 flex justify-center">
                        <?php
                        $totalPages = ceil($booksTotal / $limit);
                        for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="px-3 py-1 border rounded mx-1 <?= $page == $i ? 'bg-blue-500 text-white' : 'bg-gray-200' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
