    <?php
    include_once '../middleware/authMiddleware.php';

    // Cek apakah user sudah login dan memiliki role 'anggota'
    AuthMiddleware::checkLoggedIn();
    AuthMiddleware::checkRole('staff');

    function isActivePage($page) {
        return basename($_SERVER['PHP_SELF']) === $page ? 'bg-primary-10 bg-opacity-25 rounded-lg' : '';
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <style>
            body {
                display: flex;
                min-height: 100vh;
                margin: 0;
            }
            #sidebar {
                flex: 0 0 16rem; /* fixed width */
            }
            #content {
                flex: 1;
                padding: 20px;
                overflow: auto;
            }
        </style>
    </head>
    <body>
        <!-- Sidebar -->
        <div id="sidebar" class="bg-white h-auto shadow-md transition-all duration-300 flex flex-col">
            <div class="p-6 text-center border-b border-gray-200">
                <h1 class="font-bold text-xl">SIPERPUS</h1>
            </div>
            <nav class="px-4 py-2 flex-1">
                <a href="./home_admin.php" class="flex items-center px-4 py-2 text-p_font space-x-2 hover:bg-primary-10 hover:bg-opacity-25 hover:rounded-lg transition duration-300 <?= isActivePage('home_admin.php') ?>">
                    <i class="fa-solid fa-chart-line"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="./view_books.php" class="flex items-center px-4 py-2 text-p_font space-x-2 hover:bg-primary-10 hover:bg-opacity-25 hover:rounded-lg transition duration-300 <?= isActivePage('view_books.php') ?>">
                    <i class="fa-solid fa-book"></i>
                    <span class="sidebar-text">Manajemen Buku</span>
                </a>
                <a href="./view_user.php" class="flex items-center px-4 py-2 text-p_font space-x-2 hover:bg-primary-10 hover:bg-opacity-25 hover:rounded-lg transition duration-300 <?= isActivePage('view_user.php') ?>">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Manajemen Anggota</span>
                </a>
                <a href="./data_peminjaman.php" class="flex items-center px-4 py-2 text-p_font space-x-2 hover:bg-primary-10 hover:bg-opacity-25 hover:rounded-lg transition duration-300 <?= isActivePage('data_peminjaman.php') ?>">
                    <i class="fas fa-book-reader"></i>
                    <span class="sidebar-text">Data Peminjaman</span>
                </a>
                <a href="./data_denda.php" class="flex items-center px-4 py-2 text-p_font space-x-2 hover:bg-primary-10 hover:bg-opacity-25 hover:rounded-lg transition duration-300 <?= isActivePage('data_denda.php') ?>">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="sidebar-text">Data Denda</span>
                </a>
                <hr class="my-2">
                <a href="../auth/logout.php" class="flex items-center px-4 py-2 text-p_font space-x-2 hover:bg-primary-10 hover:bg-opacity-25 hover:rounded-lg transition duration-300">
                    <i class="fa fa-sign-out" aria-hidden="true"></i>
                    <span class="sidebar-text">Logout</span>
                </a>
            </nav>
        </div>
    </body>
    </html>
