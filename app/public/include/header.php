<?php
// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Fetch categories from the database
$query = "SELECT DISTINCT category_name FROM categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user data if logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT photo FROM users WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!-- Header -->
<div class="bg-transparent sticky top-0 z-50">
    <header class="text-bg_color p-2 text-sm bg-primary-10 backdrop-blur bg-opacity-60">
        <div class="container mx-auto flex justify-between mt-5">
            <div class="flex">
                <nav>
                    <ul class="flex space-x-10">
                        <li>
                            <a href="<?php echo isset($_SESSION['user_id']) ? '../users/home_user.php' : '../index.php'; ?>" class="transition-all duration-200 hover:rounded-lg hover:text-black hover:bg-secondary_bg hover:px-4 hover:py-1">Home</a>
                        </li>
                        <li class="relative">
                            <a href="../book/all_book.php" id="kategoriMenu" class="flex items-center transition-all duration-200 hover:px-4 hover:py-1">
                                Kategori
                                <span class="my-auto"><img src="../assets/arrow-down.svg" alt=""></span>
                            </a>
                            <div class="absolute hidden bg-white shadow-lg rounded mt-2 w-48" id="kategoriDropdown">
                                <?php foreach ($categories as $category): ?>
                                    <a href="../book/all_book.php?category=<?= urlencode($category['category_name']) ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"><?= htmlspecialchars($category['category_name']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </li>
                        <li><a href="<?php echo isset($_SESSION['user_id']) ? '../users/home_user.php#keuntungan' : '../index.php#keuntungan'; ?>" class="transition-all duration-200 hover:rounded-lg hover:text-black hover:bg-secondary_bg hover:px-4 hover:py-1">Keuntungan</a></li>
                        <li><a href="<?php echo isset($_SESSION['user_id']) ? '../users/home_user.php#testimoni' : '../index.php#testimoni'; ?>" class="transition-all duration-200 hover:rounded-lg hover:text-black hover:bg-secondary_bg hover:px-4 hover:py-1">Testimoni</a></li>
                    </ul>
                </nav>
            </div>
            <div class="flex items-center space-x-5">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'anggota'): ?>
                    <!-- Notification -->
                    <div class="relative">
                        <a href="#" id="notificationIcon" class="relative">
                            <img src="../assets/bell.svg" alt="Notifications">
                            <span id="notificationCount" class="hidden absolute top-0 right-0 bg-red-500 text-white rounded-full text-xs w-5 h-5 items-center justify-center">0</span>
                        </a>
                        <div id="notificationDropdown" class="absolute right-0 mt-2 w-64 bg-white border rounded shadow-lg hidden">
                            <div id="notificationList" class="p-4">
                                <!-- Notifications will be inserted here -->
                            </div>
                        </div>
                    </div>
                    <!-- Profile menu -->
                    <div class="relative">
                        <button class="flex items-center space-x-2" id="userMenuButton">
                            <?php if ($user && !empty($user['photo'])): ?>
                                <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Profile" class="w-8 h-8 rounded-full bg-bg_color p-0.5">
                            <?php else: ?>
                                <img src="../assets/user-circle.svg" alt="Profile">
                            <?php endif; ?>
                        </button>
                        <div class="font-normal absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg hidden" id="userMenuDropdown">
                            <a href="../users/profile.php" class="flex justify-start px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <span class="px-2"><img src="../assets/settings.svg" alt=""></span>Profil saya
                            </a>
                            <a href="../users/rented_books.php" class="flex justify-start px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <span class="px-2"><img src="../assets/books.svg" alt=""></span>Buku dipinjam
                            </a>
                            <a href="../book/favorite_book.php" class="flex justify-start px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <span class="px-2"><img src="../assets/bookmarks.svg" alt=""></span>Buku favorit
                            </a>
                            <hr>
                            <a href="../auth/logout.php" class="flex justify-start px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <span class="px-2"><img src="../assets/logout.svg" alt=""></span>Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../auth/login.php" class="bg-secondary_bg px-8 py-2 space-x-2 rounded-md flex transition-all duration-200 hover:scale-95">
                        <img src="../assets/login.svg" alt="">
                        <h2 class="text-p_font">Login</h2>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userMenuButton = document.getElementById('userMenuButton');
    const userMenuDropdown = document.getElementById('userMenuDropdown');
    const kategoriMenu = document.getElementById('kategoriMenu');
    const kategoriDropdown = document.getElementById('kategoriDropdown');
    const notificationCountElem = document.getElementById('notificationCount');
    const notificationListElem = document.getElementById('notificationList');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationIcon = document.getElementById('notificationIcon');

    // Function to show and hide dropdown menus
    function showDropdown(menu, dropdown) {
        menu.addEventListener('mouseover', function() {
            dropdown.classList.remove('hidden');
        });
        dropdown.addEventListener('mouseover', function() {
            this.classList.remove('hidden');
        });
        dropdown.addEventListener('mouseout', function() {
            this.classList.add('hidden');
        });
    }

    // Add event listeners for user and category menus
    if (userMenuButton && userMenuDropdown) {
        showDropdown(userMenuButton, userMenuDropdown);
    }

    if (kategoriMenu && kategoriDropdown) {
        showDropdown(kategoriMenu, kategoriDropdown);
    }

    if (notificationIcon && notificationDropdown) {
        showDropdown(notificationIcon, notificationDropdown);
    }

    // Function to fetch notifications
    function fetchNotifications() {
        fetch('../users/notification.php')
            .then(response => response.json())
            .then(data => {
                const notifications = data.notifications;
                notificationListElem.innerHTML = '';

                if (notifications.length > 0) {
                    notificationCountElem.textContent = notifications.length;
                    notificationCountElem.classList.remove('hidden');
                    notifications.forEach(notification => {
                        const notifElem = document.createElement('div');
                        notifElem.className = 'notification-item';
                        notifElem.innerHTML = `
                            <div class="flex justify-between items-center">
                                <div class="text-gray-700">${notification.message}</div>
                                ${notification.notification_id ? `<button class="text-gray-500" onclick="markAsRead(${notification.notification_id})">&times;</button>` : ''}
                            </div>
                        `;
                        notificationListElem.appendChild(notifElem);
                    });
                } else {
                    notificationCountElem.classList.add('hidden');
                    notificationListElem.innerHTML = '<div class="text-center text-gray-500">No notifications for you</div>';
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    function markAsRead(notificationId) {
        fetch(`../mark_notification_read.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: notificationId })
        })
        .then(() => fetchNotifications())
        .catch(error => console.error('Error marking notification as read:', error));
    }

    // Toggle notification dropdown on click
    notificationIcon.addEventListener('click', function() {
        notificationDropdown.classList.toggle('hidden');
    });

    setInterval(fetchNotifications, 5000); // Fetch notification count every 5 seconds
    fetchNotifications(); // Initial fetch on page load
});
</script>

