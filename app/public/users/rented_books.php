<?php
include_once '../include/database.php';
include_once '../middleware/authMiddleware.php';

AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('anggota');

$user_id = $_SESSION['user_id'];

$database = new Database();
$db = $database->getConnection();

$message = "";
$error_message = "";
$perpanjang = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['return_book_id']) && isset($_POST['loan_id'])) {
        $book_id = $_POST['return_book_id'];
        $loan_id = $_POST['loan_id'];
        
        // Fetch loan details for the book being returned
        $loanQuery = "
            SELECT l.loan_id, l.return_date, l.status
            FROM loans l
            WHERE l.book_id = ? AND l.user_id = ? AND l.status = 'dipinjam' AND l.loan_id = ?
        ";
        $loanStmt = $db->prepare($loanQuery);
        $loanStmt->execute([$book_id, $user_id, $loan_id]);
        $loan = $loanStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($loan) {
            $returnDate = new DateTime($loan['return_date']);
            $now = new DateTime();
            $fine = 0;

            // Calculate fine if the return date is past due
            if ($now > $returnDate) {
                $interval = $now->diff($returnDate);
                $daysLate = $interval->days;
                $fine = $daysLate * 1000; // Calculate fine
            }

            // Update the loan status to 'dikembalikan'
            $updateQuery = "UPDATE loans SET status = 'dikembalikan', fine = ? WHERE loan_id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$fine, $loan['loan_id']]);

            // Insert fine details into the 'denda' table if there is a fine
            if ($fine > 0) {
                $insertFineQuery = "INSERT INTO denda (user_id, loan_id, jumlah_denda, tanggal_denda) VALUES (?, ?, ?, NOW())";
                $insertFineStmt = $db->prepare($insertFineQuery);
                $insertFineStmt->execute([$user_id, $loan['loan_id'], $fine]);
            }

            $message = "Buku berhasil dikembalikan!";
        }
    }

    if (isset($_POST['loan_id']) && isset($_POST['action']) && $_POST['action'] === 'extend') {
        $loan_id = $_POST['loan_id'];
        
        // Fetch loan details
        $loanQuery = "SELECT return_date, extended FROM loans WHERE loan_id = ? AND user_id = ?";
        $loanStmt = $db->prepare($loanQuery);
        $loanStmt->execute([$loan_id, $user_id]);
        $loan = $loanStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($loan) {
            if ($loan['extended']) {
                $perpanjang = "Peminjaman telah kamu perpanjang sebelumnya!";
                exit;
            }

            $newReturnDate = date('Y-m-d', strtotime($loan['return_date'] . ' + 7 days'));

            // Update the loan return date and set extended to TRUE
            $updateQuery = "UPDATE loans SET return_date = ?, extended = TRUE WHERE loan_id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$newReturnDate, $loan_id]);

            $message = "Buku berhasil diperpanjang selama 7 hari!";
        } else {
            $error_message = "Buku yang dipinjam tidak ditemukan";
        }
    }
}

// Fetch loan details for the user (only 'dipinjam' status)
$query = "
    SELECT l.loan_id, l.loan_date, l.return_date, l.status, b.book_id, b.title, b.cover, COALESCE(l.fine, 0) as fine, l.extended
    FROM loans l
    JOIN books b ON l.book_id = b.book_id
    WHERE l.user_id = ? AND l.status = 'dipinjam'
";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);

$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rented Books</title>
    <link href="../css/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include "../include/header.php" ?>
    <div class="container mx-auto px-4 py-8">
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($error_message) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($perpanjang): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($perpanjang) ?></span>
            </div>
        <?php endif; ?>
        <div class="bg-white shadow rounded-lg overflow-x-auto p-6">
            <h1 class="text-3xl font-bold mb-4">Rented Books History</h1>
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="w-1/5 px-4 py-2">Book Cover</th>
                        <th class="w-1/5 px-4 py-2">Book Title</th>
                        <th class="w-1/5 px-4 py-2">Loan Date</th>
                        <th class="w-1/5 px-4 py-2">Return Date</th>
                        <th class="w-1/5 px-4 py-2">Status</th>
                        <th class="w-1/5 px-4 py-2">Fine</th>
                        <th class="w-1/5 px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                        <?php
                        $returnDate = new DateTime($loan['return_date']);
                        $now = new DateTime();
                        $interval = $now->diff($returnDate);
                        $hoursUntilReturn = ($interval->days * 24) + $interval->h;
                        $showExtendButton = $hoursUntilReturn >= 0 && $hoursUntilReturn <= 24; // Perbaikan kondisi
                        ?>
                        <tr data-loan-id="<?= $loan['loan_id'] ?>" data-return-date="<?= htmlspecialchars($loan['return_date']) ?>" data-status="<?= htmlspecialchars($loan['status']) ?>" data-fine="<?= $loan['fine'] ?>">
                            <td class="px-4 py-2 whitespace-nowrap">
                                <?php if ($loan['cover']): ?>
                                    <img src="../admin/<?= htmlspecialchars($loan['cover']) ?>" alt="Cover" class="w-16 h-20 object-cover">
                                <?php else: ?>
                                    <div class="w-16 h-20 flex items-center justify-center bg-gray-200 text-gray-500">No Cover</div>
                                <?php endif; ?>
                            </td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($loan['title']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($loan['loan_date']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($loan['return_date']) ?></td>
                            <td class="border px-4 py-2 status"><?= htmlspecialchars($loan['status']) ?></td>
                            <td class="border px-4 py-2 fine">Rp. <?= htmlspecialchars($loan['fine']) ?></td>
                            <td class="border px-4 py-2">
                                <?php if ($loan['status'] == 'dipinjam'): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="return_book_id" value="<?= $loan['book_id'] ?>">
                                        <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Kembalikan</button>
                                    </form>
                                    <?php if (!$loan['extended'] && $showExtendButton): ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                            <input type="hidden" name="action" value="extend">
                                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded mt-2">Perpanjang</button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const returnDate = new Date(row.getAttribute('data-return-date'));
                const statusCell = row.querySelector('.status');
                const fineCell = row.querySelector('.fine');
                const currentStatus = row.getAttribute('data-status');
                const now = new Date();

                if (now > returnDate && currentStatus === 'dipinjam') {
                    const daysLate = Math.ceil((now - returnDate) / (1000 * 60 * 60 * 24));
                    const fine = daysLate * 1000;
                    statusCell.textContent = 'telat';
                    fineCell.textContent = 'Rp. ' + fine;
                    updateLoanStatus(row.getAttribute('data-loan-id'), 'telat', fine);
                }
            });
        });

        function updateLoanStatus(loanId, newStatus, fine) {
            fetch('update_rented.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ loan_id: loanId, status: newStatus, fine: fine })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to update loan status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
    <?php include "../include/footer.php" ?>
</body>
</html>
