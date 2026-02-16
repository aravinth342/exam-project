-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2026 at 03:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exam_hall_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-02-02 06:00:05');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('exam_date','result','general','important') DEFAULT 'general',
  `file_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `type`, `file_path`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(5, 'ex', 'iediwhfh', 'exam_date', 'comprehensive_timetable_1770126739.pdf', 1, 1, '2026-02-03 13:52:34', '2026-02-03 13:52:34'),
(6, 'exam', 'rf', 'exam_date', 'comprehensive_timetable_1770127193.pdf', 1, 1, '2026-02-03 13:59:55', '2026-02-03 13:59:55'),
(7, 'exam', 'rf', 'exam_date', 'comprehensive_timetable_1770127193.pdf', 1, 1, '2026-02-03 14:00:34', '2026-02-03 14:00:34');

--
-- Triggers `announcements`
--
DELIMITER $$
CREATE TRIGGER `announcement_after_delete` AFTER DELETE ON `announcements` FOR EACH ROW BEGIN
  INSERT INTO announcement_logs (announcement_id, event_type, old_title, old_content, old_type, old_file_path, old_created_by, db_user, connection_id)
  VALUES (OLD.id, 'DELETE', OLD.title, OLD.content, OLD.type, OLD.file_path, OLD.created_by, CURRENT_USER(), CONNECTION_ID());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `announcement_after_insert` AFTER INSERT ON `announcements` FOR EACH ROW BEGIN
  INSERT INTO announcement_logs (announcement_id, event_type, new_title, new_content, new_type, new_file_path, new_created_by, db_user, connection_id)
  VALUES (NEW.id, 'INSERT', NEW.title, NEW.content, NEW.type, NEW.file_path, NEW.created_by, CURRENT_USER(), CONNECTION_ID());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `announcement_after_update` AFTER UPDATE ON `announcements` FOR EACH ROW BEGIN
  INSERT INTO announcement_logs (announcement_id, event_type, new_title, new_content, new_type, new_file_path, new_created_by, old_title, old_content, old_type, old_file_path, old_created_by, db_user, connection_id)
  VALUES (NEW.id, 'UPDATE', NEW.title, NEW.content, NEW.type, NEW.file_path, NEW.created_by, OLD.title, OLD.content, OLD.type, OLD.file_path, OLD.created_by, CURRENT_USER(), CONNECTION_ID());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_exam_date_update` BEFORE UPDATE ON `announcements` FOR EACH ROW BEGIN
  IF NEW.type = 'exam_date' AND (NEW.file_path IS NULL OR NEW.file_path = '') THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Exam date announcements require a file';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_exam_date_without_file` BEFORE INSERT ON `announcements` FOR EACH ROW BEGIN
  IF NEW.type = 'exam_date' AND (NEW.file_path IS NULL OR NEW.file_path = '') THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Exam date announcements require a file';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_logs`
--

CREATE TABLE `announcement_logs` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) DEFAULT NULL,
  `event_type` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `new_title` text DEFAULT NULL,
  `new_content` text DEFAULT NULL,
  `new_type` varchar(50) DEFAULT NULL,
  `new_file_path` varchar(255) DEFAULT NULL,
  `new_created_by` int(11) DEFAULT NULL,
  `old_title` text DEFAULT NULL,
  `old_content` text DEFAULT NULL,
  `old_type` varchar(50) DEFAULT NULL,
  `old_file_path` varchar(255) DEFAULT NULL,
  `old_created_by` int(11) DEFAULT NULL,
  `db_user` varchar(128) DEFAULT NULL,
  `connection_id` int(11) DEFAULT NULL,
  `event_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_logs`
--

INSERT INTO `announcement_logs` (`id`, `announcement_id`, `event_type`, `new_title`, `new_content`, `new_type`, `new_file_path`, `new_created_by`, `old_title`, `old_content`, `old_type`, `old_file_path`, `old_created_by`, `db_user`, `connection_id`, `event_time`) VALUES
(1, 5, 'INSERT', 'ex', 'iediwhfh', 'exam_date', 'comprehensive_timetable_1770126739.pdf', 1, NULL, NULL, NULL, NULL, NULL, 'root@localhost', 161, '2026-02-03 13:52:34'),
(2, 6, 'INSERT', 'exam', 'rf', 'exam_date', 'comprehensive_timetable_1770127193.pdf', 1, NULL, NULL, NULL, NULL, NULL, 'root@localhost', 175, '2026-02-03 13:59:55'),
(3, 7, 'INSERT', 'exam', 'rf', 'exam_date', 'comprehensive_timetable_1770127193.pdf', 1, NULL, NULL, NULL, NULL, NULL, 'root@localhost', 178, '2026-02-03 14:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `department_id`, `created_at`) VALUES
(1, 'Diploma in Mechanical Engineering', 1, '2026-02-02 06:00:05'),
(2, 'Diploma in Electrical and Electronics Engineering', 2, '2026-02-02 06:00:05'),
(3, 'Diploma in Automobile Engineering', 3, '2026-02-02 06:00:05'),
(4, 'Diploma in Electronics and Communication Engineering', 4, '2026-02-02 06:00:05'),
(5, 'Diploma in Computer Engineering', 5, '2026-02-02 06:00:05');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`) VALUES
(1, 'Mechanical Engineering', '2026-02-02 06:00:05'),
(2, 'Electrical and Electronics Engineering', '2026-02-02 06:00:05'),
(3, 'Automobile Engineering', '2026-02-02 06:00:05'),
(4, 'Electronics and Communication Engineering', '2026-02-02 06:00:05'),
(5, 'Computer Engineering', '2026-02-02 06:00:05'),
(6, 'Civil Engineering', '2026-02-02 13:25:21');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `session` enum('morning','afternoon') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `date`, `subject_id`, `department_id`, `session`, `created_at`) VALUES
(5, '2026-02-03', 30, 5, 'morning', '2026-02-02 13:11:18'),
(6, '2026-02-04', 29, 5, 'morning', '2026-02-02 13:11:41'),
(7, '2026-02-05', 31, 5, 'morning', '2026-02-02 13:12:17'),
(8, '2026-02-06', 35, 5, 'morning', '2026-02-02 13:12:59'),
(9, '2026-02-07', 33, 5, 'morning', '2026-02-02 13:13:18'),
(10, '2026-02-08', 32, 5, 'morning', '2026-02-02 13:13:54'),
(11, '2026-02-09', 34, 5, 'morning', '2026-02-02 13:14:21'),
(12, '2026-02-10', 36, 5, 'morning', '2026-02-02 13:14:57'),
(13, '2026-02-28', 17, 5, 'morning', '2026-02-03 13:36:27'),
(14, '2026-02-28', 25, 5, 'morning', '2026-02-03 13:39:21'),
(15, '2026-02-18', 25, 3, 'afternoon', '2026-02-03 13:41:06'),
(16, '2026-02-03', 14, 4, 'morning', '2026-02-03 13:50:46'),
(17, '2026-02-22', 18, 5, 'morning', '2026-02-03 13:59:39'),
(18, '2026-02-05', 35, 2, 'morning', '2026-02-03 14:04:59'),
(19, '2026-02-27', 25, 4, 'morning', '2026-02-03 14:14:38');

-- --------------------------------------------------------

--
-- Table structure for table `exam_halls`
--

CREATE TABLE `exam_halls` (
  `id` int(11) NOT NULL,
  `hall_no` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_halls`
--

INSERT INTO `exam_halls` (`id`, `hall_no`, `capacity`, `created_at`) VALUES
(1, 'Hall 1', 30, '2026-02-02 06:00:05'),
(2, 'Hall 2', 30, '2026-02-02 06:00:05'),
(3, 'Hall 3', 30, '2026-02-02 06:00:05');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `total_marks` decimal(5,2) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `status` enum('pass','fail','absent') DEFAULT 'pass',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `exam_id`, `student_id`, `marks_obtained`, `total_marks`, `grade`, `status`, `created_at`) VALUES
(8, 5, 11, 86.00, 100.00, 'A+', 'pass', '2026-02-02 13:17:32'),
(9, 7, 11, 76.00, 100.00, 'A', 'pass', '2026-02-02 13:17:53'),
(10, 7, 11, 89.00, 100.00, 'A+', 'pass', '2026-02-02 13:21:03'),
(11, 8, 11, 98.00, 100.00, 'O', 'pass', '2026-02-02 13:21:33'),
(12, 9, 11, 87.00, 100.00, 'A+', 'pass', '2026-02-02 13:22:15'),
(13, 10, 11, 86.00, 100.00, 'A+', 'pass', '2026-02-02 13:23:08'),
(14, 11, 11, 82.00, 100.00, 'A+', 'pass', '2026-02-02 13:23:47'),
(15, 12, 11, 89.00, 100.00, 'A+', 'pass', '2026-02-02 13:24:12'),
(16, 5, 12, 89.00, 100.00, 'A+', 'pass', '2026-02-02 13:26:03'),
(17, 6, 12, 91.00, 100.00, 'O', 'pass', '2026-02-02 13:26:29'),
(18, 7, 12, 100.00, 100.00, 'O', 'pass', '2026-02-02 13:37:59'),
(19, 8, 12, 97.00, 100.00, 'O', 'pass', '2026-02-02 13:38:17'),
(20, 9, 12, 99.00, 100.00, 'O', 'pass', '2026-02-02 13:38:39'),
(21, 10, 12, 94.00, 100.00, 'O', 'pass', '2026-02-02 13:39:02'),
(22, 11, 12, 100.00, 100.00, 'O', 'pass', '2026-02-02 13:39:30'),
(23, 12, 12, 100.00, 100.00, 'O', 'pass', '2026-02-02 13:39:46'),
(24, 5, 29, 75.00, 100.00, 'A', 'pass', '2026-02-03 03:58:52'),
(25, 6, 29, 99.00, 100.00, 'O', 'pass', '2026-02-03 03:59:20'),
(26, 7, 29, 87.00, 100.00, 'A+', 'pass', '2026-02-03 03:59:58'),
(27, 8, 29, 97.00, 100.00, 'O', 'pass', '2026-02-03 04:00:21'),
(28, 9, 29, 90.00, 100.00, 'O', 'pass', '2026-02-03 04:01:07'),
(29, 10, 29, 98.00, 100.00, 'O', 'pass', '2026-02-03 04:02:03'),
(30, 11, 29, 100.00, 100.00, 'O', 'pass', '2026-02-03 04:02:31'),
(31, 12, 29, 100.00, 100.00, 'O', 'pass', '2026-02-03 04:02:52');

-- --------------------------------------------------------

--
-- Table structure for table `seating_arrangements`
--

CREATE TABLE `seating_arrangements` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `hall_id` int(11) DEFAULT NULL,
  `seat_number` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seating_arrangements`
--

INSERT INTO `seating_arrangements` (`id`, `exam_id`, `student_id`, `hall_id`, `seat_number`, `created_at`) VALUES
(24, 5, 11, 1, 1, '2026-02-02 13:15:15'),
(25, 5, 12, 1, 2, '2026-02-02 13:15:15'),
(26, 5, 13, 1, 3, '2026-02-02 13:15:15'),
(27, 5, 14, 1, 4, '2026-02-02 13:15:15'),
(28, 5, 15, 1, 5, '2026-02-02 13:15:15'),
(29, 5, 16, 1, 6, '2026-02-02 13:15:15'),
(30, 5, 17, 1, 7, '2026-02-02 13:15:15'),
(31, 5, 18, 1, 8, '2026-02-02 13:15:15'),
(32, 5, 19, 1, 9, '2026-02-02 13:15:15'),
(33, 5, 20, 1, 10, '2026-02-02 13:15:15'),
(34, 5, 21, 1, 11, '2026-02-02 13:15:15'),
(35, 5, 22, 1, 12, '2026-02-02 13:15:15'),
(36, 5, 23, 1, 13, '2026-02-02 13:15:15'),
(37, 5, 24, 1, 14, '2026-02-02 13:15:15'),
(38, 5, 25, 1, 15, '2026-02-02 13:15:15'),
(39, 5, 26, 1, 16, '2026-02-02 13:15:15'),
(40, 5, 27, 1, 17, '2026-02-02 13:15:15'),
(41, 5, 28, 1, 18, '2026-02-02 13:15:15'),
(42, 5, 29, 1, 19, '2026-02-02 13:15:15'),
(43, 5, 31, 1, 20, '2026-02-02 13:15:15'),
(44, 5, 30, 1, 21, '2026-02-02 13:15:15'),
(45, 6, 11, 1, 1, '2026-02-02 13:15:20'),
(46, 6, 12, 1, 2, '2026-02-02 13:15:20'),
(47, 6, 13, 1, 3, '2026-02-02 13:15:20'),
(48, 6, 14, 1, 4, '2026-02-02 13:15:20'),
(49, 6, 15, 1, 5, '2026-02-02 13:15:20'),
(50, 6, 16, 1, 6, '2026-02-02 13:15:20'),
(51, 6, 17, 1, 7, '2026-02-02 13:15:20'),
(52, 6, 18, 1, 8, '2026-02-02 13:15:20'),
(53, 6, 19, 1, 9, '2026-02-02 13:15:20'),
(54, 6, 20, 1, 10, '2026-02-02 13:15:20'),
(55, 6, 21, 1, 11, '2026-02-02 13:15:20'),
(56, 6, 22, 1, 12, '2026-02-02 13:15:20'),
(57, 6, 23, 1, 13, '2026-02-02 13:15:20'),
(58, 6, 24, 1, 14, '2026-02-02 13:15:20'),
(59, 6, 25, 1, 15, '2026-02-02 13:15:20'),
(60, 6, 26, 1, 16, '2026-02-02 13:15:20'),
(61, 6, 27, 1, 17, '2026-02-02 13:15:20'),
(62, 6, 28, 1, 18, '2026-02-02 13:15:20'),
(63, 6, 29, 1, 19, '2026-02-02 13:15:20'),
(64, 6, 31, 1, 20, '2026-02-02 13:15:20'),
(65, 6, 30, 1, 21, '2026-02-02 13:15:20'),
(66, 7, 11, 1, 1, '2026-02-02 13:15:33'),
(67, 7, 12, 1, 2, '2026-02-02 13:15:33'),
(68, 7, 13, 1, 3, '2026-02-02 13:15:33'),
(69, 7, 14, 1, 4, '2026-02-02 13:15:33'),
(70, 7, 15, 1, 5, '2026-02-02 13:15:33'),
(71, 7, 16, 1, 6, '2026-02-02 13:15:33'),
(72, 7, 17, 1, 7, '2026-02-02 13:15:33'),
(73, 7, 18, 1, 8, '2026-02-02 13:15:33'),
(74, 7, 19, 1, 9, '2026-02-02 13:15:33'),
(75, 7, 20, 1, 10, '2026-02-02 13:15:33'),
(76, 7, 21, 1, 11, '2026-02-02 13:15:33'),
(77, 7, 22, 1, 12, '2026-02-02 13:15:33'),
(78, 7, 23, 1, 13, '2026-02-02 13:15:33'),
(79, 7, 24, 1, 14, '2026-02-02 13:15:33'),
(80, 7, 25, 1, 15, '2026-02-02 13:15:33'),
(81, 7, 26, 1, 16, '2026-02-02 13:15:33'),
(82, 7, 27, 1, 17, '2026-02-02 13:15:33'),
(83, 7, 28, 1, 18, '2026-02-02 13:15:33'),
(84, 7, 29, 1, 19, '2026-02-02 13:15:33'),
(85, 7, 31, 1, 20, '2026-02-02 13:15:33'),
(86, 7, 30, 1, 21, '2026-02-02 13:15:33'),
(87, 8, 11, 1, 1, '2026-02-02 13:15:39'),
(88, 8, 12, 1, 2, '2026-02-02 13:15:39'),
(89, 8, 13, 1, 3, '2026-02-02 13:15:39'),
(90, 8, 14, 1, 4, '2026-02-02 13:15:39'),
(91, 8, 15, 1, 5, '2026-02-02 13:15:39'),
(92, 8, 16, 1, 6, '2026-02-02 13:15:39'),
(93, 8, 17, 1, 7, '2026-02-02 13:15:39'),
(94, 8, 18, 1, 8, '2026-02-02 13:15:39'),
(95, 8, 19, 1, 9, '2026-02-02 13:15:39'),
(96, 8, 20, 1, 10, '2026-02-02 13:15:39'),
(97, 8, 21, 1, 11, '2026-02-02 13:15:39'),
(98, 8, 22, 1, 12, '2026-02-02 13:15:39'),
(99, 8, 23, 1, 13, '2026-02-02 13:15:39'),
(100, 8, 24, 1, 14, '2026-02-02 13:15:39'),
(101, 8, 25, 1, 15, '2026-02-02 13:15:39'),
(102, 8, 26, 1, 16, '2026-02-02 13:15:39'),
(103, 8, 27, 1, 17, '2026-02-02 13:15:39'),
(104, 8, 28, 1, 18, '2026-02-02 13:15:39'),
(105, 8, 29, 1, 19, '2026-02-02 13:15:39'),
(106, 8, 31, 1, 20, '2026-02-02 13:15:39'),
(107, 8, 30, 1, 21, '2026-02-02 13:15:39'),
(108, 9, 11, 1, 1, '2026-02-02 13:15:44'),
(109, 9, 12, 1, 2, '2026-02-02 13:15:44'),
(110, 9, 13, 1, 3, '2026-02-02 13:15:44'),
(111, 9, 14, 1, 4, '2026-02-02 13:15:44'),
(112, 9, 15, 1, 5, '2026-02-02 13:15:44'),
(113, 9, 16, 1, 6, '2026-02-02 13:15:44'),
(114, 9, 17, 1, 7, '2026-02-02 13:15:44'),
(115, 9, 18, 1, 8, '2026-02-02 13:15:44'),
(116, 9, 19, 1, 9, '2026-02-02 13:15:44'),
(117, 9, 20, 1, 10, '2026-02-02 13:15:44'),
(118, 9, 21, 1, 11, '2026-02-02 13:15:44'),
(119, 9, 22, 1, 12, '2026-02-02 13:15:44'),
(120, 9, 23, 1, 13, '2026-02-02 13:15:44'),
(121, 9, 24, 1, 14, '2026-02-02 13:15:44'),
(122, 9, 25, 1, 15, '2026-02-02 13:15:44'),
(123, 9, 26, 1, 16, '2026-02-02 13:15:44'),
(124, 9, 27, 1, 17, '2026-02-02 13:15:44'),
(125, 9, 28, 1, 18, '2026-02-02 13:15:44'),
(126, 9, 29, 1, 19, '2026-02-02 13:15:44'),
(127, 9, 31, 1, 20, '2026-02-02 13:15:44'),
(128, 9, 30, 1, 21, '2026-02-02 13:15:44'),
(129, 10, 11, 1, 1, '2026-02-02 13:15:48'),
(130, 10, 12, 1, 2, '2026-02-02 13:15:48'),
(131, 10, 13, 1, 3, '2026-02-02 13:15:48'),
(132, 10, 14, 1, 4, '2026-02-02 13:15:48'),
(133, 10, 15, 1, 5, '2026-02-02 13:15:48'),
(134, 10, 16, 1, 6, '2026-02-02 13:15:48'),
(135, 10, 17, 1, 7, '2026-02-02 13:15:48'),
(136, 10, 18, 1, 8, '2026-02-02 13:15:48'),
(137, 10, 19, 1, 9, '2026-02-02 13:15:48'),
(138, 10, 20, 1, 10, '2026-02-02 13:15:48'),
(139, 10, 21, 1, 11, '2026-02-02 13:15:48'),
(140, 10, 22, 1, 12, '2026-02-02 13:15:48'),
(141, 10, 23, 1, 13, '2026-02-02 13:15:48'),
(142, 10, 24, 1, 14, '2026-02-02 13:15:48'),
(143, 10, 25, 1, 15, '2026-02-02 13:15:48'),
(144, 10, 26, 1, 16, '2026-02-02 13:15:48'),
(145, 10, 27, 1, 17, '2026-02-02 13:15:48'),
(146, 10, 28, 1, 18, '2026-02-02 13:15:48'),
(147, 10, 29, 1, 19, '2026-02-02 13:15:48'),
(148, 10, 31, 1, 20, '2026-02-02 13:15:48'),
(149, 10, 30, 1, 21, '2026-02-02 13:15:48'),
(150, 11, 11, 1, 1, '2026-02-02 13:15:52'),
(151, 11, 12, 1, 2, '2026-02-02 13:15:52'),
(152, 11, 13, 1, 3, '2026-02-02 13:15:52'),
(153, 11, 14, 1, 4, '2026-02-02 13:15:52'),
(154, 11, 15, 1, 5, '2026-02-02 13:15:52'),
(155, 11, 16, 1, 6, '2026-02-02 13:15:52'),
(156, 11, 17, 1, 7, '2026-02-02 13:15:52'),
(157, 11, 18, 1, 8, '2026-02-02 13:15:52'),
(158, 11, 19, 1, 9, '2026-02-02 13:15:52'),
(159, 11, 20, 1, 10, '2026-02-02 13:15:52'),
(160, 11, 21, 1, 11, '2026-02-02 13:15:52'),
(161, 11, 22, 1, 12, '2026-02-02 13:15:52'),
(162, 11, 23, 1, 13, '2026-02-02 13:15:52'),
(163, 11, 24, 1, 14, '2026-02-02 13:15:52'),
(164, 11, 25, 1, 15, '2026-02-02 13:15:52'),
(165, 11, 26, 1, 16, '2026-02-02 13:15:52'),
(166, 11, 27, 1, 17, '2026-02-02 13:15:52'),
(167, 11, 28, 1, 18, '2026-02-02 13:15:52'),
(168, 11, 29, 1, 19, '2026-02-02 13:15:52'),
(169, 11, 31, 1, 20, '2026-02-02 13:15:52'),
(170, 11, 30, 1, 21, '2026-02-02 13:15:52'),
(171, 12, 11, 1, 1, '2026-02-02 13:15:56'),
(172, 12, 12, 1, 2, '2026-02-02 13:15:57'),
(173, 12, 13, 1, 3, '2026-02-02 13:15:57'),
(174, 12, 14, 1, 4, '2026-02-02 13:15:57'),
(175, 12, 15, 1, 5, '2026-02-02 13:15:57'),
(176, 12, 16, 1, 6, '2026-02-02 13:15:57'),
(177, 12, 17, 1, 7, '2026-02-02 13:15:57'),
(178, 12, 18, 1, 8, '2026-02-02 13:15:57'),
(179, 12, 19, 1, 9, '2026-02-02 13:15:57'),
(180, 12, 20, 1, 10, '2026-02-02 13:15:57'),
(181, 12, 21, 1, 11, '2026-02-02 13:15:57'),
(182, 12, 22, 1, 12, '2026-02-02 13:15:57'),
(183, 12, 23, 1, 13, '2026-02-02 13:15:57'),
(184, 12, 24, 1, 14, '2026-02-02 13:15:57'),
(185, 12, 25, 1, 15, '2026-02-02 13:15:57'),
(186, 12, 26, 1, 16, '2026-02-02 13:15:57'),
(187, 12, 27, 1, 17, '2026-02-02 13:15:57'),
(188, 12, 28, 1, 18, '2026-02-02 13:15:57'),
(189, 12, 29, 1, 19, '2026-02-02 13:15:57'),
(190, 12, 31, 1, 20, '2026-02-02 13:15:57'),
(191, 12, 30, 1, 21, '2026-02-02 13:15:57');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `register_number` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `register_number`, `name`, `department_id`, `year`, `semester`, `created_at`) VALUES
(11, '24506263', 'S.Aathish', 5, 3, 6, '2026-02-02 06:05:54'),
(12, '24506264', 'P.Aravinth', 5, 3, 6, '2026-02-02 06:06:33'),
(13, '24506265', 'R.Bhuvaneshwari', 5, 3, 6, '2026-02-02 06:07:20'),
(14, '24506266', 'A.Fawaz', 5, 3, 6, '2026-02-02 06:08:00'),
(15, '24506267', 'S.Gowri', 5, 3, 6, '2026-02-02 06:08:34'),
(16, '24506268', 'N.Guna', 5, 3, 6, '2026-02-02 06:09:14'),
(17, '24506269', 'S.Harish', 5, 3, 6, '2026-02-02 06:11:10'),
(18, '24506270', 'V.Jegannath', 5, 3, 6, '2026-02-02 06:12:01'),
(19, '24506271', 'A.Joel', 5, 3, 6, '2026-02-02 06:12:30'),
(20, '24506272', 'C.Mani Kandan', 5, 3, 6, '2026-02-02 06:13:25'),
(21, '24506274', 'H.Mohamed Tanish', 5, 3, 6, '2026-02-02 06:14:21'),
(22, '24506275', 'T.Naveen Kumar', 5, 3, 6, '2026-02-02 06:15:23'),
(23, '24506276', 'A.Naveeth Jassim', 5, 3, 6, '2026-02-02 06:16:07'),
(24, '24506279', 'R.Robert Sundar Singh', 5, 3, 6, '2026-02-02 06:17:35'),
(25, '24506280', 'M.Rocky', 5, 3, 6, '2026-02-02 06:18:23'),
(26, '24506281', 'S.Sanjay', 5, 3, 6, '2026-02-02 06:19:03'),
(27, '24506282', 'C.Tamil Selvan', 5, 3, 6, '2026-02-02 06:19:48'),
(28, '24506283', 'U.Tamas Johnson', 5, 3, 6, '2026-02-02 06:21:02'),
(29, '24506284', 'K.Thangamathi Raja', 5, 3, 6, '2026-02-02 06:22:18'),
(30, '24591428', 'Rexallin Darthi', 5, 3, 6, '2026-02-02 06:56:30'),
(31, '24507283', 'R.Vishalachi', 5, 3, 6, '2026-02-02 06:57:21');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `semester` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `course_id`, `semester`, `created_at`) VALUES
(13, 'DIGITAL LOGIC DESIGN', 5, 3, '2026-02-02 07:01:16'),
(14, 'DIGITAL ELECTRONICS', 4, 3, '2026-02-02 07:01:47'),
(15, 'BASICS OF COMPUTER ENGINEERING', 5, 2, '2026-02-02 07:02:25'),
(16, 'RELATIONAL DATABASE MANAGEMENT', 5, 3, '2026-02-02 07:03:20'),
(17, 'DIGITAL LOGIC DESIGN LAB', 5, 3, '2026-02-02 07:04:17'),
(18, 'OPERATING SYSTEM', 5, 3, '2026-02-02 07:04:48'),
(19, 'WEB DESIGN', 5, 3, '2026-02-02 07:06:39'),
(21, 'C-PROGRAMMING', 5, 3, '2026-02-02 07:09:59'),
(22, 'COMPUTER NETWORKS AND SECURITY', 5, 4, '2026-02-02 07:11:22'),
(23, 'DATA STRCTURE USING PYTHON', 5, 4, '2026-02-02 07:11:59'),
(24, 'PYTHON PROGRAMMING', 5, 4, '2026-02-02 07:12:37'),
(25, 'JAVA PROGRAMMING', 5, 4, '2026-02-02 07:13:22'),
(26, 'E-PUBLISHING TOOLS', 5, 4, '2026-02-02 07:21:55'),
(27, 'SCRIPTING LANGUAGE', 5, 4, '2026-02-02 07:22:25'),
(29, 'CLOUD COMPUTING', 5, 5, '2026-02-02 07:23:26'),
(30, 'ARTIFICIAL INTELLIGENCE', 5, 5, '2026-02-02 07:25:48'),
(31, 'COMPUTER HARDWERE NETWORK', 5, 5, '2026-02-02 07:27:12'),
(32, 'IOT', 5, 5, '2026-02-02 07:27:49'),
(33, 'INNOVATION STARTUP', 5, 5, '2026-02-02 07:28:17'),
(34, 'MULTIMEDIA', 5, 5, '2026-02-02 13:07:38'),
(35, 'INDUSTRIAL TRAINNING', 5, 5, '2026-02-02 13:08:27'),
(36, 'NAAN MUDHALVAN', 5, 5, '2026-02-02 13:09:12'),
(37, '5G TECHNOLOGY', 5, 6, '2026-02-02 13:09:41'),
(38, 'DATA SCIENCE', 5, 6, '2026-02-02 13:09:58'),
(39, 'FINAL PROJECT', 5, 6, '2026-02-02 13:10:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `announcement_logs`
--
ALTER TABLE `announcement_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `exam_halls`
--
ALTER TABLE `exam_halls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hall_no` (`hall_no`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `seating_arrangements`
--
ALTER TABLE `seating_arrangements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `hall_id` (`hall_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `register_number` (`register_number`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `announcement_logs`
--
ALTER TABLE `announcement_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `exam_halls`
--
ALTER TABLE `exam_halls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `seating_arrangements`
--
ALTER TABLE `seating_arrangements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seating_arrangements`
--
ALTER TABLE `seating_arrangements`
  ADD CONSTRAINT `seating_arrangements_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seating_arrangements_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seating_arrangements_ibfk_3` FOREIGN KEY (`hall_id`) REFERENCES `exam_halls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
