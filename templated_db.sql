-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 06:37 AM
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
(100, 'mentor', 14, '$2y$10$CXAKPnAHkdMHMQqvPoHT4OnY7u1HBJRoRySyLoHdKn2C50.niKPG2', '5', '2025-05-09 14:27:41'),
(101, 'charlie_member', 15, '$2y$10$KBkUSCwOQXX1HPoXdzUuzu8T3kU6Fm1FUPQtMibbJoHei629d9Bhy', '7', '2025-05-09 14:43:40'),
(102, 'coordinator', 16, '$2y$10$lNJMVBf269qGC.axflcd4eAYtfGhEWAmyouJkilVr/5Ip3k3ejbuu', '6', '2025-05-13 18:37:22');

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

--
-- Dumping data for table `account_email`
--

INSERT INTO `account_email` (`id`, `email_address`, `created_at`, `updated_at`) VALUES
(14, 'mentor@gmail.com', '2025-05-09 14:27:41', NULL),
(15, 'member@gmail.com', '2025-05-09 14:43:40', NULL),
(16, 'coordinator@gmail.com', '2025-05-13 18:37:22', NULL);

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
-- Table structure for table `member_applicants`
--

CREATE TABLE `member_applicants` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `email_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `sex_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_code`
--

CREATE TABLE `member_code` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) DEFAULT NULL,
  `membership_code` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_email`
--

CREATE TABLE `member_email` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_school_name`
--

CREATE TABLE `member_school_name` (
  `id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(7, '906IZi3K8od7FBS518t5I31jY', 'Edit/Delete Account', 'Account', '2025-03-11 02:49:21'),
(8, '8sAygcnqpOXP8aAAG7IAWI4Cg', 'Add Permission', 'Permission', '2025-03-11 06:04:06'),
(9, 'ubmssiHKw9GEPDulEVpDtOudM', 'View User Permission', 'Permission', '2025-03-11 09:32:05'),
(10, '6QmUceiC4tYAFPR8ablg55KFZ', 'Accept/Decline Application', 'Membership', '2025-05-13 09:44:50'),
(11, '3DijIfOkS1uljCeXsJoyJAIbt', 'Activate/Deactivate Member', 'Membership', '2025-05-13 09:46:01'),
(12, 'M24yNFhXnUXIgzruLUVLrr1dQ', 'View Member Codes', 'Membership', '2025-05-13 09:46:01'),
(13, 'nLpx3AoiT6FJB8BVTGy4D6VE7', 'Add Account Type', 'Configuration', '2025-05-13 09:59:58'),
(14, 'p1YjKW0Ny5bLE64YdGreQJ92w', 'View Account Type', 'Configuration', '2025-05-13 09:59:58');

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
  `role` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`, `created_at`) VALUES
(5, 'MENTOR', '2025-05-09 14:16:32'),
(6, 'COORDINATOR', '2025-05-09 14:42:26'),
(7, 'MEMBER', '2025-05-09 14:42:41');

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
  `code` varchar(100) DEFAULT NULL,
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
(247, 84, '8sAygcnqpOXP8aAAG7IAWI4Cg'),
(250, 84, 'c5pwoB1uPkzwwZgFokRZZ85fE'),
(251, 84, 'GfsdZkrEFuNhmIUmxIm8e7fS8'),
(252, 84, 'qD0mEzTMK6Toi4u8aR1Pdusag'),
(253, 84, 'ubmssiHKw9GEPDulEVpDtOudM'),
(259, 100, 'T9rPHeL7ectsYwT6Ih2AswTeZ'),
(260, 100, '906IZi3K8od7FBS518t5I31jY'),
(261, 100, 'c5pwoB1uPkzwwZgFokRZZ85fE'),
(262, 100, 'GfsdZkrEFuNhmIUmxIm8e7fS8'),
(263, 100, 'qD0mEzTMK6Toi4u8aR1Pdusag'),
(264, 100, '8sAygcnqpOXP8aAAG7IAWI4Cg'),
(265, 100, 'ubmssiHKw9GEPDulEVpDtOudM'),
(266, 84, '906IZi3K8od7FBS518t5I31jY'),
(268, 100, '3DijIfOkS1uljCeXsJoyJAIbt'),
(270, 100, '6QmUceiC4tYAFPR8ablg55KFZ'),
(271, 100, 'M24yNFhXnUXIgzruLUVLrr1dQ'),
(275, 100, 'p1YjKW0Ny5bLE64YdGreQJ92w'),
(276, 100, 'nLpx3AoiT6FJB8BVTGy4D6VE7'),
(277, 84, '6QmUceiC4tYAFPR8ablg55KFZ'),
(278, 84, '3DijIfOkS1uljCeXsJoyJAIbt'),
(279, 84, 'M24yNFhXnUXIgzruLUVLrr1dQ'),
(280, 84, 'nLpx3AoiT6FJB8BVTGy4D6VE7'),
(281, 84, 'p1YjKW0Ny5bLE64YdGreQJ92w'),
(282, 101, 'T9rPHeL7ectsYwT6Ih2AswTeZ'),
(283, 101, '906IZi3K8od7FBS518t5I31jY'),
(284, 101, 'c5pwoB1uPkzwwZgFokRZZ85fE'),
(285, 101, 'GfsdZkrEFuNhmIUmxIm8e7fS8'),
(286, 101, 'qD0mEzTMK6Toi4u8aR1Pdusag'),
(287, 101, '8sAygcnqpOXP8aAAG7IAWI4Cg'),
(288, 101, 'ubmssiHKw9GEPDulEVpDtOudM'),
(289, 101, '6QmUceiC4tYAFPR8ablg55KFZ'),
(290, 101, '3DijIfOkS1uljCeXsJoyJAIbt'),
(291, 101, 'M24yNFhXnUXIgzruLUVLrr1dQ'),
(292, 101, 'nLpx3AoiT6FJB8BVTGy4D6VE7'),
(293, 101, 'p1YjKW0Ny5bLE64YdGreQJ92w'),
(294, 84, 'T9rPHeL7ectsYwT6Ih2AswTeZ');

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
  `school_id` int(100) NOT NULL,
  `barangay_id` int(100) NOT NULL,
  `age` varchar(100) DEFAULT NULL,
  `membership_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitor_school_name`
--

CREATE TABLE `visitor_school_name` (
  `id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
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
-- Indexes for table `member_applicants`
--
ALTER TABLE `member_applicants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_member_email` (`email_id`),
  ADD KEY `fk_member_barangay` (`barangay_id`),
  ADD KEY `fk_member_sex` (`sex_id`),
  ADD KEY `fk_member_school_name` (`school_id`);

--
-- Indexes for table `member_code`
--
ALTER TABLE `member_code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_email`
--
ALTER TABLE `member_email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_school_name`
--
ALTER TABLE `member_school_name`
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_visitors_school` (`school_id`),
  ADD KEY `fk_visitors_barangay` (`barangay_id`),
  ADD KEY `fk_visitors_purpose` (`purpose_id`),
  ADD KEY `fk_visitors_sex` (`sex_id`),
  ADD KEY `fk_visitor_member` (`membership_id`);

--
-- Indexes for table `visitor_school_name`
--
ALTER TABLE `visitor_school_name`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `account_email`
--
ALTER TABLE `account_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `email`
--
ALTER TABLE `email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_applicants`
--
ALTER TABLE `member_applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_code`
--
ALTER TABLE `member_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_email`
--
ALTER TABLE `member_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_school_name`
--
ALTER TABLE `member_school_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `purpose`
--
ALTER TABLE `purpose`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sex`
--
ALTER TABLE `sex`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `time_logs`
--
ALTER TABLE `time_logs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitor_school_name`
--
ALTER TABLE `visitor_school_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `member_applicants`
--
ALTER TABLE `member_applicants`
  ADD CONSTRAINT `fk_member_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_member_email` FOREIGN KEY (`email_id`) REFERENCES `member_email` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_member_school_name` FOREIGN KEY (`school_id`) REFERENCES `member_school_name` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_member_sex` FOREIGN KEY (`sex_id`) REFERENCES `sex` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

--
-- Constraints for table `visitors`
--
ALTER TABLE `visitors`
  ADD CONSTRAINT `fk_visitors_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_visitors_member_code` FOREIGN KEY (`membership_id`) REFERENCES `member_code` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_visitors_school` FOREIGN KEY (`school_id`) REFERENCES `visitor_school_name` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_visitors_sex` FOREIGN KEY (`sex_id`) REFERENCES `sex` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
