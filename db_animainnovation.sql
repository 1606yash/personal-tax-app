-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2024 at 09:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_animainnovation`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) NOT NULL,
  `category_name` varchar(150) DEFAULT NULL,
  `icon` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'SLIPS', 'https://animainnovation.siplsolutions.com/assets/upload/icon/SLIPS_2024-02-08_65c4aa81996d9.png', '2024-02-08 10:18:41', '2024-02-08 10:18:41'),
(2, 'BUSINESS_EXPENSES_AND_INCOME', 'https://animainnovation.siplsolutions.com/assets/upload/icon/BUSINESS_EXPENSES_AND_INCOME_2024-02-08_65c4abe8d3351.png', '2024-02-08 10:24:40', '2024-02-08 10:24:40'),
(3, 'DONATIONS', 'https://animainnovation.siplsolutions.com/assets/upload/icon/DONATIONS_2024-02-08_65c4acd54104d.png', '2024-02-08 10:28:37', '2024-02-08 10:28:37'),
(4, 'MEDICAL', 'https://animainnovation.siplsolutions.com/assets/upload/icon/MEDICAL_2024-02-08_65c4acef1ae8c.png', '2024-02-08 10:29:03', '2024-02-08 10:29:03'),
(5, 'MISCELLANEOUS', 'https://animainnovation.siplsolutions.com/assets/upload/icon/MISCELLANEOUS_2024-02-08_65c4ad18c00b5.png', '2024-02-08 10:29:44', '2024-02-08 10:29:44');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) NOT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `document_name` varchar(150) DEFAULT NULL,
  `document_url` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `category_id`, `user_id`, `document_name`, `document_url`, `comments`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'SLIPS_2024-02-08_65c4b2d120af8.zip', 'https://animainnovation.siplsolutions.com/assets/upload/SLIPS_2024-02-08_65c4b2d120af8.zip', '', '2024-02-08 10:54:09', '2024-02-08 10:54:09'),
(2, 1, 1, 'SLIPS_2024-02-08_65c4b33e4d4fc.zip', 'https://animainnovation.siplsolutions.com/assets/upload/SLIPS_2024-02-08_65c4b33e4d4fc.zip', '', '2024-02-08 10:55:58', '2024-02-08 10:55:58'),
(3, 2, 1, 'BUSINESS_EXPENSES_AND_INCOME_2024-02-08_65c4b77c387a6.zip', 'https://animainnovation.siplsolutions.com/assets/upload/BUSINESS_EXPENSES_AND_INCOME_2024-02-08_65c4b77c387a6.zip', '', '2024-02-08 11:14:04', '2024-02-08 11:14:04'),
(4, 4, 1, 'MEDICAL_2024-02-08_65c4b90221f44.zip', 'https://animainnovation.siplsolutions.com/assets/upload/MEDICAL_2024-02-08_65c4b90221f44.zip', '', '2024-02-08 11:20:34', '2024-02-08 11:20:34'),
(5, 3, 1, 'DONATIONS_2024-02-08_65c4c6f1a7a4b.zip', 'https://animainnovation.siplsolutions.com/assets/upload/DONATIONS_2024-02-08_65c4c6f1a7a4b.zip', '', '2024-02-08 12:20:01', '2024-02-08 12:20:01'),
(6, 3, 1, 'DONATIONS_2024-02-08_65c4d98c5e27d.zip', 'https://animainnovation.siplsolutions.com/assets/upload/DONATIONS_2024-02-08_65c4d98c5e27d.zip', '', '2024-02-08 13:39:24', '2024-02-08 13:39:24'),
(7, 4, 1, 'MEDICAL_2024-02-09_65c61b4dd9b86.zip', 'https://animainnovation.siplsolutions.com/assets/upload/MEDICAL_2024-02-09_65c61b4dd9b86.zip', '', '2024-02-09 12:32:13', '2024-02-09 12:32:13');

-- --------------------------------------------------------

--
-- Table structure for table `general_comments`
--

CREATE TABLE `general_comments` (
  `id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `comment_file_name` text NOT NULL,
  `comment_file_path` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) NOT NULL,
  `menu_name` varchar(150) DEFAULT NULL,
  `menu_icon` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `menu_name`, `menu_icon`, `created_at`, `updated_at`) VALUES
(1, 'Antonio_Bobadilla', '', '2024-02-08 10:39:01', '2024-02-08 10:39:01');

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`id`, `user_id`, `token`, `created_at`, `updated_at`) VALUES
(1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE3MDczOTEwMjQsImRhdGEiOnsiZW1haWwiOiJzaHdldGFAeW9wbWFpbC5jb20iLCJwYXNzd29yZCI6MTIzNH19.wRgncL3A86JCi62P915-btl1rWiuU191BgGJnBDvRa4', '2024-02-08 10:17:04', '2024-02-08 10:17:04'),
(5, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE3MDczOTU2MzAsImRhdGEiOnsiZW1haWwiOiJzaHdldGFAeW9wbWFpbC5jb20iLCJwYXNzd29yZCI6MTIzNH19.NpSysFFLsLFXBt0VUzt1BHT-EOa1xKcR_dVIwL9GcME', '2024-02-08 11:33:50', '2024-02-08 11:33:50'),
(6, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE3MDczOTgxODksImRhdGEiOnsiZW1haWwiOiJzaHdldGFAeW9wbWFpbC5jb20iLCJwYXNzd29yZCI6MTIzNH19.2SwMBqQawam03gLUxjthE45Lr5f7Rm0OxKVj6OKvT4U', '2024-02-08 12:16:29', '2024-02-08 12:16:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `client_id` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `profile_path` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `client_id`, `first_name`, `last_name`, `profile_path`, `email`, `password`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '', 'Shweta', 'Patel', NULL, 'shweta@yopmail.com', '$2y$10$S.8Fq5HEqST7ElVRQqWb1eALAyqIc0pFO9elwjY9uZYtHu50toyxS', '2024-02-08 10:16:56', '2024-02-08 10:16:56', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_comments`
--
ALTER TABLE `general_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_users_id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `general_comments`
--
ALTER TABLE `general_comments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
