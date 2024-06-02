<?php
session_start();
include_once '../include/database.php';
include_once '../include/crud_books.php';

// Check if book_id is set and is a valid number
if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    echo "Invalid book ID.";
    exit;
}

$book_id = intval($_GET['book_id']);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$database = new Database();
$db = $database->getConnection();
$books = new Books($db);

$successMessage = "";
$errorMessage = "";

// Fetch the book details based on book_id
$bookDetails = $books->getBookDetails($book_id);

if ($bookDetails) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($user_id) {
            // Check if there are available copies
            if ($bookDetails['available_copies'] > 0) {
                // Handle loan submission
                $return_date = $_POST['return_date'];
                $loan_date = date('Y-m-d\TH:i'); // Current date and time
                
                // Ensure return_date is not earlier than loan_date
                if ($return_date > $loan_date) {
                    $query = "INSERT INTO loans (user_id, book_id, loan_date, return_date, status) VALUES (?, ?, ?, ?, 'dipinjam')";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$user_id, $book_id, $loan_date, $return_date]);

                    // Update the number of available copies
                    $updateQuery = "UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?";
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->execute([$book_id]);
                    
                    $successMessage = "Buku {$bookDetails['title']} sukses kamu pinjam!";
                    // Refresh the book details after updating the available copies
                    $bookDetails = $books->getBookDetails($book_id);
                } else {
                    $errorMessage = "Tanggal pengembalian harus lebih dari tanggal peminjaman.";
                }
            } else {
                echo "<p>Maaf, tidak ada salinan yang tersedia untuk dipinjam.</p>";
            }
        } else {
            echo "<p>Anda harus login untuk meminjam buku.</p>";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($bookDetails['title']) ?></title>
        <link href="../css/output.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            .favorite-button {
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: rgba(255, 255, 255, 0.7);
                border: none;
                border-radius: 50%;
                width: 48px;  /* Ensure width and height are the same */
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 1.5rem;
                color: #FFC100;
            }
            .favorite-button:hover {
                background-color: rgba(255, 255, 255, 1);
            }
            .floating-bar {
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #ff9800;
                color: white;
                padding: 15px;
                border-radius: 5px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                display: none;
                z-index: 1000;
            }
            .success-bar {
                background-color: #4caf50;
            }
            .error-bar {
                background-color: #f44336;
            }
        </style>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            function showLoginPrompt(){
                const loginPrompt = document.getElementById('login-prompt');
                loginPrompt.style.display = 'block';
                setTimeout(function() {
                    loginPrompt.style.display = 'none';
                }, 3000);
            }

            function showSuccessMessage() {
                const successMessage = document.getElementById('success-message');
                successMessage.style.display = 'block';
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 3000);
            }

            function showErrorMessage() {
                const errorMessage = document.getElementById('error-message');
                errorMessage.style.display = 'block';
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 3000);
            }

            $(document).ready(function() {
                $('#favorite-button').click(function() {
                    $.ajax({
                        url: './save_favorite.php',
                        type: 'POST',
                        data: {
                            book_id: <?= $book_id ?>,
                            user_id: <?= $user_id ?>
                        },
                        success: function(response) {
                            alert('Buku telah ditambahkan ke favorit.');
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat menambahkan buku ke favorit.');
                        }
                    });
                });
            });
        </script>
    </head>
    <body class="bg-gray-100" <?php if (!empty($successMessage)) echo 'onload="showSuccessMessage()"'; ?> <?php if (!empty($errorMessage)) echo 'onload="showErrorMessage()"'; ?>>
        <?php include "../include/header.php" ?>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow rounded-lg overflow-hidden p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <img src="/admin/<?= htmlspecialchars($bookDetails['cover']) ?>" alt="Book Cover" class="w-full h-auto object-cover">
                        <?php if ($user_id): ?>
                            <button id="favorite-button" class="favorite-button"><i class="fa fa-heart"></i></button>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if (!empty($successMessage)): ?>
                            <div id="success-message" class="floating-bar success-bar">
                                <p><?= htmlspecialchars($successMessage) ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($errorMessage)): ?>
                            <div id="error-message" class="floating-bar error-bar">
                                <p><?= htmlspecialchars($errorMessage) ?></p>
                            </div>
                        <?php endif; ?>
                        <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($bookDetails['title']) ?></h1>
                        <p class="text-lg text-p_font">Publisher: <?= htmlspecialchars($bookDetails['publisher']) ?></p>
                        <p class="text-lg text-p_font">Tanggal rilis: <?= htmlspecialchars($bookDetails['publish_date']) ?></p>
                        <p class="text-lg text-p_font">Pengarang: <?= htmlspecialchars($bookDetails['author']) ?></p>
                        <p class="text-lg text-p_font">Kategori: <?= htmlspecialchars($bookDetails['category_name']) ?></p>
                        <p class="text-lg text-p_font">Jumlah salinan: <?= htmlspecialchars($bookDetails['available_copies']) ?></p>
                        <p class="text-lg text-p_font">ISBN: <?= htmlspecialchars($bookDetails['isbn']) ?></p>
                        <h2 class="text-lg mt-4 font-medium">Sinopsis buku</h2>
                        <p class="mt-1 text-gray-600"><?= htmlspecialchars($bookDetails['sinopsis']) ?></p>

                        <?php if ($user_id): ?>
                            <?php if ($bookDetails['available_copies'] > 0): ?>
                                <!-- Loan form -->
                                <form method="POST" action="" class="mt-4">
                                    <label for="return_date" class="block text-sm font-medium text-gray-700">Pilih Tanggal Pengembalian:</label>
                                    <div class="flex items-center mt-2">
                                        <input type="datetime-local" id="return_date" name="return_date" class="w-1/2 rounded-md border-gray-300 shadow-sm" required>
                                        <button type="submit" class="ml-4 bg-blue-500 text-white px-4 py-2 rounded-lg">Pinjam Buku</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <!-- No available copies message -->
                                <p class="mt-4 text-red-500">Tidak ada salinan buku yang tersedia untuk dipinjam saat ini.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Login prompt button -->
                            <button onclick="showLoginPrompt()" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded-lg">Pinjam Buku</button>
                            <div id="login-prompt" class="floating-bar">
                                Silakan login terlebih dahulu untuk meminjam buku.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include "../include/footer.php" ?>
    </body>
    </html>
    <?php
} else {
    echo "Book not found.";
}
?>
