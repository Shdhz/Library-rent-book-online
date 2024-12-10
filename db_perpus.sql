-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 02, 2024 at 08:22 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_perpus`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `category_id` int DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `available_copies` int DEFAULT '0',
  `cover` varchar(255) NOT NULL,
  `publisher` varchar(255) NOT NULL,
  `sinopsis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `category_id`, `isbn`, `publish_date`, `available_copies`, `cover`, `publisher`, `sinopsis`) VALUES
(1, 'Melangkah', 'J.S. Khairen', 1, '9786020523316', '2020-05-22', 0, './uploads/ID_KAA2018MTH04.jpg', 'Gramedia', 'Novel karya J. S Khairen yang berjudul Melangkah bertemakan tentang petualangan di Indonesia. Tidak hanya itu, cerita dalam novel ini juga mengutamakan kisah pahlawan. Berbeda dari karya-karya yang sebelumnya, di novel ini Khairen memberi sedikit imajinasi yang ia tanamkan. Terdapat 36 episode dan 5 babak.'),
(4, 'bruhh', 'saha we', 1, '1872872181787', '2024-05-24', 8, './uploads/sample_book1.png', 'ujangg', 'apa deh'),
(5, 'Kanojo Okarishimasu', 'REIJI MIYAJIMA', 7, '9786230058226', '2024-05-25', 0, './uploads/sample4.jpg', 'Elex Media Komputindo', 'Kanojo Okarishimasu merupakan manga yang ditulis dan diilustrasikan oleh Reiji Miyajima dan diterbitkan pertama kali di majalah weekly shonen pada Juli 2017. Manga ini dibeli lisensinya oleh Yen Press pada 2017 dan diterbitkan secara digital pada Juli 2019. Manga ini sudah diadaptasi menjadi anime yang rilis pada Juli sampai September 2020. Kanojo Okarishimasu mengisahkan tentang Kinoshita Kazuya, lelaki yang dicampakkan pacarnya dengan lebih memilih lelaki lain. Kazuya yang tidak mau terus larut dalam kesedihan memutuskan untuk menggunakan aplikasi Diamond dan menyewa perempuan, Mizuhara Chizuru, untuk dijadikan pacar sewaan agar perasaannya lebih baik dari sebelumnya.'),
(7, 'apa ya', 'adi', 1, '921281289198', '2024-05-15', 0, './uploads/sample5.jpg', 'gramedia', 'apa ya');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(10, 'Desain'),
(3, 'Fiksi dewasa'),
(4, 'Fotografi'),
(1, 'komputer'),
(2, 'medis'),
(8, 'Psikologi'),
(5, 'Python'),
(6, 'Sains'),
(9, 'Sejarah'),
(7, 'Seni');

-- --------------------------------------------------------

--
-- Table structure for table `denda`
--

CREATE TABLE `denda` (
  `denda_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `loan_id` int DEFAULT NULL,
  `jumlah_denda` decimal(10,2) DEFAULT NULL,
  `tanggal_denda` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `book_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `user_id`, `book_id`) VALUES
(1, 35, 4),
(2, 35, 1),
(3, 35, 5),
(4, 37, 5),
(5, 38, 5);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `loan_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `loan_date` datetime NOT NULL,
  `return_date` datetime DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan','telat') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'dipinjam',
  `fine` int DEFAULT '0',
  `extended` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0',
  `loan_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('anggota','staff') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'anggota',
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `no_hp` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `verification_code` varchar(225) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `nama_lengkap`, `email`, `username`, `password`, `role`, `photo`, `no_hp`, `verification_code`, `verified`, `created_at`) VALUES
(2, 'staff', 'staff@gmail.com', 'staff', '$2y$10$WYfFtMVc68QbxkWeYE8Av.86PDcupPZ3q4SMiRgBTOB6xmEt0CUke', 'staff', './uploads/15anis baswedan.jpeg', '087737709694', NULL, 0, '2024-05-26 02:13:54'),
(4, 'abdul', 'abdul@gmail.com', 'abdul', '$2y$10$y4YIDnyL20IApmQ2MEx4QOzHDfFykTQRQ6vih1bFohlhoNyszpUqi', 'anggota', './uploads/bikbak-removebg-preview.png', '087737655345', NULL, 0, '2024-05-26 02:13:54'),
(5, 'yuda', 'yuda@gmail.com', 'yuda', '$2y$10$suMhg4lnhOZHJ1moNKluGeKJ0z4heZB1N44R//BSdTfp5ojqINSGm', 'staff', './uploads/WhatsApp Image 2024-04-13 at 9.57.29 PM.jpeg', '', NULL, 0, '2024-05-26 02:13:54'),
(6, 'vincent', 'vinc@gmail.com', 'vinc', '$2y$10$0n8e7bIT5nPbJRIqVTD6PujAIFRgGCvekB1lYuEFbXBgJjq6.RVRe', 'anggota', './uploads/247e7efe5fb9873626930b40bb6a4520---bit-pixel-art.jpg', '', NULL, 0, '2024-05-26 02:13:54'),
(8, 'staff2', 'staff2@gmail.com', 'staff2', '$2y$10$0bPPtuFr75cOC/mNJeY3EOs.tC/410Nw1f.W4BZgUpn6cwOFsXur2', 'staff', './uploads/abror.png', '087765432312', NULL, 0, '2024-05-26 02:13:54'),
(33, 'dhafa alfareza', 'dhafaalfrz13@gmail.com', 'dhafa08', '$2y$10$zm1e0Sj80tbXwjcmda015Op1mjZJv0q5evcQRxNTAaMuYanVvQ3Bm', 'anggota', '../admin/uploads/pp.jpg', '', NULL, 1, '2024-05-26 05:00:25'),
(35, 'johntor', 'johntor12@gmail.com', 'john', '$2y$10$b56fYKHS1NSnbSPqSJzE4.aavz.K6neFCDA6j2y6qq/cnev/gzEXu', 'anggota', '../admin/uploads/pp.jpg', '', NULL, 1, '2024-05-26 09:52:17'),
(36, 'tes doang', '19230798@bsi.ac.id', 'jiraiya', '$2y$10$XbZ231PTA7kg7/nIRqCkN.CpV7bjFkpjAtYWPbfKHStXRTP5/72ey', 'anggota', '', '', '9a8f67c7f581574a72195f03e55a7f2a', 0, '2024-05-26 13:41:19'),
(37, 'adi', 'ipinlegend08@gmail.com', 'adi', '$2y$10$UWD4wEZIsW.uurXDdfNrte0QuUs/WYVj62kLJGTG1AtiRgmmv.z3i', 'anggota', '../admin/uploads/pp.jpg', '', NULL, 1, '2024-05-27 07:26:29'),
(38, 'adi abdul jabar', 'zombiephoenix42@gmail.com', 'benten', '$2y$10$4voO.HmJvMj9sN4C/vJH7e.VT2lYx64Ax6bl9cZC2jEPLoHLaBKim', 'anggota', '../admin/uploads/ppKucing.jpg', '', NULL, 1, '2024-05-28 04:41:05'),
(39, 'bebas', 'bebas@gmail.com', 'bebas', '$2y$10$fGk326lTsQ5nnpKZmG7uxeex5cofeQ5lNphTuobShT1bHsa.X1T6i', 'anggota', './uploads/pp.jpg', '087765435324', NULL, 0, '2024-05-28 04:50:37'),
(40, 'andri', 'sicucut43@gmail.com', 'andri', '$2y$10$3ygK92yhcxC.etMFzZMNue6MR.rXe2rJW.fXMeXF1w3boHzULf3IS', 'anggota', '../admin/uploads/ppKucing.jpg', '', NULL, 1, '2024-06-02 03:26:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `denda`
--
ALTER TABLE `denda`
  ADD PRIMARY KEY (`denda_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_loan_id` (`loan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `denda`
--
ALTER TABLE `denda`
  MODIFY `denda_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `denda`
--
ALTER TABLE `denda`
  ADD CONSTRAINT `denda_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `denda_ibfk_2` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loan_id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_loan_id` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
