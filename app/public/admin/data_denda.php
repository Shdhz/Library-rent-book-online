<?php
include_once '../middleware/authMiddleware.php';
include_once '../include/database.php';

$database = new Database();
$db = $database->getConnection();

// Cek apakah user sudah login dan memiliki role 'staff'
AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('staff');

// Tentukan limit untuk pagination
$limit = 10; // Misalnya 10 data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Fetch loan data along with book and user details and denda details
$query = "
    SELECT l.loan_id, l.loan_date, l.return_date, l.status, 
           b.title AS book_title,
           u.nama_lengkap AS user_name, u.email,
           d.jumlah_denda, d.tanggal_denda
    FROM loans l
    JOIN books b ON l.book_id = b.book_id
    JOIN users u ON l.user_id = u.user_id
    LEFT JOIN denda d ON l.loan_id = d.loan_id
    WHERE u.nama_lengkap LIKE :search OR b.title LIKE :search
    LIMIT :limit OFFSET :offset
";
$stmt = $db->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':search', $search, PDO::PARAM_STR);
$stmt->execute();
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total data untuk pagination
$queryTotal = "
    SELECT COUNT(*) AS total
    FROM loans l
    JOIN books b ON l.book_id = b.book_id
    JOIN users u ON l.user_id = u.user_id
    LEFT JOIN denda d ON l.loan_id = d.loan_id
    WHERE u.nama_lengkap LIKE :search OR b.title LIKE :search
";
$stmtTotal = $db->prepare($queryTotal);
$stmtTotal->bindValue(':search', $search, PDO::PARAM_STR);
$stmtTotal->execute();
$totalData = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

if (isset($_GET['ajax'])) {
    echo json_encode(['loans' => $loans, 'totalData' => $totalData]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="../css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-area, #printable-area * {
                visibility: visible;
            }
            #printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex flex-auto">
        <?php include "./sidebar_admin.php"; ?>
        <!-- head Content -->
        <div class="flex-1 p-10">
            <!-- main content -->
            <div class="container">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold">Data Denda</h1>
                </div>
                <div class="bg-white px-4 py-5 rounded shadow-md mb-5 flex justify-between items-center">
                    <input type="search" id="search" placeholder="Search..." class="border-2 rounded p-2 mr-4">
                    <button onclick="printData()" class="bg-primary-10 text-white px-4 py-2 rounded hover:bg-p_second">
                        <i class="fa fa-print mr-2"></i> Print Data
                    </button>
                </div>
                <div id="printable-area" class="bg-white p-6 rounded shadow-md overflow-x-auto">
                    <table id="loanTable" class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead>
                            <tr>
                                <th class="w-1/12 px-4 py-2 bg-gray-50">No</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Nama Peminjam</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Email Peminjam</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Judul Buku</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Tanggal Pinjam</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Tanggal Kembali</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Jumlah Denda</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Tanggal Denda</th>
                                <th class="w-1/6 px-4 py-2 bg-gray-50">Status</th>
                            </tr>
                        </thead>
                        <tbody id="loanTableBody" class="bg-white divide-y divide-gray-200">
                            <?php 
                            $no = $offset + 1;
                            foreach ($loans as $loan): 
                            ?>
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= $no++ ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($loan['user_name']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($loan['email']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($loan['book_title']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($loan['loan_date']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($loan['return_date']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($loan['jumlah_denda']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($loan['tanggal_denda']) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($loan['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <hr>
                    <div id="pagination" class="mt-4 flex justify-center">
                        <?php
                        $totalPages = ceil($totalData / $limit);
                        for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="px-3 py-1 border rounded mx-1 <?= $page == $i ? 'bg-blue-500 text-white' : 'bg-gray-200' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printData() {
            window.print();
        }

        $(document).ready(function() {
            $('#search').on('input', function() {
                let searchValue = $(this).val();
                $.ajax({
                    url: '?ajax=1&search=' + searchValue + '&page=<?= $page ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        let loans = response.loans;
                        let totalData = response.totalData;
                        let tbody = '';
                        let no = <?= $offset ?> + 1;
                        $.each(loans, function(index, loan) {
                            tbody += `<tr>
                                <td class="px-4 py-2 whitespace-nowrap">${no++}</td>
                                <td class="px-4 py-2 whitespace-nowrap">${loan.user_name}</td>
                                <td class="px-4 py-2 whitespace-nowrap">${loan.email}</td>
                                <td class="px-4 py-2 whitespace-nowrap">${loan.book_title}</td>
                                <td class="px-4 py-2 whitespace-nowrap">${loan.loan_date}</td>
                                <td class="px-4 py-2 whitespace-nowrap">${loan.return_date}</td>
                                <td class="px-4 py-2 whitespace-nowrap">${loan.jumlah_denda}</td>
                                <td class="px-4 py-2 whitespace-nowrap">${loan.tanggal_denda}</td>
                                <td class="px-4 py-2 whitespace-nowrap">${loan.status}</td>
                            </tr>`;
                        });
                        $('#loanTableBody').html(tbody);
                        let totalPages = Math.ceil(totalData / <?= $limit ?>);
                        let pagination = '';
                        for (let i = 1; i <= totalPages; i++) {
                            pagination += `<a href="?page=${i}" class="px-3 py-1 border rounded mx-1 ${<?= $page ?> == i ? 'bg-blue-500 text-white' : 'bg-gray-200'}">${i}</a>`;
                        }
                        $('#pagination').html(pagination);
                    }
                });
            });
        });
    </script>
</body>
</html>
