<?php
include_once './include/database.php';
include_once './include/crud_books.php';

$database = new Database();
$db = $database->getConnection();
$books = new Books($db);

// Ambil data buku
$booksList = $books->readCover()->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="./css/output.css">
    <style>
        swiper-container {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body class="bg-primary-10">
    <!-- Header include --> 
    <?php include './include/header.php'; ?>

    <div class="bg-primary-10">
        <!-- Hero section -->
        <section class="text-white p-12">
            <div class="container mx-auto flex flex-col md:flex-row">
                <div class="w-full md:w-1/2 mb-8 md:mb-0">
                    <h2 class="text-[60px] font-bold mb-4 leading-tight">Buat dirimu pintar <br> dengan membaca buku</h2>
                    <p class="text-[20px] mb-8 tracking-normal">More you learn more you feel you are dumb</p>
                    <div class="max-w-lg">
                        <form action="/book/all_book.php" method="GET">
                            <input type="text" name="search" class="w-3/5 px-3 py-3 rounded-xl text-black" placeholder="Cari ISBN, Judul, Author">
                        </form>
                    </div>
                </div>
                <div class="w-full md:w-1/2 flex  md:justify-end">
                    <img src="./assets/foto.png" alt="Reading Illustration" class="max-w-md">
                </div>
            </div>
        </section>
    </div>

    <!-- Book section -->
    <div class="bg-bg_color rounded-t-3xl mt-5">
        <div class="py-8">
            <div class="container mx-auto px-4 mb-10">
                <div class="flex justify-between items-center mb-10">
                    <h2 class="text-xl font-bold">Buku baru</h2>
                    <a href="../book/all_book.php" class="text-p_font hover:text-icon_color">Lihat selengkapnya &gt;</a>
                </div>
                <div id="books-container">
                    <?php if ($booksList && count($booksList) > 0): ?>
                        <div class="flex space-x-6 overflow-x-auto scroll-snap-x snap-mandatory">
                            <?php foreach ($booksList as $book): ?>
                                <?php
                                $coverUrl = '/admin/' . htmlspecialchars($book['cover']);
                                ?>
                                <div class="relative bg-white shadow rounded-lg overflow-hidden snap-start shrink-0 flex flex-col w-52">
                                    <a href="/book/detail_books.php?book_id=<?= $book['book_id'] ?>" class="flex flex-col items-start">
                                        <img src="<?= $coverUrl ?>" alt="Book Cover" class="w-full h-64 object-cover transition-all duration-200 hover:scale-95">
                                        <div class="p-4 flex flex-col items-start">
                                            <h3 class="text-lg font-medium text-start"><?= htmlspecialchars($book['title']) ?></h3>
                                            <p class="text-s_font text-start"><?= htmlspecialchars($book['author']) ?></p>
                                            <?php if ($book['available_copies'] > 0): ?>
                                                <span class="absolute top-4 left-4 bg-green-500 bg-opacity-60 text-white text-sm px-2 py-1 rounded-br-xl">Tersedia</span>
                                            <?php else: ?>
                                                <span class="absolute top-4 left-4 bg-red-500 bg-opacity-60 text-white text-sm px-2 py-1 rounded-br-xl">Habis</span>
                                            <?php endif; ?>
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

    <!-- Keuntungan Section -->
    <div class="bg-bg_color">
        <div class="py-8" id="keuntungan">
            <div class="container mx-auto px-4 mt-10 mb-10">
                <h2 class="text-2xl font-bold mb-10">Keuntungan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <!-- Keuntungan Item -->
                    <?php $item = array_fill(0, 8, ''); ?>
                    <?php foreach ($item as $i): ?>
                        <div class="group">
                            <div class="transition-all duration-200 hover:scale-95 group-hover:bg-primary-10 bg-bg_color rounded-lg p-6 flex flex-col items-start text-start border border-indigo-500/100">
                                <div class="mb-5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#3574F2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4 group-hover:stroke-white"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 12l3 2"/><path d="M12 7v5"/></svg>
                                </div>
                                <h3 class="text-lg font-medium group-hover:text-bg_color">Menghemat waktu</h3>
                                <p class="text-s_font">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc accumsan.</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Testimoni section -->
    <div class="bg-primary-10 mx-auto px-8 py-8">
        <h2 class="text-2xl font-bold text-bg_color px-4" id="testimoni">Testimoni</h2>
        <div class="container">
            <swiper-container class="mySwiper" pagination="true" pagination-clickable="true" navigation="false" space-between="30"
            centered-slides="false" autoplay-delay="2800" autoplay-disable-on-interaction="false" slides-per-view="3">
                <?php $item = array_fill(0, 8, ''); ?>
                <?php foreach ($item as $i): ?>
                <swiper-slide>
                    <div class="container mx-auto py-2">
                        <div class="relative overflow-hidden">
                            <div class="slider-container">
                                <div class="slider">
                                    <div class="slide p-4">
                                        <div class="bg-bg_color text-black rounded-lg p-6 shadow-lg">
                                            <img class="w-10 h-10 rounded-full mb-4 mt-2" src="./assets/client.png" alt="User">
                                            <h3 class="text-lg font-bold">John Mayer</h3>
                                            <p>Regularly reviewing privileged admin or privileged user access on a quarterly basis is crucial for maintaining security...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                </swiper-slide>
                <?php endforeach; ?>
            </swiper-container>
        </div>
    </div>
    
    <!-- Footer include -->
    <?php include 'include/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>
</body>
</html>
