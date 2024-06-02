<?php
include_once '../middleware/authMiddleware.php';

// Cek apakah user sudah login dan memiliki role 'staff'
AuthMiddleware::checkLoggedIn();
AuthMiddleware::checkRole('staff');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="../css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <?php include "./sidebar_admin.php" ?>
        <!-- Main Content -->
        <div class="flex flex-col p-10 w-full">
            <div class="flex justify-between mb-6">
                <h1 class="text-2xl font-bold">Staff Dashboard</h1>
            </div>
            <!-- Main content -->
            <div class="flex bg-white shadow-lg rounded-lg px-2 py-4">
                <div class="w-full md:w-1/3 p-3">
                    <canvas id="booksChart"></canvas>
                </div>
                <div class="w-full md:w-1/3 p-3">
                    <canvas id="borrowedBooksChart"></canvas>
                </div>
                <div class="w-full md:w-1/3 p-3">
                    <canvas id="membersChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('./data.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Data fetched:', data); // Debugging: Log data yang diterima

                    if (data.error) {
                        console.error('Error fetching data:', data.error);
                        return;
                    }

                    const ctxBooks = document.getElementById('booksChart').getContext('2d');
                    const ctxBorrowedBooks = document.getElementById('borrowedBooksChart').getContext('2d');
                    const ctxMembers = document.getElementById('membersChart').getContext('2d');

                    const booksChart = new Chart(ctxBooks, {
                        type: 'bar',
                        data: {
                            labels: ['Total Buku'],
                            datasets: [{
                                label: 'Buku',
                                data: [data.total_books],
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    const borrowedBooksChart = new Chart(ctxBorrowedBooks, {
                        type: 'bar',
                        data: {
                            labels: ['Buku yang dipinjam'],
                            datasets: [{
                                label: 'Buku',
                                data: [data.borrowed_books],
                                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    const membersChart = new Chart(ctxMembers, {
                        type: 'bar',
                        data: {
                            labels: ['Total Pengguna'],
                            datasets: [{
                                label: 'Pengguna',
                                data: [data.total_members],
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    </script>
</body>
</html>
