-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2025 at 09:15 AM
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
-- Database: `tripplanner`
--

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `paid_by` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `split_amount` decimal(10,2) NOT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `group_id`, `paid_by`, `amount`, `description`, `split_amount`, `date`, `created_at`) VALUES
(1, 17, 'admin2', 33.00, 'xyz', 11.00, NULL, '2025-09-20 07:36:28'),
(2, 17, 'admin3', 999.00, 'fsdgewrag', 333.00, NULL, '2025-09-20 07:37:56'),
(3, 18, 'admin6', 444.00, 'fgug', 111.00, NULL, '2025-09-20 07:40:57'),
(4, 18, 'admin7', 444.00, 'jbloj', 111.00, NULL, '2025-09-20 07:41:42'),
(5, 18, 'admin2', 444.00, 'ghcugfc', 111.00, NULL, '2025-09-20 07:42:23'),
(6, 19, 'sohanchaudhuri', 1500.00, 'dhgakugaeu', 1500.00, NULL, '2025-09-20 09:46:10'),
(7, 21, 'admin', 1000.00, 'nbn', 1000.00, '2025-09-20', '2025-09-20 09:53:04'),
(8, 22, 'admin', 484.00, 'fewr', 242.00, '2025-09-20', '2025-09-20 10:03:07'),
(9, 22, 'admin1', 998989.00, 'fwfwf', 499494.50, '2025-09-20', '2025-09-20 10:03:19'),
(10, 23, 'sohanchaudhuri', 1000.00, 'tiffin', 500.00, '2025-09-20', '2025-09-20 10:18:04'),
(11, 23, 'admin6', 1000.00, 'sudhe dilm', 500.00, '2025-09-20', '2025-09-20 10:19:13'),
(12, 23, 'sohanchaudhuri', 1200.00, 'khabar', 600.00, '2025-09-20', '2025-09-20 10:19:46'),
(13, 26, 'dipu', 1000.00, 'gdgv', 333.33, '2025-09-20', '2025-09-20 12:25:33'),
(14, 26, 'dipu', 1000.00, 'food', 333.33, '2025-09-20', '2025-09-20 12:30:53'),
(15, 26, 'sohan', 500.00, 'travel', 166.67, '2025-09-20', '2025-09-20 12:31:38'),
(16, 26, 'sohan', 666.00, 'return', 222.00, '2025-09-22', '2025-09-21 23:55:47');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `creator` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `country` varchar(100) DEFAULT NULL,
  `currency` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `creator`, `created_at`, `country`, `currency`) VALUES
(17, 'triptoindia', 'admin9', '2025-09-20 07:12:08', 'Afghanistan', 'AFN'),
(18, 'test4', 'admin2', '2025-09-20 07:39:12', 'Algeria', 'DZD'),
(19, 'CodeBytes', 'sohanchaudhuri', '2025-09-20 08:20:03', 'India', 'INR'),
(20, 'CodeBytes', 'sohanchaudhuri', '2025-09-20 08:23:46', 'Afghanistan', 'AFN'),
(21, 'testttt', 'admin', '2025-09-20 09:50:59', 'United States', 'USD'),
(22, 'CodeBytes', 'admin', '2025-09-20 10:02:07', 'Czech Republic', 'CZK'),
(23, 'ABCDE', 'sohanchaudhuri', '2025-09-20 10:16:20', 'Dominica', 'XCD'),
(24, 'sdafasd', 'sohanchaudhuri', '2025-09-20 11:36:56', 'Albania', 'ALL'),
(25, 'ABCDE', 'admin', '2025-09-20 11:42:48', 'Andorra', 'EUR'),
(26, 'final test', 'dipu', '2025-09-20 12:01:57', 'United States', 'USD');

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'invited'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`id`, `group_id`, `user_name`, `email`, `full_name`, `status`) VALUES
(12, 17, 'admin9', NULL, NULL, 'accepted'),
(13, 17, 'admin2', NULL, NULL, 'accepted'),
(14, 17, 'admin3', NULL, NULL, 'invited'),
(15, 18, 'admin2', NULL, NULL, 'accepted'),
(16, 18, 'admin', NULL, NULL, 'accepted'),
(17, 18, 'admin6', NULL, NULL, 'accepted'),
(18, 18, 'admin7', NULL, NULL, 'accepted'),
(19, 19, 'sohanchaudhuri', 'sohanchaudhuri@gmail.com', 'Sohan Chaudhuri', 'accepted'),
(20, 20, 'sohanchaudhuri', 'sohanchaudhuri@gmail.com', 'Sohan Chaudhuri', 'accepted'),
(21, 20, 'admin7', NULL, NULL, 'invited'),
(22, 20, 'admin3', NULL, NULL, 'invited'),
(23, 21, 'admin', NULL, NULL, 'accepted'),
(24, 22, 'admin', NULL, NULL, 'accepted'),
(25, 22, 'admin1', NULL, NULL, 'invited'),
(26, 23, 'sohanchaudhuri', 'sohanchaudhuri@gmail.com', 'Sohan Chaudhuri', 'accepted'),
(27, 23, 'admin6', NULL, NULL, 'accepted'),
(28, 24, 'sohanchaudhuri', 'sohanchaudhuri@gmail.com', 'Sohan Chaudhuri', 'invited'),
(29, 24, 'admin', NULL, NULL, 'accepted'),
(30, 25, 'admin', NULL, NULL, 'accepted'),
(31, 25, 'admin1', NULL, NULL, 'invited'),
(32, 26, 'dipu', 'mango@gmail.com', 'DIPAYAN SAHA', 'accepted'),
(33, 26, 'sohan', 'sohanchaudhuri@gmail.xom', 'Sohan Chaudhuri', 'accepted'),
(34, 26, 'arijit', 'student.arijit@gmail.com', 'Arijit', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `password`, `country`, `currency`, `email`, `full_name`) VALUES
(3, 'admin', '$2y$10$J1./hKXniMibQ8bE.xIm4uMTui8RpkPJ0NJO0XcMojhCq/gUyFjdi', NULL, NULL, NULL, NULL),
(4, 'admin1', '$2y$10$4btckcqsBMNCIGsPztJ9fuz8VmtEr3E6B9fSalzp7ZMFQee9Ra1k.', NULL, NULL, NULL, NULL),
(5, 'admin2', '$2y$10$cDZ1Te4B2E5wzQTMqRyl6uvtfFe5mMWKH3N.ObpqjLoJSv85iBzE6', NULL, 'INR', NULL, NULL),
(6, 'admin3', '$2y$10$O0mDvoKPAYaAgY4gIMpu9uhvaH8HSljJDZSHNX/1k6MsXy6pcN9Wq', NULL, NULL, NULL, NULL),
(7, 'admin4', '$2y$10$mzcr0UNyTec3V.6DRlXVweQ0vIuAzlvv4w9ydDb8hObSJlhV6wcle', NULL, NULL, NULL, NULL),
(8, 'admin5', '$2y$10$bMnKYu6QTzvulImZZtvo/.ZCRR4lNTxVKciwsaYvhr5d9qJfH1p4G', NULL, NULL, NULL, NULL),
(9, 'admin6', '$2y$10$HnzOoLIIA5cgnMDk4Qgj8.19eKUg.03LACx3tFEy8Odj303R358I2', NULL, NULL, NULL, NULL),
(10, 'admin7', '$2y$10$IG5mMjOWvBFQHDTTy6NVd.NORpTr9/IpygEcH75dtPt44i4h.X/Ja', NULL, NULL, NULL, NULL),
(11, 'admin8', '$2y$10$aDyAy8dtrx/m6gXzOaQ8w.LP/59KFMmz1.DSCritJA8vtdDvSrmLy', NULL, NULL, NULL, NULL),
(12, 'admin9', '$2y$10$nMD7sc2SEqBp1cyeHK29oulaNQVTpbPMKySx4juTYm7AyWtrwfSki', NULL, NULL, NULL, NULL),
(13, 'sohanchaudhuri', '$2y$10$7t9.QuO1bfwdRY3.F75ti.reFS/gC.p/1BOb6bMqhfSxbSK6kgbVe', 'India', 'INR', 'sohanchaudhuri@gmail.com', 'Sohan Chaudhuri'),
(14, 'dipu', '$2y$10$H875c0q7E1euC0sRUMDR8eUwg3JlAGpUAvpgO/fGt/NgzaH/4lsLe', 'India', 'INR', 'mango@gmail.com', 'DIPAYAN SAHA'),
(15, 'sohan', '$2y$10$SEYcMZT2V8HaTCjLd9wk.eHGayAhb6NqBipEgyOw05Z48mf4VRiyy', 'Germany', 'EUR', 'sohanchaudhuri@gmail.xom', 'Sohan Chaudhuri'),
(16, 'arijit', '$2y$10$fbFtoTjC0nfmnU3Z1v/Ir.xnopw7oBFagaJyyHc8YO873EarF6FUi', 'Chad', 'XAF', 'student.arijit@gmail.com', 'Arijit'),
(17, 'DEBADRITA', '$2y$10$M7A7F6W4iiMvwKD9FfajIuhW089AsUwbyBjQNsANYsFSziZa1cwJu', 'India', 'INR', 'student.arijit@gmail.com', 'DEBADRITA GHOSAL');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator` (`creator`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`user_name`) ON DELETE CASCADE;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
