-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 25, 2025 at 02:27 AM
-- Server version: 9.1.0
-- PHP Version: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my-faucet`
--

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

DROP TABLE IF EXISTS `claims`;
CREATE TABLE IF NOT EXISTS `claims` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `claim_amount` decimal(10,3) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claims_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `user_id`, `claim_amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 5.000, '2025-05-24 15:17:16', '2025-05-24 15:17:16', NULL),
(2, 1, 5.000, '2025-05-24 15:22:58', '2025-05-24 15:22:58', NULL),
(3, 2, 5.000, '2025-05-24 15:24:11', '2025-05-24 15:24:11', NULL),
(4, 2, 5.000, '2025-05-24 15:29:35', '2025-05-24 15:29:35', NULL),
(5, 1, 5.000, '2025-05-24 15:31:24', '2025-05-24 15:31:24', NULL),
(6, 1, 5.000, '2025-05-24 15:40:05', '2025-05-24 15:40:05', NULL),
(7, 2, 5.000, '2025-05-24 15:41:02', '2025-05-24 15:41:02', NULL),
(8, 3, 5.000, '2025-05-24 16:29:44', '2025-05-24 16:29:44', NULL),
(9, 4, 5.000, '2025-05-24 16:45:19', '2025-05-24 16:45:19', NULL),
(10, 1, 5.000, '2025-05-25 00:23:15', '2025-05-25 00:23:15', NULL),
(11, 1, 5.000, '2025-05-25 00:35:41', '2025-05-25 00:35:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_message` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `last_active` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `points` decimal(10,3) DEFAULT NULL,
  `exp` int DEFAULT '0',
  `level` int DEFAULT '0',
  `referred_by` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `users_referred_by_foreign` (`referred_by`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `status`, `status_message`, `active`, `last_active`, `created_at`, `updated_at`, `deleted_at`, `points`, `exp`, `level`, `referred_by`) VALUES
(1, 'kaicenat', NULL, NULL, 1, '2025-05-25 02:18:52', '2025-05-24 10:44:04', '2025-05-24 10:44:17', NULL, 21.000, 0, 0, NULL),
(2, 'penguinz', NULL, NULL, 1, '2025-05-24 15:41:02', '2025-05-24 10:47:00', '2025-05-24 10:47:21', NULL, 10.000, 0, 0, 1),
(3, 'arthurmorgan', NULL, NULL, 1, '2025-05-24 16:29:44', '2025-05-24 16:29:10', '2025-05-24 16:29:39', NULL, NULL, 0, 0, NULL),
(4, 'salasulu', NULL, NULL, 1, '2025-05-24 16:46:19', '2025-05-24 16:43:37', '2025-05-24 16:43:58', NULL, NULL, 0, 0, 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_referred_by_foreign` FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
