-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2026 at 01:27 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `watch_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `logo`, `is_active`, `created_at`) VALUES
(1, 'Rolex', 'rolex', 'rolex-1783312816.png', 1, '2026-05-21 07:58:31'),
(2, 'Fossil', 'fossil', 'fossil-1783312804.png', 1, '2026-05-21 17:46:13'),
(3, 'Omega', 'omega', 'omega-1783312793.png', 1, '2026-06-19 06:32:16'),
(4, 'Casio', 'casio', NULL, 0, '2026-06-19 06:32:33'),
(5, 'G-SHOCK', 'g-shock', 'g-shock-1783312780.png', 1, '2026-06-19 06:33:17'),
(6, 'TAG Heuer', 'tag-heuer', 'tag-heuer-1783312767.png', 1, '2026-06-19 06:33:36'),
(7, 'Tudor', 'tudor', 'tudor-1783312754.png', 1, '2026-06-19 06:33:44'),
(8, 'Citizen', 'citizen', 'citizen-1783312741.png', 0, '2026-06-19 06:34:02'),
(9, 'Polar', 'polar', 'polar-1783312725.png', 1, '2026-06-19 06:34:16'),
(10, 'Apple', 'apple', 'apple-1783312709.png', 1, '2026-06-19 06:34:27'),
(11, 'Samsung', 'samsung', 'samsung-1783312698.png', 1, '2026-06-19 06:34:33'),
(12, 'Gurkha', 'gurkha', 'gurkha-1783312625.png', 1, '2026-06-19 06:35:00'),
(13, 'Samaya', 'samaya', 'samaya-1783312526.svg', 1, '2026-06-19 06:35:07'),
(14, 'Titan', 'titan', 'titan-1783312608.png', 1, '2026-06-19 06:35:17'),
(15, 'Oris', 'oris', 'oris-1783312513.png', 1, '2026-06-19 06:35:28'),
(16, 'Timex', 'timex', 'timex-1783312499.png', 1, '2026-06-19 06:35:46'),
(19, 'Mema', 'mema', NULL, 1, '2026-08-23 08:50:42');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `selected_strap_size` varchar(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(10) NOT NULL,
  `shipping_address` text NOT NULL,
  `payment_method` enum('COD','eSewa') NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_charge` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `order_status` enum('pending','confirmed','processing','shipped','delivered') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `ordered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `payment_method`, `payment_status`, `transaction_id`, `total_amount`, `shipping_charge`, `grand_total`, `order_status`, `admin_notes`, `ordered_at`, `confirmed_at`, `shipped_at`, `delivered_at`) VALUES
(3, 3, 'CN-20260605-26E366', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '200.00', '500.00', '700.00', 'pending', NULL, '2026-06-05 06:01:22', NULL, NULL, NULL),
(4, 3, 'CN-20260605-4D1F20', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 06:04:36', NULL, NULL, NULL),
(5, 3, 'CN-20260605-E57D0A', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 06:06:54', NULL, NULL, NULL),
(7, 3, 'CN-20260605-2D4E13', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 09:13:22', NULL, NULL, NULL),
(8, 3, 'CN-20260605-622F55', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 14:09:10', NULL, NULL, NULL),
(9, 3, 'CN-20260605-860A9B', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 14:15:04', NULL, NULL, NULL),
(10, 3, 'CN-20260605-88FFC1', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 14:15:52', NULL, NULL, NULL),
(11, 3, 'CN-20260605-B51B06', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 14:18:35', NULL, NULL, NULL),
(12, 3, 'CN-20260605-B4AC0D', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 14:33:31', NULL, NULL, NULL),
(13, 3, 'CN-20260605-F46225', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 14:47:11', NULL, NULL, NULL),
(14, 3, 'CN-20260605-26152F', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 15:43:46', NULL, NULL, NULL),
(15, 3, 'CN-20260605-920055', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', 'TXN-1780674947-1560', '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 15:52:09', NULL, NULL, NULL),
(16, 3, 'CN-20260605-60E856', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', 'TXN-1780675137-4100', '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 15:58:14', NULL, NULL, NULL),
(17, 3, 'CN-20260605-790560', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-05 16:08:55', NULL, NULL, NULL),
(18, 3, 'CN-20260606-F37BCF', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'paid', '000FNH8', '100.00', '500.00', '600.00', 'confirmed', NULL, '2026-06-06 01:01:35', '2026-06-06 01:02:41', NULL, NULL),
(19, 3, 'CN-20260606-DCB77E', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-06 01:03:41', NULL, NULL, NULL),
(20, 3, 'CN-20260606-DA4F94', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '100.00', '500.00', '600.00', 'pending', NULL, '2026-06-06 01:09:49', NULL, NULL, NULL),
(21, 3, 'CN-20260606-A2A7FE', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'COD', 'paid', NULL, '100.00', '500.00', '600.00', 'delivered', NULL, '2026-06-06 01:15:06', '2026-07-30 11:28:06', '2026-07-30 11:28:13', '2026-07-30 11:28:16'),
(22, 3, 'CN-20260606-64D2C5', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'paid', '000FNHD', '100.00', '500.00', '600.00', 'confirmed', NULL, '2026-06-06 02:47:18', '2026-06-06 02:48:37', NULL, NULL),
(23, 3, 'CN-20260612-9A2A0E', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'pending', NULL, '1000.00', '500.00', '1500.00', 'pending', NULL, '2026-06-12 14:07:21', NULL, NULL, NULL),
(24, 3, 'CN-20260612-1132B4', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'COD', 'paid', NULL, '1000.00', '500.00', '1500.00', 'confirmed', NULL, '2026-06-12 14:07:45', '2026-06-12 14:08:58', NULL, NULL),
(25, 3, 'CN-20260615-4613B9', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'paid', '000FSVC', '1200.00', '500.00', '1700.00', 'shipped', NULL, '2026-06-15 05:36:20', '2026-06-15 05:37:05', '2026-07-15 04:43:11', NULL),
(26, 3, 'CN-20260623-9ECD53', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel', 'eSewa', 'paid', '000FYEQ', '2000.00', '500.00', '2500.00', 'delivered', NULL, '2026-06-23 11:40:09', '2026-06-23 11:41:05', '2026-07-05 16:55:49', '2026-07-05 16:55:54'),
(27, 3, 'CN-20260715-5D4CC7', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'COD', 'pending', NULL, '6629.00', '500.00', '7129.00', 'pending', NULL, '2026-07-15 04:35:01', NULL, NULL, NULL),
(28, 3, 'CN-20260715-B37B25', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'paid', '000G7LT', '2999.00', '500.00', '3499.00', 'delivered', 'Ready to process', '2026-07-15 04:36:43', '2026-07-15 04:37:47', '2026-07-15 04:40:14', '2026-07-15 04:40:19'),
(29, 3, 'CN-20260716-19E2AD', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '5999.99', '500.00', '6499.99', 'pending', NULL, '2026-07-16 13:42:41', NULL, NULL, NULL),
(30, 3, 'CN-20260730-82810C', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'paid', '000GBNN', '3100.00', '500.00', '3600.00', 'confirmed', NULL, '2026-07-30 11:22:00', '2026-07-30 11:22:42', NULL, NULL),
(31, 3, 'CN-20260820-D6CA8E', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '326025.00', '500.00', '326525.00', 'pending', NULL, '2026-08-20 17:22:37', NULL, NULL, NULL),
(32, 3, 'CN-20260820-88EAC5', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '3100.00', '500.00', '3600.00', 'pending', NULL, '2026-08-20 17:27:36', NULL, NULL, NULL),
(33, 3, 'CN-20260820-A86296', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'paid', '000GOZW', '4500.00', '500.00', '5000.00', 'confirmed', NULL, '2026-08-20 17:28:26', '2026-08-20 17:29:21', NULL, NULL),
(34, 3, 'CN-20260821-D8FD69', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '2899.99', '500.00', '3399.99', 'pending', NULL, '2026-08-21 02:54:05', NULL, NULL, NULL),
(35, 3, 'CN-20260821-E82F23', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '4500.00', '500.00', '5000.00', 'pending', NULL, '2026-08-21 03:08:46', NULL, NULL, NULL),
(36, 3, 'CN-20260821-B0618F', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '1600.00', '500.00', '2100.00', 'pending', NULL, '2026-08-21 03:29:15', NULL, NULL, NULL),
(37, 3, 'CN-20260821-05FC97', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '5000.00', '500.00', '5500.00', 'pending', NULL, '2026-08-21 12:30:40', NULL, NULL, NULL),
(38, 3, 'CN-20260821-3D93F1', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'COD', 'pending', NULL, '81250.00', '500.00', '81750.00', 'pending', NULL, '2026-08-21 17:10:43', NULL, NULL, NULL),
(40, 3, 'CN-20260823-C09302', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'paid', '000GSG0', '3749.99', '500.00', '4249.99', 'confirmed', NULL, '2026-08-23 11:51:40', '2026-08-23 11:52:44', NULL, NULL),
(41, 3, 'CN-20260824-53E847', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '2950.00', '500.00', '3450.00', 'pending', NULL, '2026-08-24 12:34:13', NULL, NULL, NULL),
(42, 3, 'CN-20260824-A434AC', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'paid', '000GTDM', '2899.99', '500.00', '3399.99', 'confirmed', NULL, '2026-08-24 12:44:42', '2026-08-24 12:45:16', NULL, NULL),
(43, 3, 'CN-20260825-B63031', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'paid', '000GU1Z', '210.00', '500.00', '710.00', 'confirmed', NULL, '2026-08-25 01:11:55', '2026-08-25 01:12:39', NULL, NULL),
(44, 3, 'CN-20260825-C9DA6C', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'paid', '000GU2H', '1000.00', '500.00', '1500.00', 'confirmed', NULL, '2026-08-25 01:24:28', '2026-08-25 01:25:35', NULL, NULL),
(45, 3, 'CN-20260825-3DBF39', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '1500.00', '500.00', '2000.00', 'pending', NULL, '2026-08-25 01:26:27', NULL, NULL, NULL),
(46, 3, 'CN-20260825-4094C8', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'paid', '000GU2P', '500.00', '500.00', '1000.00', 'confirmed', NULL, '2026-08-25 01:28:52', '2026-08-25 01:29:19', NULL, NULL),
(47, 3, 'CN-20260825-C63C41', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'pending', NULL, '500.00', '500.00', '1000.00', 'pending', NULL, '2026-08-25 01:34:04', NULL, NULL, NULL),
(48, 3, 'CN-20260825-633699', 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', 'Langakhel, Lalitpur', 'eSewa', 'paid', '000GU37', '500.00', '500.00', '1000.00', 'confirmed', NULL, '2026-08-25 01:36:06', '2026-08-25 01:38:24', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_model_number` varchar(50) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `selected_strap_size` varchar(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_model_number`, `product_image`, `selected_strap_size`, `quantity`, `price`, `total`) VALUES
(1, 3, NULL, 'Rolex', 'RLX-12324', NULL, 'M', 2, '100.00', '200.00'),
(2, 4, NULL, 'Rolex', 'RLX-12324', NULL, 'M', 1, '100.00', '100.00'),
(3, 5, NULL, 'Rolex', 'RLX-12324', NULL, 'S', 1, '100.00', '100.00'),
(5, 7, NULL, 'Rolex', 'RLX-12324', NULL, 'M', 1, '100.00', '100.00'),
(6, 8, NULL, 'Rolex', 'RLX-12324', NULL, 'L', 1, '100.00', '100.00'),
(7, 9, NULL, 'Rolex', 'RLX-12324', NULL, 'S', 1, '100.00', '100.00'),
(8, 10, NULL, 'Rolex', 'RLX-12324', NULL, 'L', 1, '100.00', '100.00'),
(9, 11, NULL, 'Rolex', 'RLX-12324', NULL, 'S', 1, '100.00', '100.00'),
(10, 12, NULL, 'Rolex', 'RLX-12324', NULL, 'L', 1, '100.00', '100.00'),
(11, 13, NULL, 'Rolex', 'RLX-12324', NULL, 'L', 1, '100.00', '100.00'),
(12, 14, NULL, 'Rolex', 'RLX-12324', NULL, 'S', 1, '100.00', '100.00'),
(13, 15, NULL, 'Rolex', 'RLX-12324', NULL, 'M', 1, '100.00', '100.00'),
(14, 16, NULL, 'Rolex', 'RLX-12324', NULL, 'L', 1, '100.00', '100.00'),
(15, 17, NULL, 'Rolex', 'RLX-12324', NULL, 'L', 1, '100.00', '100.00'),
(16, 18, NULL, 'Rolex', 'RLX-12324', NULL, 'M', 1, '100.00', '100.00'),
(17, 19, NULL, 'Rolex', 'RLX-12324', NULL, 'L', 1, '100.00', '100.00'),
(18, 20, NULL, 'Rolex', 'RLX-12324', NULL, 'M', 1, '100.00', '100.00'),
(19, 21, NULL, 'Rolex', 'RLX-12324', NULL, 'M', 1, '100.00', '100.00'),
(20, 22, NULL, 'Rolex', 'RLX-12324', NULL, 'M', 1, '100.00', '100.00'),
(21, 23, NULL, 'Rolex', 'RLX-12324', 'uploads/products/rolex-1781272350-2.webp', 'M', 1, '1000.00', '1000.00'),
(22, 24, NULL, 'Rolex', 'RLX-12324', 'uploads/products/rolex-1781272350-2.webp', 'L', 1, '1000.00', '1000.00'),
(23, 25, NULL, 'Rolex', 'RLX-12324', 'rolex-1781501746-0.jpg', 'S', 1, '1200.00', '1200.00'),
(24, 26, NULL, 'Rolex', 'RLX-12324', 'rolex-1781502987-0.jpg', 'M', 2, '1000.00', '2000.00'),
(25, 27, 44, 'Yangtze Jiangtun', '01 733 7789 4197-Set', 'yangtze-jiangtun-1783576959-0.jpg', 'M', 1, '2900.00', '2900.00'),
(26, 27, 51, 'Divers Sixty-Five', '01 733 7771 3155-07 3 19 02BRS', 'divers-sixty-five-1783578402-0.jpg', 'M', 1, '3100.00', '3100.00'),
(27, 27, 4, 'Expedition Pioneer Titanium Automatic GMT', 'TW2W53000', 'expedition-pioneer-titanium-automatic-gmt-1783326002-0.webp', NULL, 1, '629.00', '629.00'),
(28, 28, 44, 'Yangtze Jiangtun', '01 733 7789 4197-Set', 'yangtze-jiangtun-1783576959-0.jpg', 'XL', 1, '2999.00', '2999.00'),
(29, 29, 44, 'Yangtze Jiangtun', '01 733 7789 4197-Set', 'yangtze-jiangtun-1783576959-0.jpg', 'XL', 1, '2899.99', '2899.99'),
(30, 29, 51, 'Divers Sixty-Five', '01 733 7771 3155-07 3 19 02BRS', 'divers-sixty-five-1783578402-0.jpg', 'XL', 1, '3100.00', '3100.00'),
(31, 30, 51, 'Divers Sixty-Five', '01 733 7771 3155-07 3 19 02BRS', 'divers-sixty-five-1783578402-0.jpg', 'M', 1, '3100.00', '3100.00'),
(32, 31, 40, 'Daytona', '116588SACO', 'daytona-1783575807-0.webp', 'XL', 1, '326025.00', '326025.00'),
(33, 32, 51, 'Divers Sixty-Five', '01 733 7771 3155-07 3 19 02BRS', 'divers-sixty-five-1783578402-0.jpg', 'S', 1, '3100.00', '3100.00'),
(34, 33, 47, 'Aquis HÃ¶lstein Edition 2023', '01 400 7769 4188-Set', 'aquis-h-lstein-edition-2023-1783578155-0.jpg', 'L', 1, '4500.00', '4500.00'),
(35, 34, 44, 'Yangtze Jiangtun', '01 733 7789 4197-Set', 'yangtze-jiangtun-1783576959-0.jpg', 'XL', 1, '2899.99', '2899.99'),
(36, 35, 47, 'Aquis HÃ¶lstein Edition 2023', '01 400 7769 4188-Set', 'aquis-h-lstein-edition-2023-1783578155-0.jpg', 'XL', 1, '4500.00', '4500.00'),
(37, 36, 26, 'MTG', 'MTG-B4000B-1A', 'mtg-1783571894-0.jpg', 'L', 1, '1600.00', '1600.00'),
(38, 37, 49, 'Aquis Chronograph', '01 771 7793 4155-07 8 23 01PEB', 'aquis-chronograph-1783578256-0.webp', 'L', 1, '5000.00', '5000.00'),
(39, 38, 43, 'Cosmograph Daytona', '126515LN', 'cosmograph-daytona-1783576205-0.webp', 'XL', 1, '81250.00', '81250.00'),
(41, 40, 1, 'Galaxy Watch Ultra (2025)', 'SM-L705F', 'galaxy-watch-ultra-2025-1783323585-5.png', NULL, 1, '649.99', '649.99'),
(42, 40, 51, 'Divers Sixty-Five', '01 733 7771 3155-07 3 19 02BRS', 'divers-sixty-five-1783578402-0.jpg', 'M', 1, '3100.00', '3100.00'),
(43, 41, 45, 'Oris X RedBar', '01 733 7795 4018-Set', 'oris-x-redbar-1783577190-0.webp', 'L', 1, '2950.00', '2950.00'),
(44, 42, 44, 'Yangtze Jiangtun', '01 733 7789 4197-Set', 'yangtze-jiangtun-1783576959-0.jpg', 'S', 1, '2899.99', '2899.99'),
(45, 43, 30, 'GM-S2110SR', 'GM-S2110SR-7A', 'gm-s2110sr-1783573010-0.jpg', NULL, 1, '210.00', '210.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `model_number` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `gender` enum('Men','Women','Unisex','Kids') NOT NULL,
  `strap_material` varchar(50) DEFAULT NULL,
  `strap_color` varchar(50) DEFAULT NULL,
  `strap_adjustable` tinyint(1) DEFAULT 1,
  `strap_length_mm` decimal(4,1) DEFAULT NULL,
  `strap_size_options` varchar(255) DEFAULT NULL,
  `dial_shape` enum('Round','Square','Rectangular','Oval','Tonneau') DEFAULT 'Round',
  `dial_color` varchar(50) DEFAULT NULL,
  `case_diameter_mm` decimal(4,1) DEFAULT NULL,
  `case_material` varchar(50) DEFAULT NULL,
  `water_resistance` varchar(20) DEFAULT NULL,
  `movement_type` enum('Automatic','Quartz','Mechanical','Solar','Kinetic','Digital','Smartwatch','-') NOT NULL DEFAULT '-',
  `features` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_expensive` tinyint(1) DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `description` text DEFAULT NULL,
  `warranty_years` int(11) DEFAULT 2,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `model_number`, `name`, `slug`, `brand_id`, `gender`, `strap_material`, `strap_color`, `strap_adjustable`, `strap_length_mm`, `strap_size_options`, `dial_shape`, `dial_color`, `case_diameter_mm`, `case_material`, `water_resistance`, `movement_type`, `features`, `price`, `is_expensive`, `stock_quantity`, `is_active`, `description`, `warranty_years`, `created_at`, `updated_at`) VALUES
(1, 'SM-L705F', 'Galaxy Watch Ultra (2025)', 'galaxy-watch-ultra-2025', 11, 'Unisex', 'Fluoroelastomer', 'Titanium Silver', 0, '220.0', NULL, 'Round', 'Black', '47.0', 'Titanium', '10ATM', 'Smartwatch', 'LTE, eSIM, Wear OS Powered by Samsung, One UI Watch, Exynos W1000 (3nm) Processor, 2GB RAM, 64GB Storage, 1.5-inch Super AMOLED Display (480Ã—480), Sapphire Crystal Glass, Always-On Display, Bluetooth 5.3, Wi-Fi 2.4/5GHz, NFC, Dual-Frequency GPS (L1+L5), Glonass, Beidou, Galileo, Samsung BioActive Sensor, Optical Heart Rate Sensor, ECG, Bioelectrical Impedance Analysis (BIA), Skin Temperature Sensor, Accelerometer, Gyroscope, Barometer, Compass, Light Sensor, Sleep Tracking, Body Composition, Blood Oxygen (SpO2), Energy Score, Running Coach, Fall Detection, Emergency SOS, Quick Button, 590mAh Battery, Wireless Fast Charging, IP68, MIL-STD-810H\r\nprice:', '649.99', 1, 4, 1, 'Samsung Galaxy Watch Ultra (2025) LTE is a premium rugged smartwatch featuring a 47 mm Grade 4 titanium case, sapphire crystal display, 64GB storage, advanced Galaxy AI health tracking, dual-frequency GPS, 10ATM water resistance, IP68 certification, and military-grade durability. Designed for outdoor adventures, fitness, and everyday smart connectivity.', 2, '2026-06-23 06:13:51', '2026-08-23 11:51:40'),
(2, 'SM-L315F', 'Samsung Watch7 (LTE, 44m)', 'samsung-watch7-lte-44m', 11, 'Unisex', 'Fluoroelastomer', 'Green', 0, '205.0', NULL, 'Round', 'Black', '44.0', 'Armor Aluminum', '5ATM', 'Smartwatch', 'LTE, eSIM, Wear OS Powered by Samsung, One UI Watch, Exynos W1000 (3nm) Processor, 2GB RAM, 32GB Storage, 1.5-inch Super AMOLED Display (480 Ã— 480), Sapphire Crystal Glass, Always-On Display, Bluetooth 5.3, Wi-Fi 2.4/5GHz, NFC, Dual-Frequency GPS (L1+L5), GPS, GLONASS, Galileo, BeiDou, Samsung BioActive Sensor, Optical Heart Rate Sensor, ECG, Bioelectrical Impedance Analysis (BIA), Skin Temperature Sensor, Accelerometer, Gyroscope, Barometer, Compass, Light Sensor, Sleep Tracking, Blood Oxygen (SpO2), Body Composition, Energy Score, AI Wellness Tips, Fall Detection, Emergency SOS, 425mAh Battery, Fast Wireless Charging, IP68, MIL-STD-810H', '329.99', 0, 10, 1, 'Samsung Galaxy Watch7 LTE (44 mm) is a premium smartwatch featuring a 44 mm Armor Aluminum case, 1.5-inch Super AMOLED display protected by Sapphire Crystal, Exynos W1000 3nm processor, 32GB storage, advanced Galaxy AI health tracking, dual-frequency GPS, LTE connectivity, and Wear OS powered by Samsung. It is designed for fitness, health monitoring, and everyday smart connectivity.', 2, '2026-07-06 02:10:56', '2026-07-06 02:10:56'),
(3, 'TW6A06000', 'Waterbury Heritage America 250', 'waterbury-heritage-america-250', 16, 'Men', 'Leather', 'Black', 0, '205.0', NULL, 'Round', 'Black', '39.0', 'Stainless Steel', '50m', 'Quartz', 'Meca-Quartz Chronograph, Tachymeter Bezel, Anti-Reflective Mineral Crystal, Luminant Hands, Chronograph Stopwatch, Quick-Release Leather Strap, Anniversary Edition, SR936SW Battery, Buckle Clasp', '319.00', 0, 6, 1, 'The Timex Waterbury Heritage America 250 is a limited-edition chronograph created to commemorate the 250th anniversary of the United States. It features a 39 mm brushed and polished stainless steel case, black panda-style dial with a commemorative America 250 logo, blue tachymeter bezel, red and blue chronograph pushers, anti-reflective mineral crystal, meca-quartz chronograph movement, and a perforated black leather strap with quick-release spring bars.', 2, '2026-07-06 02:20:48', '2026-07-06 07:54:29'),
(4, 'TW2W53000', 'Expedition Pioneer Titanium Automatic GMT', 'expedition-pioneer-titanium-automatic-gmt', 16, 'Men', 'Silicone', 'Black', 0, '210.0', NULL, 'Round', 'Black', '41.0', 'Titanium', '200m', 'Automatic', 'Japanese Automatic GMT Movement, Dual Time Zone Display, Bidirectional 24-Hour Bezel, Sapphire Crystal, Exhibition Case Back, Luminous Hands and Markers, Date Display, Screw-Down Crown, Quick-Release Strap, Shock Resistant', '629.00', 1, 0, 1, 'The Timex Expedition Pioneer Titanium Automatic GMT features a 41 mm titanium case, Japanese automatic GMT movement, sapphire crystal, 200 m water resistance, screw-down crown, dual time zone functionality, and a durable black silicone strap designed for travel and outdoor use.', 2, '2026-07-06 02:29:46', '2026-07-15 04:35:01'),
(5, 'TW2Y63500', 'Expedition Pioneer Titanium Automatic GMT (Titanium)', 'expedition-pioneer-titanium-automatic-gmt-titanium', 16, 'Men', 'Titanium', 'Titanium', 1, NULL, 'S, M, L, XL', 'Round', 'Black', '41.0', 'Titanium', '200m', 'Automatic', 'Japanese Automatic GMT Movement, Dual Time Zone Display, Bidirectional 24-Hour Bezel, Sapphire Crystal, Exhibition Case Back, Luminous Hands and Markers, Date Display, Screw-Down Crown, Titanium Bracelet, Butterfly Deployant Clasp', '729.00', 1, 1, 1, 'The Timex Expedition Pioneer Titanium Automatic GMT features a 41 mm titanium case and bracelet, Japanese automatic GMT movement, sapphire crystal, 200 m water resistance, screw-down crown, dual time zone functionality, and a lightweight titanium bracelet designed for durability and comfort.', 2, '2026-07-06 02:39:28', '2026-07-06 07:51:07'),
(6, 'TW2Y70200', 'Waterbury Ace Fly-Back Chronograph', 'waterbury-ace-fly-back-chronograph', 16, 'Men', 'Stainless Steel', 'Silver-Tone', 1, NULL, 'S, M, L, XL', 'Round', 'Black', '43.0', 'Stainless Steel', '100m', 'Quartz', 'Fly-Back Chronograph, Timex Intelligent Quartz Movement, Dual Time Zone Display, Tachymeter Scale, Luminous Hands and Hour Markers, Mineral Crystal, Quick-Release Spring Bars, Deployant Clasp, Brushed & Polished Stainless Steel Case, Stainless Steel Bracelet', '319.00', 0, 19, 1, 'The Waterbury Ace Fly-Back Chronograph is inspired by classic pilot watches and powered by Timex\'s proprietary Intelligent Quartz fly-back chronograph movement developed in Pforzheim, Germany. It features a 43mm brushed and polished stainless steel case, a black dial with retrograde sub-dials accented by yellow hands, a second time zone display, tachymeter scale, luminous hands and markers, mineral crystal, quick-release stainless steel bracelet with deployant clasp, and 100-meter water resistance.', 1, '2026-07-06 07:56:50', '2026-07-07 23:56:18'),
(7, 'TW2Y77000', 'Harry Potter x Timex Weekender Sorting Hat', 'harry-potter-x-timex-weekender-sorting-hat', 16, 'Unisex', 'Leather', 'Brown', 0, '190.0', NULL, 'Round', 'Brown-Tone', '37.0', 'Stainless Steel', '50m', 'Quartz', 'Harry Potter Special Edition, Sorting Hat Artwork, INDIGLO Backlight, Luminant Hands, Mineral Glass Crystal, Quick-Release Leather Strap, Buckle Clasp, Official Harry Potter Collaboration', '179.00', 0, 15, 1, 'Celebrate the magic of Hogwarts with the Harry Potter x Timex Weekender Sorting Hat watch. Featuring a 37mm low-lead brass case, cream dial with Sorting Hat artwork, reliable quartz movement, INDIGLO backlight, mineral glass crystal, and a brown genuine leather quick-release strap. This special edition combines timeless Timex craftsmanship with iconic Harry Potter design, making it an ideal collectible for fans and everyday wear.', 1, '2026-07-06 08:11:01', '2026-07-06 08:32:13'),
(8, 'TW5M69000', 'World Time Digital', 'world-time-digital', 16, 'Unisex', 'Stainless Steel', 'Gold-Tone', 1, NULL, 'XS,S,M,L,XL', 'Square', 'Digital', '40.0', 'Resin', '100m', 'Digital', 'World Time (40 Cities), Chronograph, Day Display, Date Display, Countdown Timer, 5 Alarms, INDIGLO Light-Up Dial, Acrylic Crystal, Fold-Over Clasp, Quartz Digital Movement', '115.00', 0, 20, 1, 'The World Time Digital combines everyday durability with advanced digital functionality. It features a lightweight 40mm resin case, gold-tone stainless steel bracelet with removable links, world time covering 40 cities, chronograph, countdown timer, five alarms, day and date display, INDIGLO light-up dial, acrylic crystal, quartz digital movement, and 100-meter water resistance.', 1, '2026-07-06 08:17:13', '2026-07-06 08:17:13'),
(9, 'TW2Y19200', 'Waterbury Heritage Chronograph', 'waterbury-heritage-chronograph', 16, 'Men', 'Stainless Steel', 'Silver-Tone', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Silver-Tone', '39.0', 'Stainless Steel', '50m', 'Quartz', 'Chronograph, Tachymeter Bezel, Date Display, Mineral Crystal, Luminous Hands, Stainless Steel Bracelet, Fold-Over Clasp, Applied Hour Markers', '359.00', 0, 20, 1, 'The Waterbury Heritage Chronograph blends vintage-inspired styling with modern reliability. It features a polished 39mm stainless steel case, blue dial with chronograph sub-dials, tachymeter bezel, date display, quartz chronograph movement, luminous hands, mineral crystal, stainless steel bracelet, and 50-meter water resistance.', 1, '2026-07-06 08:17:47', '2026-07-07 23:56:06'),
(10, 'TW2Y50300', 'Peanuts x Timex Marlin Automatic Americana', 'peanuts-x-timex-marlin-automatic-americana', 16, 'Unisex', 'Leather', 'Black', 0, '205.0', NULL, 'Round', 'Silver-Tone', '40.0', 'Stainless Steel', '50m', 'Automatic', 'Automatic Movement, Exhibition Case Back, Peanuts Character Dial Artwork, Snoopy Design Elements, Sapphire Crystal, Brown Leather Strap, Stainless Steel Case, Date Display, Luminous Hands', '359.00', 0, 14, 1, 'The Peanuts x Timex Marlin Automatic Americana celebrates the iconic Peanuts universe with a playful Snoopy-themed dial. It features a 40mm stainless steel case, cream dial with Peanuts artwork, automatic movement, sapphire crystal, exhibition case back, brown leather strap, luminous hands, and 50-meter water resistance.', 1, '2026-07-06 08:18:16', '2026-07-07 23:55:54'),
(11, 'TW3A01500', 'END. x Timex 1981 Reissue Inspired', 'end-x-timex-1981-reissue-inspired', 16, 'Men', 'Stainless Steel', 'Silver', 1, NULL, 'XS,S,M,L,XL', 'Square', 'Blue', '35.0', 'Stainless Steel', '30m', '-', 'Digital Display, Alarm Function, Chronograph, Retro 1980s Design, INDIGLO Night Light, Stainless Steel Bracelet, Day-Date Display, Lightweight Case', '199.00', 0, 25, 1, 'The END. x Timex 1981 Reissue Inspired is a modern reinterpretation of a classic digital watch from the 1980s. It features a 35mm lightweight case, retro digital display, INDIGLO backlight, chronograph, alarm functions, stainless steel bracelet, and vintage-inspired design.', 1, '2026-07-06 08:18:31', '2026-07-06 08:38:45'),
(12, 'TW2Y49900', 'Marlin', 'marlin', 16, 'Women', 'Fabric', 'Blue', 0, '190.0', NULL, 'Round', 'Cream', '34.0', 'Stainless Steel', '30m', 'Quartz', 'Acrylic Crystal, Stainless Steel Case, Double-Layer Fabric Slip-Thru Strap, Luminous Hands, Water Resistant 30m', '169.00', 0, 14, 1, 'The Marlin is a refreshed 34mm women\'s watch featuring a polished gold-tone stainless-steel case and a clean cream dial with a mix of stick and Arabic numerals. Its luminant handset improves nighttime readability, while a double-layered, slip-thru fabric strap in a preppy striped pattern offers a comfortable, secure fit for all-day wear.', 2, '2026-07-06 23:24:26', '2026-07-06 23:43:07'),
(13, 'TW2Y26100', 'Transcend', 'transcend', 16, 'Women', 'Stainless Steel', 'Gold-Tone', 1, NULL, 'XS,S,M,L', 'Square', 'Silver-Tone', '22.0', 'Low Lead Brass', '30m', 'Quartz', 'Mineral Crystal, Polished Case, Stainless Steel Mesh Band, Self-Adjust Clasp, Sunray Dial', '159.00', 0, 8, 1, 'Transcend is a minimalist petite women\'s watch with a fresh rectangular 22mm case, all-polished finishing, and a stainless-steel mesh band. The silver sunray dial with alpha hands and simple markers gives it a clean, versatile look suitable for everyday pairing.', 2, '2026-07-06 23:26:09', '2026-07-06 23:44:57'),
(14, 'TW2Y70700', 'Cavatina Mini', 'cavatina-mini', 16, 'Women', 'Stainless Steel', 'Silver-Tone', 0, '16.0', NULL, 'Round', 'White', '16.0', 'Stainless Steel', '30m', 'Quartz', 'Mineral Crystal, Stainless Steel Case, Expansion Band with Removable Links, Ring Watch Design', '129.00', 0, 22, 1, 'Cavatina Mini transforms the brand\'s elegant oval silhouette into a ring watch. Its petite 16mm stainless-steel case houses a white dial with clean Arabic numerals and a script-style logo, paired with a fully brushed recycled stainless-steel expansion band designed to slip comfortably onto the finger.', 2, '2026-07-06 23:27:33', '2026-07-06 23:48:30'),
(15, 'TW2U92900', 'Transcend-TM', 'transcend-tm', 16, 'Women', 'Stainless Steel', 'Stainless Steel', 1, NULL, 'XS,S,M,L', 'Round', 'Silver-Tone', '31.0', 'Low Lead Brass', '50m', 'Quartz', 'Mineral Crystal, Polished Silver-Tone Case, Quick-Release Stainless Steel Mesh Band, Fold-Over Clasp, Dimensional Crystal Dial Markers, Sunray Dial', '139.00', 0, 12, 1, 'This ultra-thin Transcend watch pairs an elegant sunray dial with a polished silver-tone case in a subtle 31mm size, offering understated sophistication with a beach-ready feel. Dimensional dial markers set with crystals add a touch of sparkle, while the sleek quick-release stainless-steel mesh band lets you swap straps in seconds without tools.', 2, '2026-07-06 23:29:47', '2026-07-06 23:49:21'),
(16, 'TW2Y77400', 'Q Timex Continental Mini', 'q-timex-continental-mini', 16, 'Women', 'Stainless Steel', 'Silver-Tone', 1, NULL, 'XS,S,M,L', 'Round', 'Pink', '24.0', 'Stainless Steel', '50m', 'Quartz', 'Mineral Crystal, Brushed/Polished Case, Adjustable Stainless Steel Bracelet, Luminant Hands and Markers', '189.00', 0, 11, 1, 'This mini edition of the Q Timex Continental brings the collection\'s standout style into a compact 24mm stainless-steel case. Its textured dial catches the light beautifully, with luminant hands and markers for low-light visibility, finished with a brushed stainless-steel bracelet for a polished everyday look.', 2, '2026-07-06 23:30:59', '2026-07-06 23:50:23'),
(17, 'TW2W87400', 'Timex Legacy', 'timex-legacy', 16, 'Unisex', 'Stainless Steel', 'Two-Tone', 1, NULL, 'XS,S,M,L', 'Round', 'Green', '36.0', 'Stainless Steel', '50m', 'Quartz', 'Mineral Crystal, Brushed/Polished Case, Adjustable Two-Tone Bracelet, Fluted Bezel, Faceted Applied Indices', '189.00', 0, 19, 1, 'Timex Legacy is a fresh take on a traditional watch with easy-to-wear 36mm proportions. It showcases a rich emerald sunray dial with faceted applied indices and an elegantly fluted bezel, paired with a brushed and polished two-tone stainless-steel bracelet and case for everyday versatility.', 2, '2026-07-06 23:31:52', '2026-07-06 23:51:32'),
(18, 'TW2W78300', 'Cavatina', 'cavatina', 16, 'Women', 'Leather', 'Black', 0, '190.0', NULL, 'Oval', 'White', '19.0', 'Low Lead Brass', '30m', 'Quartz', 'Mineral Crystal, Domed Crystal, Roman Numeral Dial, Petite Tapered Leather Strap, Polished Case', '129.00', 0, 6, 1, 'This redesigned Cavatina keeps its petite roots with a polished 19mm oval case that whispers timeless elegance. A delicately domed mineral crystal complements sophisticated Roman numerals on the clean white dial, finished with a black crocodile-grain tapered leather strap for a feminine, on-trend look.', 2, '2026-07-06 23:33:32', '2026-07-06 23:52:28'),
(19, 'TW2Y68300', 'Q Timex Continental Mini-Stainless Steel', 'q-timex-continental-mini-stainless-steel', 16, 'Women', 'Stainless Steel', 'Gold-Tone', 1, NULL, 'XS,S,M,L', 'Round', 'Gold-Tone', '24.0', 'Stainless Steel', '50m', 'Quartz', 'Mineral Crystal, Brushed/Polished Case, Adjustable Stainless Steel Bracelet, Luminant Hands and Markers', '209.00', 0, 5, 1, 'This mini edition of the Q Timex Continental delivers the collection\'s standout style in a compact 24mm gold-tone stainless-steel case. Its textured champagne dial catches the light beautifully, with luminant hands and markers, finished with a brushed stainless-steel bracelet for a polished, versatile look.', 2, '2026-07-06 23:34:19', '2026-07-06 23:54:02'),
(20, 'TW2W91900', 'Timex Time Machines', 'timex-time-machines', 16, 'Kids', 'Silicone', 'Black', 1, NULL, 'XS,S', 'Round', 'Black', '30.0', 'Resin', '30m', 'Quartz', 'Acrylic Crystal, Resin Case, Soft Silicone Rubber Strap, Crown Protector, Easy-to-Read Numbers', '49.00', 0, 27, 1, 'This durable kids\' watch makes timekeeping fun with easy-to-read numbers, time-teaching hour and minute hands, a super-soft silicone strap, and a sturdy resin case with a crown protector, all built to handle active little wrists.', 2, '2026-07-06 23:35:20', '2026-07-06 23:35:20'),
(21, 'T71912', 'Timex Time Machines-Digital', 'timex-time-machines-digital', 16, 'Kids', 'Elastic Fabric', 'Green', 1, NULL, 'XS,S,M', 'Round', 'Digital', '34.0', 'Resin', '30m', 'Digital', 'Acrylic Crystal, Chronograph Function, INDIGLO Light-Up Dial, Stretch Fabric Strap, Green Camo Case', '55.00', 0, 9, 1, 'Kids love this digital watch for its INDIGLO night-light that lets them tell time in the dark, plus a super easy-to-read display. A grime-resistant green camo elastic fabric strap and chronograph function round out a fun, durable design that parents will appreciate too.', 2, '2026-07-06 23:36:07', '2026-07-06 23:56:28'),
(22, 'TW2Y51100', 'Peanuts x Timex Youth Spring', 'peanuts-x-timex-youth-spring', 16, 'Kids', 'Silicone', 'Blue', 1, NULL, 'XS,S', 'Round', 'White', '30.0', 'ABS', '30m', 'Quartz', 'Acrylic Crystal, Soft Silicone Strap, Snoopy and Woodstock Dial Illustration, Arabic Numeral Markers, Bright Yellow Second Hand', '65.00', 0, 16, 1, 'Snoopy and Woodstock hunt for eggs on the white dial of this playful Peanuts watch for kids. Easy-to-read Arabic numeral markers, a bright yellow second hand, a lightweight resin case, and a super-soft silicone strap help children learn to tell time in style.', 2, '2026-07-06 23:37:26', '2026-07-06 23:37:26'),
(23, 'TW2Y39500', 'Peanuts x Timex Weekender Snoopy Santa Hat', 'peanuts-x-timex-weekender-snoopy-santa-hat', 16, 'Unisex', 'Fabric', 'Red', 1, NULL, 'S,M,L', 'Round', 'White', '31.0', 'Brass and Low Lead Brass', '30m', 'Quartz', 'Mineral Crystal, Slip-Thru Fabric Strap, Snoopy in Santa Hat Illustration, String Light Pattern Strap, Green Arabic Numerals', '99.00', 0, 13, 1, 'This best-selling Peanuts holiday watch arrives in a smaller 31mm case. A charming red slip-thru fabric strap decorated with a string-light pattern pairs with a white dial featuring Snoopy in a Santa hat and Woodstock, bringing festive fun to the holiday season.', 2, '2026-07-06 23:37:46', '2026-07-06 23:37:46'),
(24, 'MTPS110-2AV', 'MTP', 'mtp', 4, 'Men', 'Resin', 'Blue', 1, NULL, 'M, L, XL', 'Round', 'Digital', '46.9', 'Stainless Steel', '100m', 'Solar', 'Solar Powered, Day Display, Date Display, Mineral Glass, Stainless Steel Case, Resin Strap, Luminous Hands and Hour Markers, Screw-Down Case Back, Analog Display', '160.00', 0, 4, 1, 'A diver-inspired solar-powered analog watch designed for everyday wear, featuring a blue dial with day and date display, stainless steel case, durable resin strap, mineral glass, luminous hands and markers, and 100-meter water resistance for dependable daily performance.', 2, '2026-07-06 23:38:13', '2026-07-07 22:57:35'),
(25, 'MF0J4LW/A', 'Apple Watch Ultra 3', 'apple-watch-ultra-3', 10, 'Unisex', 'Fluoroelastomer', 'Black', 0, '205.0', NULL, 'Square', 'Black', '49.0', 'Black Titanium', '100m', 'Smartwatch', 'GPS + Cellular, Always-On Retina LTPO3 OLED Display, S10 Chip, 3000 nits Brightness, ECG, Blood Oxygen Monitoring, Heart Rate Monitoring, Sleep Tracking, Sleep Apnea Notifications, Temperature Sensor, Dual-Frequency GPS, Emergency SOS, Crash Detection, Fall Detection, Siren, Depth Gauge to 40m, Water Temperature Sensor, Fast Charging, Up to 42 Hours Battery Life, Ocean Band, 5G Capable', '799.00', 1, 12, 1, 'Apple Watch Ultra 3 is Apple\'s premium rugged smartwatch built for outdoor adventures and everyday performance. It features a durable 49mm black titanium case, an adjustable black Ocean Band, GPS and cellular connectivity, advanced health and fitness tracking, dual-frequency GPS, emergency safety features, fast charging, and a bright Always-On Retina display designed for demanding environments.', 2, '2026-07-07 07:12:45', '2026-07-07 07:15:26'),
(26, 'MTG-B4000B-1A', 'MTG', 'mtg', 5, 'Men', 'Resin', 'Black', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Black', '45.3', 'Carbon / Stainless steel', '200m', 'Solar', 'TRIPLE G RESIST, Carbon Core Guard, Tough Solar, Multi-Band 6, Bluetooth, Sapphire Crystal with anti-reflective coating, World Time, Stopwatch, Timer, Alarm, LED Light, Neobrite', '1600.00', 1, 4, 1, 'The MTG-B4000B-1A features a dual-core guard structure developed with AI assistance and a hybrid frame combining carbon fiber and stainless steel. The multi-layered carbon frame is accented with vibrant red details and paired with a durable soft urethane band for a dynamic and sporty design. This timepiece offers Tough Solar power, Multi-Band 6 radio control, and Bluetooth smartphone connectivity.', 2, '2026-07-08 22:45:38', '2026-08-21 03:29:15'),
(27, 'GWG-B1000TLC-1A', 'MUDMASTER', 'mudmaster', 5, 'Men', 'Bio-based Resin', 'Black', 0, '205.0', NULL, 'Round', 'Black', '52.1', 'Bio-based Carbon/Stainless Steel', '200m', 'Solar', 'Triple Sensor, Mud Resistant, Carbon Core Guard, Multi-Band 6, Bluetooth, Sapphire Crystal, Dual LED Light, World Time, Stopwatch, Timer, Alarm, Position Indicator', '960.00', 1, 1, 1, 'The GWG-B1000TLC-1A is a Master of G MUDMASTER collaboration with Team Land Cruiser Toyota Auto Body, inspired by the harsh night stages of the Dakar Rally. It features a metal exterior and reinforced carbon fiber resin case, triple sensor technology, and a position indicator function. The black design is accented with gold metal rings and multi-colored hands inspired by race car navigation systems.', 2, '2026-07-08 22:55:22', '2026-07-08 23:01:20'),
(28, 'GA-110GB-1A', 'GA-110', 'ga-110', 5, 'Men', 'Resin', 'Black', 0, '205.0', NULL, 'Round', 'Black', '51.2', 'Resin', '200m', 'Quartz', 'Shock Resistant, Magnetic Resistant, Mineral Glass, 1/1000-second Stopwatch, Speed Indicator, LED Light, Auto Light Switch', '165.00', 0, 12, 1, 'The GA-110GB-1A features a three-dimensional dial with a speedometer and layered parts, combining a classic black G-SHOCK design with vibrant gold accents. The glossy paint and mirror-finish textures create a stylish look that fuses technology with advanced design.', 2, '2026-07-08 23:01:36', '2026-07-08 23:05:58'),
(29, 'GMA-P2100ST-4A', 'GMA-P2100ST', 'gma-p2100st', 5, 'Women', 'Bio-based Resin', 'Pink', 0, '190.0', NULL, 'Round', 'Pink', '40.2', 'Resin/Bio-based Resin', '200m', 'Quartz', 'Shock Resistant, Mineral Glass, Dual LED Light, World Time, Stopwatch, Timer, Alarm, Neobrite, Mute Function', '145.00', 0, 8, 1, 'The GMA-P2100ST-4A features a pearl-like polarized coating with a matte finish on the bezel and band, while the dial showcases a beautiful matte metallic color. This minimalist design with a subtle glow is complemented by a compact 40.2mm case and bio-based resin construction.', 2, '2026-07-08 23:06:23', '2026-07-08 23:09:03'),
(30, 'GM-S2110SR-7A', 'GM-S2110SR', 'gm-s2110sr', 5, 'Women', 'Bio-based Resin', 'White', 0, '190.0', NULL, 'Round', 'Silver', '40.5', 'Resin/Stainless Steel', '200m', 'Quartz', 'Shock Resistant, Polarized Vapor Deposition Glass, World Time, Stopwatch, Timer, Alarm, Dual LED Light, Neobrite', '210.00', 0, 5, 1, 'The GM-S2110SR-7A features an octagonal stainless steel bezel with a silver-tone finish and a white bio-based resin band. The watch glass is treated with polarized vapor deposition for a summery color-changing effect. The compact design fits smaller wrists comfortably and offers a sophisticated, streamlined aesthetic.', 2, '2026-07-08 23:09:41', '2026-08-25 01:11:55'),
(31, 'MRG-B2000R-1A', 'MRG-B2000R', 'mrg-b2000r', 5, 'Men', 'Dura Soft Fluoro Rubber', 'Black', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Black', '49.8', 'Titanium', '200m', 'Solar', 'Tough Solar, Multi-Band 6, Bluetooth, Sapphire Crystal, Recrystallized Titanium Bezel, DLC Coating, World Time, Stopwatch, Timer, Alarm, LED Light', '3300.00', 1, 3, 1, 'The MRG-B2000R-1A features a recrystallized titanium bezel representing the crystalline patterns of Japanese swords, with a Durasoft band displaying a Bishamon tortoise shell pattern. The dial features a scale pattern and fan-shaped cuts, with indices reflecting the curvature of Japanese swords, all accented with kachi-iro coloring.', 2, '2026-07-08 23:13:16', '2026-07-08 23:16:51'),
(32, 'MRG-B5000B-1', 'MRG-B5000B', 'mrg-b5000b', 5, 'Men', 'Titanium', 'Black', 1, NULL, 'XS,S,M,L,XL', 'Square', 'Black', '43.2', 'Titanium', '200m', 'Solar', 'Tough Solar, Multi-Band 6, Bluetooth, Sapphire Crystal, Diamond-like Carbon Coating, Multi-guard Structure, World Time, Stopwatch, Timer, Alarm, LED Backlight', '4500.00', 1, 5, 1, 'The MRG-B5000B-1 is based on the first-ever G-SHOCK and crafted with state-of-the-art metals and meticulous finishes. The case and bezel are made of Ti64 super-hard titanium alloy with a diamond-like carbon coating for a brilliant black mirror finish, featuring an iconic brick pattern and red line on the dial.', 2, '2026-07-08 23:26:13', '2026-07-08 23:28:14'),
(33, 'MRG-BF1000B-1A', 'MRG-BF1000B', 'mrg-bf1000b', 5, 'Men', 'Dura Soft Fluoro Rubber', 'Black', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Black', '49.7', 'Titanium', '200m', 'Solar', 'Tough Solar, Multi-Band 6, Bluetooth, Sapphire Crystal, DLC Coating, Magnetic Resistant, Diving Mode, Tide Graph, Dive Log, World Time, Stopwatch, Timer, Alarm', '6500.00', 1, 4, 1, 'The MRG-BF1000B-1A is a premium full-metal MR-G FROGMAN dive watch with a titanium case and ISO 200-meter water resistance. It features an asymmetrical design, screw-lock case back, and is constructed from over 70 external components. The watch offers Bluetooth connectivity, tide graph display for over 3,000 locations, and a dive log function.', 2, '2026-07-08 23:27:33', '2026-07-08 23:29:46'),
(34, 'MRG-B2000KT-3A', 'MRG-B2000KT', 'mrg-b2000kt', 5, 'Men', 'Dura Soft Rubber', 'Dark Green', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Dark Blue-Green', '49.8', 'Titanium', '200m', 'Solar', 'Tough Solar, Multi-Band 6, Bluetooth, Sapphire Crystal, Phoenix Engraving, Emerald Screws, Deep-layer Hardening, AIP Coating, World Time, Stopwatch, Timer, Alarm', '8000.00', 1, 2, 1, 'The MRG-B2000KT-3A is a limited-edition timepiece featuring a hand-engraved phoenix design by master metalsmith Kobayashi Masao, with four bezel screws set with emeralds. The titanium case showcases a crystallized pattern, while the dark green DLC coating creates a kurogane-iro hue. This watch combines Japanese sword-making aesthetics with modern functionality.', 2, '2026-07-08 23:29:59', '2026-07-08 23:30:53'),
(35, '126710BLRO', 'GMT-Master II', 'gmt-master-ii', 1, 'Men', 'Oystersteel', 'Silver', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Black', '40.0', 'Oystersteel', '100m', 'Automatic', 'Caliber 3285, 70-hour power reserve, Chronometer, Bidirectional rotatable bezel, Cerachrom insert, Sapphire crystal, Date display', '27945.00', 1, 3, 1, 'The Rolex GMT-Master II 126710BLRO, known as the \"Pepsi,\" features a 40mm Oystersteel case with a red and blue Cerachrom bezel and a black dial. Powered by the Caliber 3285 automatic movement with a 70-hour power reserve, it offers a second time zone function via an independent 24-hour hand.', 2, '2026-07-08 23:38:12', '2026-07-08 23:40:27'),
(36, '118135', 'Day-Date 36', 'day-date-36', 1, 'Men', 'Leather', 'Black', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Chocolate', '36.0', 'Everose Gold', '100m', 'Automatic', 'Caliber 3155, 48-hour power reserve, Double Quickset, Day and date display, Fluted bezel, Sapphire crystal', '20700.00', 1, 2, 1, 'The Rolex Day-Date 36 118135 is a masterclass in tonal luxury, pairing a chocolate sunray dial with the enduring glow of Everose gold. Its 36mm architecture houses the Calibre 3155 with a 48-hour power reserve and Double Quickset complication.', 2, '2026-07-08 23:42:24', '2026-07-08 23:45:08'),
(37, '116595RBOW', 'Daytona Rainbow', 'daytona-rainbow', 1, 'Men', 'Rose Gold (RG)', 'Gold', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Black', '40.0', 'Rose Gold (RG)', '100m', 'Automatic', 'Caliber 4130, 72-hour power reserve, Chronograph, Tachymeter, Rainbow sapphire bezel, Diamond-set dial, Baguette-cut sapphire markers', '439875.00', 1, 1, 1, 'The Rolex Daytona 116595RBOW \"Rainbow\" is a 40mm rose gold stunner decked out in diamonds and topped with a bezel lined in rainbow-colored sapphires. The black lacquered dial features baguette-cut sapphire markers and three rose gold sub-dials.', 2, '2026-07-08 23:45:28', '2026-07-08 23:50:16'),
(38, '126718GRNR', 'GMT-Master II-GRNR', 'gmt-master-ii-grnr', 1, 'Men', 'Yellow Gold', 'Yellow', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Black', '40.0', 'Yellow Gold', '100m', 'Automatic', 'Caliber 3285, 70-hour power reserve, Chronometer, Bidirectional rotatable bezel, Grey and black Cerachrom insert, Sapphire crystal, Date display', '45540.00', 1, 2, 1, 'The Rolex GMT-Master II 126718GRNR features a 40mm 18 ct yellow gold case with a bidirectional rotatable bezel with a grey and black Cerachrom insert. Powered by the Caliber 3285 automatic movement, it offers a 70-hour power reserve and a second time zone function.', 2, '2026-07-08 23:53:18', '2026-07-08 23:54:42'),
(39, '116659SABR', 'Submariner', 'submariner', 1, 'Men', 'White Gold', 'Silver', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Blue', '40.0', 'White Gold', '300m', 'Automatic', 'Caliber 3135, 48-hour power reserve, Chronometer, Sapphire and diamond bezel, Luminous white gold markers, Date display, Sapphire crystal', '144900.00', 1, 1, 1, 'The Rolex Submariner 116659SABR is a diamond-set 40mm white gold watch with a blue dial and luminous white gold markers. The bezel features sapphire and diamond baguettes, and it is powered by the caliber 3135 automatic movement with a 48-hour power reserve.', 2, '2026-07-08 23:54:54', '2026-07-08 23:56:41'),
(40, '116588SACO', 'Daytona', 'daytona', 1, 'Men', 'Oysterflex', 'Black', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Black', '40.0', 'Yellow Gold', '100m', 'Automatic', 'Caliber 4130, 72-hour power reserve, Chronograph, Tachymeter, Square-cut orange sapphire-set bezel, Luminous gold hands, Sapphire crystal', '326025.00', 1, 0, 1, 'The Rolex Daytona 116588SACO is a masterpiece in motion with its solid 40mm yellow gold case and square-cut orange sapphire-set bezel. Secured with an Oysterflex strap, it is powered by the legendary caliber 4130 automatic movement with a 72-hour power reserve.', 2, '2026-07-08 23:56:20', '2026-08-20 17:22:37'),
(41, '126660', 'Sea-Dweller Deepsea', 'sea-dweller-deepsea', 1, 'Men', 'Steel', 'Silver', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Black', '44.0', 'Steel', '120+ ATM', 'Automatic', 'Caliber 3235, 70-hour power reserve, Chronometer, Helium escape valve, Unidirectional rotatable bezel, Cerachrom insert, Sapphire crystal, Date display', '14235.00', 1, 2, 1, 'The Rolex Sea-Dweller Deepsea is a professional diver\'s watch designed for saturation diving, featuring exceptional water resistance up to 3,900 meters. It is equipped with a helium escape valve, a unidirectional rotating bezel, and is powered by the Caliber 3235 movement.', 2, '2026-07-08 23:58:07', '2026-08-21 17:11:23'),
(42, '126508', 'Daytona-Gold', 'daytona-gold', 1, 'Men', 'Yellow Gold', 'Yellow', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Green', '40.0', 'Yellow Gold', '100m', 'Automatic', 'Caliber 4131, 72-hour power reserve, Chronograph, Tachymeter, Fixed yellow gold bezel, Chronometer, Sapphire crystal', '89010.00', 1, 2, 1, 'The Rolex Cosmograph Daytona 126508 features a 40mm 18 ct yellow gold case with a distinctive green sunburst dial. It is powered by the updated Caliber 4131 automatic movement and features a fixed tachymeter bezel in yellow gold.', 2, '2026-07-09 00:01:39', '2026-07-09 00:03:35'),
(43, '126515LN', 'Cosmograph Daytona', 'cosmograph-daytona', 1, 'Men', 'Rubber', 'Black', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Meteorite', '40.0', 'Rose Gold', '100m', 'Automatic', 'Caliber 4131, 72-hour power reserve, Chronograph, Tachymeter, Black Cerachrom bezel, Chronometer, Sapphire crystal', '81250.00', 1, 1, 1, 'The Rolex Cosmograph Daytona 126515LN features a 40mm 18 ct Everose gold case with a sleek black ceramic bezel. Powered by the Caliber 4131 automatic movement, it is secured on an Oysterflex bracelet with a patented cushion system.', 2, '2026-07-09 00:03:26', '2026-08-21 17:10:43'),
(44, '01 733 7789 4197-Set', 'Yangtze Jiangtun', 'yangtze-jiangtun', 15, 'Unisex', 'Stainless Steel', 'Silver', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Green', '43.5', 'Stainless Steel', '300m', 'Automatic', 'Caliber 733-1, 41-hour power reserve, Date display, Tungsten bezel, Sapphire crystal, Limited to 1249 pieces, Mother-of-pearl dial', '2899.99', 1, 3, 1, 'The Oris Aquis Yangtze Jiangtun Limited Edition is a special version of the Aquis Date dive watch with a mother-of-pearl dial. It supports the Changjiang Conservation Foundation in conserving the critically endangered Yangtze finless porpoise.', 2, '2026-07-09 00:13:03', '2026-08-24 12:44:42'),
(45, '01 733 7795 4018-Set', 'Oris X RedBar', 'oris-x-redbar', 15, 'Unisex', 'Rubber', 'Black', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Red', '39.0', 'Stainless Steel', '200m', 'Automatic', 'Caliber 733-1, 41-hour power reserve, Ceramic bezel, Sapphire crystal, Limited to 250 pieces, Gradient red dial, Additional metal bracelet', '2950.00', 1, 4, 1, 'The Oris X RedBar Limited Edition II is a 250-piece numbered edition based on the retro-inspired Divers watch. It features a gradient red dial and comes with a rubber strap and an additional bracelet.', 2, '2026-07-09 00:19:31', '2026-08-24 12:34:13'),
(46, '01 400 7790 4185-Set', 'Great Barrier Reef', 'great-barrier-reef', 15, 'Unisex', 'Stainless Steel', 'Silver', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Blue', '43.5', 'Stainless Steel', '300m', 'Automatic', 'Caliber 400, 120-hour power reserve, High anti-magnetism, 10-year service intervals, Date display, Tungsten bezel, Sapphire crystal, Limited to 2000 pieces', '4750.00', 1, 10, 1, 'The Oris Great Barrier Reef Limited Edition IV is a 2,000-piece limited edition based on the Aquis Date, inspired by the work of Resilient Reefs Foundation. It features a gradient blue dial and is powered by the Caliber 400 five-day automatic movement.', 2, '2026-07-09 00:21:40', '2026-07-09 00:23:48'),
(47, '01 400 7769 4188-Set', 'Aquis HÃ¶lstein Edition 2023', 'aquis-h-lstein-edition-2023', 15, 'Unisex', 'Stainless Steel', 'Silver', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Purple', '41.5', 'Stainless Steel', '300m', 'Automatic', 'Caliber 400, 120-hour power reserve, High anti-magnetism, 10-year service intervals, Ceramic bezel, Sapphire crystal, Limited to 250 pieces, Purple case back with Oris Bear', '4500.00', 1, 2, 1, 'The Oris Holstein Edition 2023 is a 250-piece limited edition based on the Aquis diver\'s watch. It features a first-ever purple dial, no-date Aquis, and a purple case back with a diving Oris Bear.', 2, '2026-07-09 00:35:24', '2026-08-21 03:08:46'),
(49, '01 771 7793 4155-07 8 23 01PEB', 'Aquis Chronograph', 'aquis-chronograph', 15, 'Unisex', 'Stainless Steel', 'Silver', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Blue', '43.5', 'Stainless Steel', '300m', 'Automatic', 'Caliber 771-1, 62-hour power reserve, Chronograph, Date display, Ceramic bezel, Sapphire crystal, Tachymeter', '5000.00', 1, 5, 1, 'The Oris Aquis Chronograph is an everyday tool watch with 3, 6 and 9 sub-counters. It features a slimline case, elegantly tapered bracelet, 30 bar water resistance, faceted hour markers, and a ceramic bezel insert.', 2, '2026-07-09 00:38:04', '2026-08-21 12:30:40'),
(50, '01 400 7790 4157-07 4 23 47EB', 'Aquis Date Calibre 400', 'aquis-date-calibre-400', 15, 'Unisex', 'Rubber', 'Green', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Green', '43.5', 'Stainless Steel', '300m', 'Automatic', 'Caliber 400, 120-hour power reserve, High anti-magnetism, 10-year service intervals, Date display, Ceramic bezel, Sapphire crystal, Quick adjust clasp system', '4100.00', 1, 7, 1, 'The Oris Aquis Date Calibre 400 features a 43.5mm stainless steel case with a ceramic bezel and a quick adjust clasp system. It is powered by the innovative in-house Caliber 400 with a five-day power reserve and 10-year warranty.', 2, '2026-07-09 00:39:19', '2026-07-09 00:40:51'),
(51, '01 733 7771 3155-07 3 19 02BRS', 'Divers Sixty-Five', 'divers-sixty-five', 15, 'Unisex', 'Textile', 'Blue', 1, NULL, 'XS,S,M,L,XL', 'Round', 'Blue', '38.0', 'Bronze', '100m', 'Automatic', 'Caliber 733, 38-hour power reserve, Date display, Bronze bezel, Sapphire crystal, Recycled Perlon strap', '3100.00', 1, 0, 1, 'The Oris Divers Sixty-Five Cotton Candy features an all-bronze case, bezel, and crown with a vibrant blue dial. It comes on a recycled Perlon strap in sky blue and is powered by a Swiss Made mechanical movement.', 2, '2026-07-09 00:40:50', '2026-08-23 11:51:40');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_main`, `sort_order`, `created_at`) VALUES
(1, 1, 'galaxy-watch-ultra-2025-1783323585-5.png', 1, 1, '2026-07-06 01:54:45'),
(2, 1, 'galaxy-watch-ultra-2025-1783323585-4.png', 0, 2, '2026-07-06 01:54:45'),
(3, 1, 'galaxy-watch-ultra-2025-1783323585-3.png', 0, 3, '2026-07-06 01:54:45'),
(4, 1, 'galaxy-watch-ultra-2025-1783323585-2.png', 0, 4, '2026-07-06 01:54:45'),
(5, 1, 'galaxy-watch-ultra-2025-1783323585-1.png', 0, 5, '2026-07-06 01:54:45'),
(6, 1, 'galaxy-watch-ultra-2025-1783323585-0.png', 0, 6, '2026-07-06 01:54:45'),
(11, 2, 'samsung-watch7-lte-44m-1783324556-0.jpg', 1, 0, '2026-07-06 02:10:56'),
(12, 2, 'samsung-watch7-lte-44m-1783324556-1.jpg', 0, 1, '2026-07-06 02:10:56'),
(13, 2, 'samsung-watch7-lte-44m-1783324556-2.jpg', 0, 2, '2026-07-06 02:10:56'),
(14, 2, 'samsung-watch7-lte-44m-1783324556-3.png', 0, 3, '2026-07-06 02:10:56'),
(15, 2, 'samsung-watch7-lte-44m-1783324556-4.jpg', 0, 4, '2026-07-06 02:10:56'),
(16, 2, 'samsung-watch7-lte-44m-1783324556-5.png', 0, 5, '2026-07-06 02:10:56'),
(17, 3, 'waterbury-heritage-america-250-1783325148-0.webp', 1, 0, '2026-07-06 02:20:48'),
(18, 3, 'waterbury-heritage-america-250-1783325148-1.webp', 0, 1, '2026-07-06 02:20:48'),
(19, 3, 'waterbury-heritage-america-250-1783325148-2.webp', 0, 2, '2026-07-06 02:20:48'),
(20, 3, 'waterbury-heritage-america-250-1783325148-3.webp', 0, 3, '2026-07-06 02:20:48'),
(21, 3, 'waterbury-heritage-america-250-1783325148-4.webp', 0, 4, '2026-07-06 02:20:48'),
(22, 3, 'waterbury-heritage-america-250-1783325148-5.webp', 0, 5, '2026-07-06 02:20:48'),
(23, 3, 'waterbury-heritage-america-250-1783325148-6.webp', 0, 6, '2026-07-06 02:20:48'),
(24, 4, 'expedition-pioneer-titanium-automatic-gmt-1783326002-0.webp', 1, 0, '2026-07-06 02:35:02'),
(25, 4, 'expedition-pioneer-titanium-automatic-gmt-1783326002-1.webp', 0, 1, '2026-07-06 02:35:02'),
(26, 4, 'expedition-pioneer-titanium-automatic-gmt-1783326002-2.webp', 0, 2, '2026-07-06 02:35:02'),
(27, 4, 'expedition-pioneer-titanium-automatic-gmt-1783326002-3.webp', 0, 3, '2026-07-06 02:35:02'),
(28, 4, 'expedition-pioneer-titanium-automatic-gmt-1783326002-4.webp', 0, 4, '2026-07-06 02:35:02'),
(29, 4, 'expedition-pioneer-titanium-automatic-gmt-1783326002-5.webp', 0, 5, '2026-07-06 02:35:02'),
(30, 5, 'expedition-pioneer-titanium-automatic-gmt-titanium-1783346973-0.webp', 1, 0, '2026-07-06 08:24:33'),
(31, 5, 'expedition-pioneer-titanium-automatic-gmt-titanium-1783346973-1.webp', 0, 1, '2026-07-06 08:24:33'),
(32, 5, 'expedition-pioneer-titanium-automatic-gmt-titanium-1783346973-2.webp', 0, 2, '2026-07-06 08:24:33'),
(33, 5, 'expedition-pioneer-titanium-automatic-gmt-titanium-1783346973-3.webp', 0, 3, '2026-07-06 08:24:33'),
(34, 5, 'expedition-pioneer-titanium-automatic-gmt-titanium-1783346973-4.webp', 0, 4, '2026-07-06 08:24:33'),
(35, 5, 'expedition-pioneer-titanium-automatic-gmt-titanium-1783346973-5.webp', 0, 5, '2026-07-06 08:24:33'),
(36, 5, 'expedition-pioneer-titanium-automatic-gmt-titanium-1783346973-6.webp', 0, 6, '2026-07-06 08:24:33'),
(37, 6, 'waterbury-ace-fly-back-chronograph-1783347174-0.webp', 1, 0, '2026-07-06 08:27:54'),
(38, 6, 'waterbury-ace-fly-back-chronograph-1783347174-1.webp', 0, 1, '2026-07-06 08:27:54'),
(39, 6, 'waterbury-ace-fly-back-chronograph-1783347174-2.webp', 0, 2, '2026-07-06 08:27:54'),
(40, 6, 'waterbury-ace-fly-back-chronograph-1783347174-3.webp', 0, 3, '2026-07-06 08:27:54'),
(41, 6, 'waterbury-ace-fly-back-chronograph-1783347174-4.webp', 0, 4, '2026-07-06 08:27:54'),
(42, 6, 'waterbury-ace-fly-back-chronograph-1783347174-5.webp', 0, 5, '2026-07-06 08:27:54'),
(43, 6, 'waterbury-ace-fly-back-chronograph-1783347174-6.webp', 0, 6, '2026-07-06 08:27:54'),
(44, 7, 'harry-potter-x-timex-weekender-sorting-hat-1783347433-0.webp', 1, 0, '2026-07-06 08:32:13'),
(45, 7, 'harry-potter-x-timex-weekender-sorting-hat-1783347433-1.webp', 0, 1, '2026-07-06 08:32:13'),
(46, 7, 'harry-potter-x-timex-weekender-sorting-hat-1783347433-2.webp', 0, 2, '2026-07-06 08:32:13'),
(47, 7, 'harry-potter-x-timex-weekender-sorting-hat-1783347433-3.webp', 0, 3, '2026-07-06 08:32:13'),
(48, 7, 'harry-potter-x-timex-weekender-sorting-hat-1783347433-4.webp', 0, 4, '2026-07-06 08:32:13'),
(49, 7, 'harry-potter-x-timex-weekender-sorting-hat-1783347433-5.webp', 0, 5, '2026-07-06 08:32:13'),
(50, 7, 'harry-potter-x-timex-weekender-sorting-hat-1783347433-6.webp', 0, 6, '2026-07-06 08:32:13'),
(51, 8, 'world-time-digital-1783347581-0.webp', 1, 0, '2026-07-06 08:34:41'),
(52, 8, 'world-time-digital-1783347581-1.webp', 0, 1, '2026-07-06 08:34:41'),
(53, 8, 'world-time-digital-1783347581-2.webp', 0, 2, '2026-07-06 08:34:41'),
(54, 8, 'world-time-digital-1783347581-3.webp', 0, 3, '2026-07-06 08:34:41'),
(55, 8, 'world-time-digital-1783347581-4.webp', 0, 4, '2026-07-06 08:34:41'),
(56, 11, 'end-x-timex-1981-reissue-inspired-1783347825-0.webp', 1, 0, '2026-07-06 08:38:45'),
(57, 11, 'end-x-timex-1981-reissue-inspired-1783347825-1.webp', 0, 1, '2026-07-06 08:38:45'),
(58, 11, 'end-x-timex-1981-reissue-inspired-1783347825-2.webp', 0, 2, '2026-07-06 08:38:45'),
(59, 11, 'end-x-timex-1981-reissue-inspired-1783347825-3.webp', 0, 3, '2026-07-06 08:38:45'),
(60, 11, 'end-x-timex-1981-reissue-inspired-1783347825-4.webp', 0, 4, '2026-07-06 08:38:45'),
(61, 9, 'waterbury-heritage-chronograph-1783348028-0.webp', 1, 0, '2026-07-06 08:42:08'),
(62, 9, 'waterbury-heritage-chronograph-1783348028-1.webp', 0, 1, '2026-07-06 08:42:08'),
(63, 9, 'waterbury-heritage-chronograph-1783348028-2.webp', 0, 2, '2026-07-06 08:42:08'),
(64, 9, 'waterbury-heritage-chronograph-1783348028-3.webp', 0, 3, '2026-07-06 08:42:08'),
(65, 9, 'waterbury-heritage-chronograph-1783348028-4.webp', 0, 4, '2026-07-06 08:42:08'),
(66, 9, 'waterbury-heritage-chronograph-1783348028-5.webp', 0, 5, '2026-07-06 08:42:08'),
(67, 9, 'waterbury-heritage-chronograph-1783348028-6.webp', 0, 6, '2026-07-06 08:42:08'),
(68, 10, 'peanuts-x-timex-marlin-automatic-americana-1783348286-0.webp', 1, 0, '2026-07-06 08:46:26'),
(69, 10, 'peanuts-x-timex-marlin-automatic-americana-1783348286-1.webp', 0, 1, '2026-07-06 08:46:26'),
(70, 10, 'peanuts-x-timex-marlin-automatic-americana-1783348286-2.webp', 0, 2, '2026-07-06 08:46:26'),
(71, 10, 'peanuts-x-timex-marlin-automatic-americana-1783348286-3.webp', 0, 3, '2026-07-06 08:46:26'),
(72, 10, 'peanuts-x-timex-marlin-automatic-americana-1783348286-4.webp', 0, 4, '2026-07-06 08:46:26'),
(73, 12, 'marlin-1783402086-0.webp', 1, 0, '2026-07-06 23:43:07'),
(74, 12, 'marlin-1783402086-1.webp', 0, 1, '2026-07-06 23:43:07'),
(75, 12, 'marlin-1783402086-2.webp', 0, 2, '2026-07-06 23:43:07'),
(76, 12, 'marlin-1783402086-3.webp', 0, 3, '2026-07-06 23:43:07'),
(77, 12, 'marlin-1783402086-4.webp', 0, 4, '2026-07-06 23:43:07'),
(78, 12, 'marlin-1783402086-5.webp', 0, 5, '2026-07-06 23:43:07'),
(79, 12, 'marlin-1783402086-6.webp', 0, 6, '2026-07-06 23:43:07'),
(80, 13, 'transcend-1783402197-0.webp', 1, 0, '2026-07-06 23:44:57'),
(81, 13, 'transcend-1783402197-1.webp', 0, 1, '2026-07-06 23:44:57'),
(82, 13, 'transcend-1783402197-2.webp', 0, 2, '2026-07-06 23:44:57'),
(83, 13, 'transcend-1783402197-3.webp', 0, 3, '2026-07-06 23:44:57'),
(84, 13, 'transcend-1783402197-4.webp', 0, 4, '2026-07-06 23:44:57'),
(85, 13, 'transcend-1783402197-5.webp', 0, 5, '2026-07-06 23:44:57'),
(86, 14, 'cavatina-mini-1783402410-0.webp', 1, 0, '2026-07-06 23:48:30'),
(87, 14, 'cavatina-mini-1783402410-1.webp', 0, 1, '2026-07-06 23:48:30'),
(88, 14, 'cavatina-mini-1783402410-2.webp', 0, 2, '2026-07-06 23:48:30'),
(89, 14, 'cavatina-mini-1783402410-3.webp', 0, 3, '2026-07-06 23:48:30'),
(90, 14, 'cavatina-mini-1783402410-4.webp', 0, 4, '2026-07-06 23:48:30'),
(91, 14, 'cavatina-mini-1783402410-5.webp', 0, 5, '2026-07-06 23:48:30'),
(92, 15, 'transcend-tm-1783402461-0.webp', 1, 0, '2026-07-06 23:49:21'),
(93, 15, 'transcend-tm-1783402461-1.webp', 0, 1, '2026-07-06 23:49:21'),
(94, 15, 'transcend-tm-1783402461-2.webp', 0, 2, '2026-07-06 23:49:21'),
(95, 15, 'transcend-tm-1783402461-3.webp', 0, 3, '2026-07-06 23:49:21'),
(96, 15, 'transcend-tm-1783402461-4.webp', 0, 4, '2026-07-06 23:49:21'),
(97, 15, 'transcend-tm-1783402461-5.webp', 0, 5, '2026-07-06 23:49:21'),
(98, 16, 'q-timex-continental-mini-1783402522-0.webp', 1, 0, '2026-07-06 23:50:23'),
(99, 16, 'q-timex-continental-mini-1783402522-1.webp', 0, 1, '2026-07-06 23:50:23'),
(100, 16, 'q-timex-continental-mini-1783402522-2.webp', 0, 2, '2026-07-06 23:50:23'),
(101, 16, 'q-timex-continental-mini-1783402522-3.webp', 0, 3, '2026-07-06 23:50:23'),
(102, 16, 'q-timex-continental-mini-1783402522-4.webp', 0, 4, '2026-07-06 23:50:23'),
(103, 17, 'timex-legacy-1783402591-0.webp', 1, 0, '2026-07-06 23:51:32'),
(104, 17, 'timex-legacy-1783402591-1.webp', 0, 1, '2026-07-06 23:51:32'),
(105, 17, 'timex-legacy-1783402591-2.webp', 0, 2, '2026-07-06 23:51:32'),
(106, 17, 'timex-legacy-1783402591-3.webp', 0, 3, '2026-07-06 23:51:32'),
(107, 17, 'timex-legacy-1783402591-4.webp', 0, 4, '2026-07-06 23:51:32'),
(108, 17, 'timex-legacy-1783402591-5.webp', 0, 5, '2026-07-06 23:51:32'),
(109, 17, 'timex-legacy-1783402591-6.webp', 0, 6, '2026-07-06 23:51:32'),
(110, 18, 'cavatina-1783402647-0.webp', 1, 0, '2026-07-06 23:52:28'),
(111, 18, 'cavatina-1783402647-1.webp', 0, 1, '2026-07-06 23:52:28'),
(112, 18, 'cavatina-1783402647-2.webp', 0, 2, '2026-07-06 23:52:28'),
(113, 18, 'cavatina-1783402647-3.webp', 0, 3, '2026-07-06 23:52:28'),
(114, 18, 'cavatina-1783402647-4.webp', 0, 4, '2026-07-06 23:52:28'),
(115, 18, 'cavatina-1783402647-5.webp', 0, 5, '2026-07-06 23:52:28'),
(116, 18, 'cavatina-1783402647-6.webp', 0, 6, '2026-07-06 23:52:28'),
(117, 18, 'cavatina-1783402647-7.webp', 0, 7, '2026-07-06 23:52:28'),
(118, 19, 'q-timex-continental-mini-stainless-steel-1783402742-0.webp', 1, 0, '2026-07-06 23:54:02'),
(119, 19, 'q-timex-continental-mini-stainless-steel-1783402742-1.webp', 0, 1, '2026-07-06 23:54:02'),
(120, 19, 'q-timex-continental-mini-stainless-steel-1783402742-2.webp', 0, 2, '2026-07-06 23:54:02'),
(121, 19, 'q-timex-continental-mini-stainless-steel-1783402742-3.webp', 0, 3, '2026-07-06 23:54:02'),
(122, 19, 'q-timex-continental-mini-stainless-steel-1783402742-4.webp', 0, 4, '2026-07-06 23:54:02'),
(123, 19, 'q-timex-continental-mini-stainless-steel-1783402742-5.webp', 0, 5, '2026-07-06 23:54:02'),
(124, 19, 'q-timex-continental-mini-stainless-steel-1783402742-6.webp', 0, 6, '2026-07-06 23:54:02'),
(125, 20, 'timex-time-machines-1783402847-0.webp', 1, 0, '2026-07-06 23:55:47'),
(126, 20, 'timex-time-machines-1783402847-1.webp', 0, 1, '2026-07-06 23:55:47'),
(127, 20, 'timex-time-machines-1783402847-2.webp', 0, 2, '2026-07-06 23:55:47'),
(128, 20, 'timex-time-machines-1783402847-3.webp', 0, 3, '2026-07-06 23:55:47'),
(129, 20, 'timex-time-machines-1783402847-4.webp', 0, 4, '2026-07-06 23:55:47'),
(130, 20, 'timex-time-machines-1783402847-5.webp', 0, 5, '2026-07-06 23:55:47'),
(131, 21, 'timex-time-machines-digital-1783402888-0.webp', 1, 0, '2026-07-06 23:56:28'),
(132, 21, 'timex-time-machines-digital-1783402888-1.webp', 0, 1, '2026-07-06 23:56:28'),
(133, 21, 'timex-time-machines-digital-1783402888-2.webp', 0, 2, '2026-07-06 23:56:28'),
(134, 22, 'peanuts-x-timex-youth-spring-1783402930-0.webp', 1, 0, '2026-07-06 23:57:10'),
(135, 22, 'peanuts-x-timex-youth-spring-1783402930-1.webp', 0, 1, '2026-07-06 23:57:10'),
(136, 22, 'peanuts-x-timex-youth-spring-1783402930-2.webp', 0, 2, '2026-07-06 23:57:10'),
(137, 22, 'peanuts-x-timex-youth-spring-1783402930-3.webp', 0, 3, '2026-07-06 23:57:10'),
(138, 22, 'peanuts-x-timex-youth-spring-1783402930-4.webp', 0, 4, '2026-07-06 23:57:10'),
(139, 22, 'peanuts-x-timex-youth-spring-1783402930-5.webp', 0, 5, '2026-07-06 23:57:10'),
(140, 23, 'peanuts-x-timex-weekender-snoopy-santa-hat-1783402965-0.webp', 1, 0, '2026-07-06 23:57:45'),
(141, 23, 'peanuts-x-timex-weekender-snoopy-santa-hat-1783402965-1.webp', 0, 1, '2026-07-06 23:57:45'),
(142, 23, 'peanuts-x-timex-weekender-snoopy-santa-hat-1783402965-2.webp', 0, 2, '2026-07-06 23:57:45'),
(143, 23, 'peanuts-x-timex-weekender-snoopy-santa-hat-1783402965-3.webp', 0, 3, '2026-07-06 23:57:45'),
(144, 23, 'peanuts-x-timex-weekender-snoopy-santa-hat-1783402965-4.webp', 0, 4, '2026-07-06 23:57:45'),
(145, 25, 'apple-watch-ultra-3-1783429226-0.webp', 1, 0, '2026-07-07 07:15:26'),
(146, 25, 'apple-watch-ultra-3-1783429226-1.webp', 0, 1, '2026-07-07 07:15:26'),
(147, 25, 'apple-watch-ultra-3-1783429226-2.webp', 0, 2, '2026-07-07 07:15:26'),
(148, 25, 'apple-watch-ultra-3-1783429226-3.webp', 0, 3, '2026-07-07 07:15:26'),
(149, 25, 'apple-watch-ultra-3-1783429226-4.webp', 0, 4, '2026-07-07 07:15:26'),
(150, 25, 'apple-watch-ultra-3-1783429226-5.webp', 0, 5, '2026-07-07 07:15:26'),
(151, 25, 'apple-watch-ultra-3-1783429226-6.webp', 0, 6, '2026-07-07 07:15:26'),
(152, 24, 'mtp-1783485833-0.jpg', 1, 0, '2026-07-07 22:58:54'),
(153, 26, 'mtg-1783571894-0.jpg', 1, 0, '2026-07-08 22:53:14'),
(154, 26, 'mtg-1783571894-1.jpg', 0, 1, '2026-07-08 22:53:14'),
(155, 26, 'mtg-1783571894-2.jpg', 0, 2, '2026-07-08 22:53:14'),
(156, 26, 'mtg-1783571894-3.jpg', 0, 3, '2026-07-08 22:53:14'),
(157, 26, 'mtg-1783571894-4.jpg', 0, 4, '2026-07-08 22:53:14'),
(158, 26, 'mtg-1783571894-5.jpg', 0, 5, '2026-07-08 22:53:14'),
(159, 26, 'mtg-1783571894-6.jpg', 0, 6, '2026-07-08 22:53:14'),
(160, 27, 'mudmaster-1783572379-0.jpg', 1, 0, '2026-07-08 23:01:20'),
(161, 27, 'mudmaster-1783572379-1.jpg', 0, 1, '2026-07-08 23:01:20'),
(162, 27, 'mudmaster-1783572379-2.jpg', 0, 2, '2026-07-08 23:01:20'),
(163, 27, 'mudmaster-1783572379-3.jpg', 0, 3, '2026-07-08 23:01:20'),
(164, 27, 'mudmaster-1783572379-4.jpg', 0, 4, '2026-07-08 23:01:20'),
(165, 27, 'mudmaster-1783572379-5.jpg', 0, 5, '2026-07-08 23:01:20'),
(166, 27, 'mudmaster-1783572379-6.jpg', 0, 6, '2026-07-08 23:01:20'),
(167, 27, 'mudmaster-1783572379-7.jpg', 0, 7, '2026-07-08 23:01:20'),
(168, 27, 'mudmaster-1783572379-8.jpg', 0, 8, '2026-07-08 23:01:20'),
(169, 27, 'mudmaster-1783572379-9.jpg', 0, 9, '2026-07-08 23:01:20'),
(170, 27, 'mudmaster-1783572379-10.jpg', 0, 10, '2026-07-08 23:01:20'),
(171, 28, 'ga-110-1783572658-0.jpg', 1, 0, '2026-07-08 23:05:58'),
(172, 28, 'ga-110-1783572658-1.jpg', 0, 1, '2026-07-08 23:05:58'),
(173, 28, 'ga-110-1783572658-2.jpg', 0, 2, '2026-07-08 23:05:58'),
(174, 28, 'ga-110-1783572658-3.jpg', 0, 3, '2026-07-08 23:05:58'),
(175, 29, 'gma-p2100st-1783572842-0.jpg', 1, 0, '2026-07-08 23:09:03'),
(176, 29, 'gma-p2100st-1783572843-1.jpg', 0, 1, '2026-07-08 23:09:03'),
(177, 29, 'gma-p2100st-1783572843-2.jpg', 0, 2, '2026-07-08 23:09:03'),
(178, 29, 'gma-p2100st-1783572843-3.jpg', 0, 3, '2026-07-08 23:09:03'),
(179, 29, 'gma-p2100st-1783572843-4.jpg', 0, 4, '2026-07-08 23:09:03'),
(180, 29, 'gma-p2100st-1783572843-5.jpg', 0, 5, '2026-07-08 23:09:03'),
(181, 29, 'gma-p2100st-1783572843-6.jpg', 0, 6, '2026-07-08 23:09:03'),
(182, 30, 'gm-s2110sr-1783573010-0.jpg', 1, 0, '2026-07-08 23:11:50'),
(183, 30, 'gm-s2110sr-1783573010-1.jpg', 0, 1, '2026-07-08 23:11:50'),
(184, 30, 'gm-s2110sr-1783573010-2.jpg', 0, 2, '2026-07-08 23:11:50'),
(185, 30, 'gm-s2110sr-1783573010-3.jpg', 0, 3, '2026-07-08 23:11:50'),
(186, 30, 'gm-s2110sr-1783573010-4.jpg', 0, 4, '2026-07-08 23:11:50'),
(187, 31, 'mrg-b2000r-1783573311-0.jpg', 1, 0, '2026-07-08 23:16:51'),
(188, 31, 'mrg-b2000r-1783573311-1.jpg', 0, 1, '2026-07-08 23:16:51'),
(189, 31, 'mrg-b2000r-1783573311-2.jpg', 0, 2, '2026-07-08 23:16:51'),
(190, 31, 'mrg-b2000r-1783573311-3.jpg', 0, 3, '2026-07-08 23:16:51'),
(191, 31, 'mrg-b2000r-1783573311-4.jpg', 0, 4, '2026-07-08 23:16:51'),
(192, 31, 'mrg-b2000r-1783573311-5.jpg', 0, 5, '2026-07-08 23:16:51'),
(193, 32, 'mrg-b5000b-1783573993-0.jpg', 1, 0, '2026-07-08 23:28:14'),
(194, 32, 'mrg-b5000b-1783573993-1.jpg', 0, 1, '2026-07-08 23:28:14'),
(195, 32, 'mrg-b5000b-1783573993-2.jpg', 0, 2, '2026-07-08 23:28:14'),
(196, 32, 'mrg-b5000b-1783573993-3.jpg', 0, 3, '2026-07-08 23:28:14'),
(197, 32, 'mrg-b5000b-1783573993-4.jpg', 0, 4, '2026-07-08 23:28:14'),
(198, 32, 'mrg-b5000b-1783573993-5.jpg', 0, 5, '2026-07-08 23:28:14'),
(199, 32, 'mrg-b5000b-1783573993-6.jpg', 0, 6, '2026-07-08 23:28:14'),
(200, 32, 'mrg-b5000b-1783573993-7.jpg', 0, 7, '2026-07-08 23:28:14'),
(201, 32, 'mrg-b5000b-1783573993-8.jpg', 0, 8, '2026-07-08 23:28:14'),
(202, 32, 'mrg-b5000b-1783573993-9.jpg', 0, 9, '2026-07-08 23:28:14'),
(203, 32, 'mrg-b5000b-1783573993-10.jpg', 0, 10, '2026-07-08 23:28:14'),
(204, 33, 'mrg-bf1000b-1783574086-0.jpg', 1, 0, '2026-07-08 23:29:46'),
(205, 33, 'mrg-bf1000b-1783574086-1.jpg', 0, 1, '2026-07-08 23:29:46'),
(206, 33, 'mrg-bf1000b-1783574086-2.jpg', 0, 2, '2026-07-08 23:29:46'),
(207, 33, 'mrg-bf1000b-1783574086-3.jpg', 0, 3, '2026-07-08 23:29:46'),
(208, 33, 'mrg-bf1000b-1783574086-4.jpg', 0, 4, '2026-07-08 23:29:46'),
(209, 33, 'mrg-bf1000b-1783574086-5.jpg', 0, 5, '2026-07-08 23:29:46'),
(210, 33, 'mrg-bf1000b-1783574086-6.jpg', 0, 6, '2026-07-08 23:29:46'),
(211, 33, 'mrg-bf1000b-1783574086-7.jpg', 0, 7, '2026-07-08 23:29:46'),
(212, 34, 'mrg-b2000kt-1783574152-0.jpg', 1, 0, '2026-07-08 23:30:53'),
(213, 34, 'mrg-b2000kt-1783574152-1.jpg', 0, 1, '2026-07-08 23:30:53'),
(214, 34, 'mrg-b2000kt-1783574152-2.webp', 0, 2, '2026-07-08 23:30:53'),
(215, 34, 'mrg-b2000kt-1783574152-3.jpg', 0, 3, '2026-07-08 23:30:53'),
(216, 34, 'mrg-b2000kt-1783574152-4.jpg', 0, 4, '2026-07-08 23:30:53'),
(217, 34, 'mrg-b2000kt-1783574152-5.jpg', 0, 5, '2026-07-08 23:30:53'),
(218, 34, 'mrg-b2000kt-1783574152-6.jpg', 0, 6, '2026-07-08 23:30:53'),
(219, 34, 'mrg-b2000kt-1783574152-7.jpg', 0, 7, '2026-07-08 23:30:53'),
(220, 34, 'mrg-b2000kt-1783574152-8.jpg', 0, 8, '2026-07-08 23:30:53'),
(221, 34, 'mrg-b2000kt-1783574152-9.jpg', 0, 9, '2026-07-08 23:30:53'),
(222, 34, 'mrg-b2000kt-1783574152-10.jpg', 0, 10, '2026-07-08 23:30:53'),
(223, 35, 'gmt-master-ii-1783574823-0.webp', 1, 0, '2026-07-08 23:42:03'),
(224, 35, 'gmt-master-ii-1783574823-1.webp', 0, 1, '2026-07-08 23:42:03'),
(225, 35, 'gmt-master-ii-1783574823-2.webp', 0, 2, '2026-07-08 23:42:03'),
(226, 35, 'gmt-master-ii-1783574823-3.webp', 0, 3, '2026-07-08 23:42:03'),
(227, 35, 'gmt-master-ii-1783574823-4.webp', 0, 4, '2026-07-08 23:42:03'),
(228, 35, 'gmt-master-ii-1783574823-5.webp', 0, 5, '2026-07-08 23:42:03'),
(229, 35, 'gmt-master-ii-1783574823-6.webp', 0, 6, '2026-07-08 23:42:03'),
(230, 35, 'gmt-master-ii-1783574823-7.webp', 0, 7, '2026-07-08 23:42:03'),
(231, 36, 'day-date-36-1783575008-0.webp', 1, 0, '2026-07-08 23:45:08'),
(232, 36, 'day-date-36-1783575008-1.webp', 0, 1, '2026-07-08 23:45:08'),
(233, 36, 'day-date-36-1783575008-2.webp', 0, 2, '2026-07-08 23:45:08'),
(234, 36, 'day-date-36-1783575008-3.webp', 0, 3, '2026-07-08 23:45:08'),
(235, 36, 'day-date-36-1783575008-4.webp', 0, 4, '2026-07-08 23:45:08'),
(237, 37, 'daytona-rainbow-1783575352-0.webp', 1, 0, '2026-07-08 23:50:52'),
(238, 37, 'daytona-rainbow-1783575352-1.webp', 0, 1, '2026-07-08 23:50:52'),
(239, 37, 'daytona-rainbow-1783575352-2.webp', 0, 2, '2026-07-08 23:50:52'),
(240, 37, 'daytona-rainbow-1783575352-3.webp', 0, 3, '2026-07-08 23:50:52'),
(241, 37, 'daytona-rainbow-1783575352-4.webp', 0, 4, '2026-07-08 23:50:52'),
(242, 37, 'daytona-rainbow-1783575352-5.webp', 0, 5, '2026-07-08 23:50:52'),
(243, 37, 'daytona-rainbow-1783575352-6.webp', 0, 6, '2026-07-08 23:50:52'),
(244, 37, 'daytona-rainbow-1783575352-7.webp', 0, 7, '2026-07-08 23:50:52'),
(245, 37, 'daytona-rainbow-1783575352-8.webp', 0, 8, '2026-07-08 23:50:52'),
(246, 38, 'gmt-master-ii-grnr-1783575582-0.webp', 1, 0, '2026-07-08 23:54:42'),
(247, 38, 'gmt-master-ii-grnr-1783575582-1.webp', 0, 1, '2026-07-08 23:54:42'),
(248, 38, 'gmt-master-ii-grnr-1783575582-2.webp', 0, 2, '2026-07-08 23:54:42'),
(249, 38, 'gmt-master-ii-grnr-1783575582-3.webp', 0, 3, '2026-07-08 23:54:42'),
(250, 38, 'gmt-master-ii-grnr-1783575582-4.webp', 0, 4, '2026-07-08 23:54:42'),
(251, 38, 'gmt-master-ii-grnr-1783575582-5.webp', 0, 5, '2026-07-08 23:54:42'),
(252, 38, 'gmt-master-ii-grnr-1783575582-6.webp', 0, 6, '2026-07-08 23:54:42'),
(253, 38, 'gmt-master-ii-grnr-1783575582-7.webp', 0, 7, '2026-07-08 23:54:42'),
(254, 39, 'submariner-1783575700-0.webp', 1, 0, '2026-07-08 23:56:41'),
(255, 39, 'submariner-1783575700-1.webp', 0, 1, '2026-07-08 23:56:41'),
(256, 39, 'submariner-1783575700-2.webp', 0, 2, '2026-07-08 23:56:41'),
(257, 39, 'submariner-1783575700-3.webp', 0, 3, '2026-07-08 23:56:41'),
(258, 39, 'submariner-1783575700-4.webp', 0, 4, '2026-07-08 23:56:41'),
(259, 40, 'daytona-1783575807-0.webp', 1, 0, '2026-07-08 23:58:28'),
(260, 40, 'daytona-1783575807-1.webp', 0, 1, '2026-07-08 23:58:28'),
(261, 40, 'daytona-1783575807-2.webp', 0, 2, '2026-07-08 23:58:28'),
(262, 40, 'daytona-1783575807-3.webp', 0, 3, '2026-07-08 23:58:28'),
(263, 40, 'daytona-1783575807-4.webp', 0, 4, '2026-07-08 23:58:28'),
(264, 40, 'daytona-1783575807-5.webp', 0, 5, '2026-07-08 23:58:28'),
(265, 40, 'daytona-1783575807-6.webp', 0, 6, '2026-07-08 23:58:28'),
(266, 40, 'daytona-1783575807-7.webp', 0, 7, '2026-07-08 23:58:28'),
(267, 40, 'daytona-1783575807-8.webp', 0, 8, '2026-07-08 23:58:28'),
(268, 41, 'sea-dweller-deepsea-1783575961-0.webp', 1, 0, '2026-07-09 00:01:01'),
(269, 41, 'sea-dweller-deepsea-1783575961-1.webp', 0, 1, '2026-07-09 00:01:01'),
(270, 41, 'sea-dweller-deepsea-1783575961-2.webp', 0, 2, '2026-07-09 00:01:01'),
(271, 41, 'sea-dweller-deepsea-1783575961-3.webp', 0, 3, '2026-07-09 00:01:01'),
(272, 41, 'sea-dweller-deepsea-1783575961-4.webp', 0, 4, '2026-07-09 00:01:01'),
(273, 41, 'sea-dweller-deepsea-1783575961-5.webp', 0, 5, '2026-07-09 00:01:01'),
(274, 41, 'sea-dweller-deepsea-1783575961-6.webp', 0, 6, '2026-07-09 00:01:01'),
(275, 41, 'sea-dweller-deepsea-1783575961-7.webp', 0, 7, '2026-07-09 00:01:01'),
(276, 42, 'daytona-gold-1783576114-0.webp', 1, 0, '2026-07-09 00:03:35'),
(277, 42, 'daytona-gold-1783576114-1.webp', 0, 1, '2026-07-09 00:03:35'),
(278, 42, 'daytona-gold-1783576114-2.webp', 0, 2, '2026-07-09 00:03:35'),
(279, 42, 'daytona-gold-1783576114-3.webp', 0, 3, '2026-07-09 00:03:35'),
(280, 42, 'daytona-gold-1783576114-4.webp', 0, 4, '2026-07-09 00:03:35'),
(281, 42, 'daytona-gold-1783576114-5.webp', 0, 5, '2026-07-09 00:03:35'),
(282, 42, 'daytona-gold-1783576114-6.webp', 0, 6, '2026-07-09 00:03:35'),
(283, 42, 'daytona-gold-1783576114-7.webp', 0, 7, '2026-07-09 00:03:35'),
(284, 43, 'cosmograph-daytona-1783576205-0.webp', 1, 0, '2026-07-09 00:05:05'),
(285, 43, 'cosmograph-daytona-1783576205-1.webp', 0, 1, '2026-07-09 00:05:05'),
(286, 43, 'cosmograph-daytona-1783576205-2.webp', 0, 2, '2026-07-09 00:05:05'),
(287, 43, 'cosmograph-daytona-1783576205-3.webp', 0, 3, '2026-07-09 00:05:05'),
(288, 43, 'cosmograph-daytona-1783576205-4.webp', 0, 4, '2026-07-09 00:05:05'),
(289, 43, 'cosmograph-daytona-1783576205-5.webp', 0, 5, '2026-07-09 00:05:05'),
(290, 43, 'cosmograph-daytona-1783576205-6.webp', 0, 6, '2026-07-09 00:05:05'),
(291, 43, 'cosmograph-daytona-1783576205-7.webp', 0, 7, '2026-07-09 00:05:05'),
(292, 44, 'yangtze-jiangtun-1783576959-0.jpg', 1, 0, '2026-07-09 00:17:39'),
(293, 44, 'yangtze-jiangtun-1783576959-1.webp', 0, 1, '2026-07-09 00:17:39'),
(294, 44, 'yangtze-jiangtun-1783576959-2.webp', 0, 2, '2026-07-09 00:17:39'),
(295, 44, 'yangtze-jiangtun-1783576959-3.jpg', 0, 3, '2026-07-09 00:17:39'),
(296, 44, 'yangtze-jiangtun-1783576959-4.webp', 0, 4, '2026-07-09 00:17:39'),
(297, 44, 'yangtze-jiangtun-1783576959-5.jpg', 0, 5, '2026-07-09 00:17:39'),
(298, 44, 'yangtze-jiangtun-1783576959-6.jpg', 0, 6, '2026-07-09 00:17:39'),
(299, 44, 'yangtze-jiangtun-1783576959-7.jpg', 0, 7, '2026-07-09 00:17:39'),
(300, 44, 'yangtze-jiangtun-1783576959-8.jpg', 0, 8, '2026-07-09 00:17:39'),
(301, 45, 'oris-x-redbar-1783577190-0.webp', 1, 0, '2026-07-09 00:21:30'),
(302, 45, 'oris-x-redbar-1783577190-1.webp', 0, 1, '2026-07-09 00:21:30'),
(303, 45, 'oris-x-redbar-1783577190-2.webp', 0, 2, '2026-07-09 00:21:30'),
(304, 45, 'oris-x-redbar-1783577190-3.webp', 0, 3, '2026-07-09 00:21:30'),
(305, 45, 'oris-x-redbar-1783577190-4.webp', 0, 4, '2026-07-09 00:21:30'),
(313, 46, 'great-barrier-reef-1783577591-0.jpg', 1, 0, '2026-07-09 00:28:12'),
(314, 46, 'great-barrier-reef-1783577591-1.jpg', 0, 1, '2026-07-09 00:28:12'),
(315, 46, 'great-barrier-reef-1783577591-2.webp', 0, 2, '2026-07-09 00:28:12'),
(316, 46, 'great-barrier-reef-1783577591-3.webp', 0, 3, '2026-07-09 00:28:12'),
(317, 46, 'great-barrier-reef-1783577591-4.webp', 0, 4, '2026-07-09 00:28:12'),
(318, 46, 'great-barrier-reef-1783577591-5.webp', 0, 5, '2026-07-09 00:28:12'),
(319, 46, 'great-barrier-reef-1783577591-6.jpg', 0, 6, '2026-07-09 00:28:12'),
(320, 47, 'aquis-h-lstein-edition-2023-1783578155-0.jpg', 1, 0, '2026-07-09 00:37:35'),
(321, 47, 'aquis-h-lstein-edition-2023-1783578155-1.webp', 0, 1, '2026-07-09 00:37:35'),
(322, 47, 'aquis-h-lstein-edition-2023-1783578155-2.jpg', 0, 2, '2026-07-09 00:37:35'),
(323, 47, 'aquis-h-lstein-edition-2023-1783578155-3.webp', 0, 3, '2026-07-09 00:37:35'),
(324, 49, 'aquis-chronograph-1783578256-0.webp', 1, 0, '2026-07-09 00:39:16'),
(325, 49, 'aquis-chronograph-1783578256-1.jpg', 0, 1, '2026-07-09 00:39:16'),
(326, 49, 'aquis-chronograph-1783578256-2.jpg', 0, 2, '2026-07-09 00:39:16'),
(327, 49, 'aquis-chronograph-1783578256-3.webp', 0, 3, '2026-07-09 00:39:16'),
(328, 49, 'aquis-chronograph-1783578256-4.webp', 0, 4, '2026-07-09 00:39:16'),
(329, 50, 'aquis-date-calibre-400-1783578351-0.jpg', 1, 0, '2026-07-09 00:40:51'),
(330, 50, 'aquis-date-calibre-400-1783578351-1.jpg', 0, 1, '2026-07-09 00:40:51'),
(331, 50, 'aquis-date-calibre-400-1783578351-2.jpg', 0, 2, '2026-07-09 00:40:51'),
(332, 50, 'aquis-date-calibre-400-1783578351-3.jpg', 0, 3, '2026-07-09 00:40:51'),
(333, 50, 'aquis-date-calibre-400-1783578351-4.jpg', 0, 4, '2026-07-09 00:40:51'),
(334, 50, 'aquis-date-calibre-400-1783578351-5.webp', 0, 5, '2026-07-09 00:40:51'),
(335, 51, 'divers-sixty-five-1783578402-0.jpg', 1, 0, '2026-07-09 00:41:42'),
(336, 51, 'divers-sixty-five-1783578402-1.jpg', 0, 1, '2026-07-09 00:41:42'),
(337, 51, 'divers-sixty-five-1783578402-2.jpg', 0, 2, '2026-07-09 00:41:42'),
(338, 51, 'divers-sixty-five-1783578402-3.jpg', 0, 3, '2026-07-09 00:41:42'),
(339, 51, 'divers-sixty-five-1783578402-4.webp', 0, 4, '2026-07-09 00:41:42'),
(340, 51, 'divers-sixty-five-1783578402-5.jpg', 0, 5, '2026-07-09 00:41:42'),
(341, 51, 'divers-sixty-five-1783578402-6.jpg', 0, 6, '2026-07-09 00:41:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `address` text DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `address`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'Suresh Shrestha', 'suresh@gmail.com', '9848838858', '$2a$12$5JS52oV9CUQ5DOrJn/rNeOtyV83FpGTfB0S2WLq1yy3V0A3JVlVuC', 'admin', 'Mahalaxmi, Lalitpur', '2026-08-25 01:05:02', '2026-05-14 11:19:11', '2026-08-25 01:05:02'),
(2, 'Try', 'sureshSS@gmail.com', '9834231243', '$2y$10$M8Cq./kVcpxpL7N4YVLCnuH7..4C4vUVSjp6TqlGDde/SY4rnlMVG', 'customer', 'tt', NULL, '2026-05-28 17:21:21', '2026-05-28 17:21:21'),
(3, 'Uttam Thapa', 'uttamthapa290@gmail.com', '9848984949', '$2a$12$RpLkc/mCQezL31L1umaT6eWSEBnjBwriRQ/ptvUYGao7nJ4G7igqy', 'customer', 'Langakhel, Lalitpur', '2026-08-25 01:11:30', '2026-06-02 03:06:35', '2026-08-25 01:11:30'),
(4, 'Avi Kumar', 'avi@gmail.com', '9704833484', '$2y$10$7L0LAAkzJ5y6YGU9FuxwRefVzF0tkKMMBGTovQHuh5PdcJjdE6GwS', 'customer', NULL, NULL, '2026-08-23 08:19:33', '2026-08-23 08:19:33');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `product_id`, `added_at`) VALUES
(65, 3, 49, '2026-08-20 17:20:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`,`selected_strap_size`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `model_number` (`model_number`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=344;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
