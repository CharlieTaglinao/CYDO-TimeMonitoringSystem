-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 07:00 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `youthtimesystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email_id` int(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `username`, `email_id`, `password`, `role`, `created_at`) VALUES
(84, 'admin', 1, '$2y$10$uPul6WagIXQHnK0.08tAr.rNNXftzl.ERKTrUh4sAATX6Pjxwt.Ay', '1', '2025-04-04 16:04:23'),
(92, 'staff', 7, '$2y$10$GVzk6YjIhodlPrfVECYHXuCMOIt7BjgEASeO31WLA7jRWOZOs6VgC', '2', '2025-04-07 11:00:49'),
(93, 'admin_2', 8, '$2y$10$DIOcBc1kq/7YmYwccEKLQORzeNHmYsRt1pGJZ11CKPB2o3B33Atb.', '1', '2025-04-08 09:05:04'),
(96, 'shork', 11, '$2y$10$YY2cSldqnKcVQfG9OQRaCu7ccp2l25Ln1hVbghVOrzVDHvngsdE9m', '1', '2025-04-08 09:56:20'),
(97, 'pogi', 0, '$2y$10$Yx/gYoLMQTTHLG19qM4yXecP3Uq0z9a9RCWIoIgi9sMg90gYyIPDC', '2', '2025-04-08 10:08:31');

-- --------------------------------------------------------

--
-- Table structure for table `account_email`
--

CREATE TABLE `account_email` (
  `id` int(11) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int(11) NOT NULL,
  `barangay_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `barangay_name`) VALUES
(1, 'Alingaro'),
(2, 'Arnaldo'),
(3, 'Bacao I'),
(4, 'Bacao II'),
(5, 'Bagumbayan'),
(6, 'Biclatan'),
(7, 'Buenavista I'),
(8, 'Buenavista II'),
(9, 'Buenavista III'),
(10, 'Corregidor'),
(11, 'Dulong Bayan'),
(12, 'Governor Ferrer'),
(13, 'Javalera'),
(14, 'Manggahan'),
(15, 'Navarro'),
(16, 'Panungyanan'),
(17, 'Pasong Camachile I'),
(18, 'Pasong Camachile II'),
(19, 'Pasong Kawayan I'),
(20, 'Pasong Kawayan II'),
(21, 'Pinagtipunan'),
(22, 'Prinza'),
(23, 'Sampalucan'),
(24, 'Santiago'),
(25, 'San Francisco'),
(26, 'San Gabriel'),
(27, 'San Juan I'),
(28, 'San Juan II'),
(29, 'Santa Clara'),
(30, 'Tapia'),
(31, 'Tejero'),
(32, 'Vibora'),
(33, '1896');

-- --------------------------------------------------------

--
-- Table structure for table `email`
--

CREATE TABLE `email` (
  `id` int(11) NOT NULL,
  `client_id` int(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `office`
--

CREATE TABLE `office` (
  `id` int(255) NOT NULL,
  `office_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `office`
--

INSERT INTO `office` (`id`, `office_name`) VALUES
(1, 'CITY YOUTH DEVELOPMENT OFFICE'),
(2, 'PERSON WITH DISABILITY AFFAIRS OFFICE');

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `id` int(100) NOT NULL,
  `permission_id` varchar(255) NOT NULL,
  `permission_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`id`, `permission_id`, `permission_name`, `category`, `created_at`) VALUES
(1, 'T9rPHeL7ectsYwT6Ih2AswTeZ', 'Add Account', 'Account', '2025-03-11 02:22:00'),
(3, 'c5pwoB1uPkzwwZgFokRZZ85fE', 'Monitor Visitor', 'Monitoring', '2025-03-11 02:32:06'),
(4, 'GfsdZkrEFuNhmIUmxIm8e7fS8', 'Download Reports', 'Reports', '2025-03-11 02:33:42'),
(5, 'qD0mEzTMK6Toi4u8aR1Pdusag', 'View Analytics', 'Reports', '2025-03-11 02:34:07'),
(6, 'JyCQULxjmYOycJFVOyceWb8BA', 'Monitor Dashboard', 'Monitoring', '2025-03-11 02:34:55'),
(7, '906IZi3K8od7FBS518t5I31jY', 'Edit/Delete Account', 'Account', '2025-03-11 02:49:21'),
(8, '8sAygcnqpOXP8aAAG7IAWI4Cg', 'Add Permission', 'Permission', '2025-03-11 06:04:06'),
(9, 'ubmssiHKw9GEPDulEVpDtOudM', 'View User Permission', 'Permission', '2025-03-11 09:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `purpose`
--

CREATE TABLE `purpose` (
  `id` int(255) NOT NULL,
  `client_id` int(255) NOT NULL,
  `purpose` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(255) NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `sex`
--

CREATE TABLE `sex` (
  `id` int(255) NOT NULL,
  `sex_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sex`
--

INSERT INTO `sex` (`id`, `sex_name`) VALUES
(1, 'MALE'),
(2, 'FEMALE');

-- --------------------------------------------------------

--
-- Table structure for table `time_logs`
--

CREATE TABLE `time_logs` (
  `id` int(255) NOT NULL,
  `client_id` int(255) NOT NULL,
  `time_in` varchar(255) NOT NULL,
  `time_out` varchar(255) DEFAULT NULL,
  `code` varchar(6) DEFAULT NULL,
  `office_id` int(50) NOT NULL,
  `status` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission_id`) VALUES
(246, 84, 'JyCQULxjmYOycJFVOyceWb8BA'),
(247, 84, '8sAygcnqpOXP8aAAG7IAWI4Cg'),
(248, 84, 'T9rPHeL7ectsYwT6Ih2AswTeZ'),
(249, 84, '906IZi3K8od7FBS518t5I31jY'),
(250, 84, 'c5pwoB1uPkzwwZgFokRZZ85fE'),
(251, 84, 'GfsdZkrEFuNhmIUmxIm8e7fS8'),
(252, 84, 'qD0mEzTMK6Toi4u8aR1Pdusag'),
(253, 84, 'ubmssiHKw9GEPDulEVpDtOudM');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `sex_id` int(20) NOT NULL,
  `purpose_id` int(100) NOT NULL,
  `office_id` int(100) NOT NULL,
  `barangay_id` int(100) NOT NULL,
  `age` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_email`
--
ALTER TABLE `account_email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email`
--
ALTER TABLE `email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `office`
--
ALTER TABLE `office`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purpose`
--
ALTER TABLE `purpose`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_client_id` (`client_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sex`
--
ALTER TABLE `sex`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_logs`
--
ALTER TABLE `time_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `account_email`
--
ALTER TABLE `account_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `email`
--
ALTER TABLE `email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `office`
--
ALTER TABLE `office`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `purpose`
--
ALTER TABLE `purpose`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sex`
--
ALTER TABLE `sex`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `time_logs`
--
ALTER TABLE `time_logs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `purpose`
--
ALTER TABLE `purpose`
  ADD CONSTRAINT `fk_client_id` FOREIGN KEY (`client_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
