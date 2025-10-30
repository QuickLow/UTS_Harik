-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2025 at 03:46 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uts`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `price` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `created_at`, `updated_at`) VALUES
(2, 'Pulpen Standar', 'Pulpen', 3500, '2025-10-30 02:10:08', '2025-10-30 02:12:21'),
(3, 'Kabel UTP', '-', 6500, '2025-10-30 02:44:36', '2025-10-30 02:44:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin_gudang') NOT NULL DEFAULT 'admin_gudang',
  `status` int(1) NOT NULL DEFAULT 0,
  `activation_token` varchar(64) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `activation_token`, `reset_token`, `reset_token_expiry`, `created_at`) VALUES
(2, 'yah', 'harik12@gmail.com', '$2y$10$KoQE5fu.mxiOVFnsWtIHMe6S/KJw171Rs3DgvbiD/5FQ/lNRJvVUS', 'admin_gudang', 1, NULL, NULL, NULL, '2025-10-30 01:50:00'),
(3, 'ray', 'rik12@gmail.com', '$2y$10$6NS6Qa1pPCNazxMBtT98ieMPQo8jOQ/yTPeF6yv3UIqe2FkPsO9xy', 'admin_gudang', 1, NULL, '5a084bb6024626dbda1b860a6113cc13bd1d6d74ace660b6d878dfb63abe3a7c', '2025-10-30 04:35:05', '2025-10-30 02:27:16'),
(4, 'arab', 'yb12@gmail.com', '$2y$10$zrvStGddJr4n4ogsTpdrJ.bjQfOWNKiZMZ9C5CPtq280Dx1QnPWBe', 'admin_gudang', 1, NULL, NULL, NULL, '2025-10-30 02:36:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
