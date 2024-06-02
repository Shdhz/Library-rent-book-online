<?php
// Mendapatkan nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="container mx-auto px-4 py-8 w-1/2">
    <div class="bg-white rounded-lg overflow-x-auto p-6 shadow-md">
        <h1 class="text-xl font-bold mb-4">Profile</h1>
        <hr>
        <div class="flex items-center mt-8">
            <div class="foto">
                <img src="<?= htmlspecialchars($photo) ?>" alt="Profile Photo" class="mr-4 w-16 h-auto rounded-full mb-4">
            </div>
            <div class="">
                <h3 class="text-lg font-semibold">halo</h3>
                <h3 class="text-sm text-p_font"><?= $username ?></h3>
            </div>
        </div>
        <h1 class="text-bg_color text-center px-2 py-2 w-full mx-auto mt-5 rounded-lg" style="background-color: <?= ($current_page == 'profile.php') ? '#1e40af' : '#e5e7eb'; ?>; color: <?= ($current_page == 'profile.php') ? 'white' : 'black'; ?>;">
            <a href="./profile.php" style="color: inherit;">Profil Saya</a>
        </h1>
        <h1 class="text-center px-2 py-2 w-full mx-auto mt-5 rounded-lg" style="background-color: <?= ($current_page == 'edit_acc.php') ? '#1e40af' : '#e5e7eb'; ?>; color: <?= ($current_page == 'edit_acc.php') ? 'white' : 'black'; ?>;">
            <a href="./edit_acc.php" style="color: inherit;">Akun saya</a>
        </h1>
    </div>
</div>
