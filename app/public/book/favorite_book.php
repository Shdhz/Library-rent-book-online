<?php
session_start();
include_once '../include/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to login to see your favorite books.";
    exit;
}

$user_id = $_SESSION['user_id'];

$database = new Database();
$db = $database->getConnection();

$query = "SELECT b.book_id, b.title, b.author, b.cover, b.available_copies 
          FROM favorites f 
          JOIN books b ON f.book_id = b.book_id 
          WHERE f.user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);

$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Books</title>
    <link href="../css/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include "../include/header.php" ?>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Favorite Books</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($favorites as $book): ?>
                <div class="relative bg-white p-4 shadow-md rounded-lg">
                    <img src="/admin/<?= htmlspecialchars($book['cover']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class=" w-full h-96 object-cover rounded-md">
                    <div class="mt-4">
                        <h2 class="text-lg font-semibold"><?= htmlspecialchars($book['title']) ?></h2>
                        <p class="text-gray-600"><?= htmlspecialchars($book['author']) ?></p>
                    </div>
                    <?php if ($book['available_copies'] > 0): ?>
                        <span class="absolute top-4 left-4 bg-green-500 text-white text-sm px-2 py-1 rounded-br-xl">Available</span>
                    <?php else: ?>
                        <span class="absolute top-4 left-4 bg-red-500 text-white text-sm px-2 py-1 rounded-br-xl">Not Available</span>
                    <?php endif; ?>
                    <button class="absolute top-4 right-4 text-red-500">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include "../include/footer.php" ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
