-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 08, 2026 at 03:45 PM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `belle_boutique`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `email`, `phone`, `created_at`) VALUES
(1, 'Charlotte ', 'charlotte@example.com', '07123456001', '2026-11-08 15:22:59'),
(2, 'Amelia ', 'amelia@example.com', '07123456002', '2026-11-08 15:22:59'),
(3, 'Fatema', 'fatema@example.com', '07123456003', '2026-11-08 15:22:59'),
(4, 'Isabella', 'isabella@example.com', '07123456004', '2026-11-08 15:22:59'),
(5, 'Forida', 'forida@example.com', '07123456005', '2026-11-08 15:22:59'),
(6, 'Grace', 'grace@example.com', '07123456006', '2026-11-08 15:22:59');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int NOT NULL,
  `item_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `colour` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quantity_in_stock` int NOT NULL DEFAULT '0',
  `low_stock_threshold` int NOT NULL DEFAULT '5',
  `unit` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pieces'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `item_name`, `colour`, `quantity_in_stock`, `low_stock_threshold`, `unit`) VALUES
(1, 'Roses', 'Red', 120, 30, 'pieces'),
(2, 'Tulips', 'Pink', 80, 25, 'pieces'),
(3, 'Peonies', 'White', 18, 20, 'pieces'),
(4, 'Baby’s Breath', 'White', 60, 20, 'pieces'),
(5, 'Floral Ribbon', 'Burgundy', 45, 15, 'rolls'),
(6, 'Wrapping Paper', 'Blush Pink', 12, 15, 'sheets'),
(7, 'Floral Foam', 'Green', 35, 10, 'pieces'),
(8, 'Eucalyptus', 'Green', 50, 20, 'pieces');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `bouquet_description` text COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `order_date` date NOT NULL,
  `deadline` date DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `bouquet_description`, `quantity`, `price`, `order_date`, `deadline`, `status`) VALUES
(1, 1, 'Classic Red Rose Bouquet', 1, 45.00, '2026-11-02', '2026-11-10', 'Completed'),
(2, 2, 'Pink Tulip and Eucalyptus Bouquet', 2, 65.00, '2026-11-04', '2026-11-11', 'Completed'),
(3, 3, 'White Peony Arrangement', 1, 75.00, '2026-11-05', '2026-11-12', 'In Progress'),
(4, 4, 'Burgundy Bouquet', 1, 55.00, '2026-11-06', '2026-11-13', 'Pending'),
(5, 5, 'Rose and Baby’s Breath Bouquet', 2, 90.00, '2026-11-07', '2026-11-15', 'In Progress'),
(6, 6, 'Pastel Pink Bouquet', 1, 60.00, '2026-11-08', '2026-11-16', 'Pending'),
(7, 1, 'White and Green Floral Arrangement', 1, 85.00, '2026-11-08', '2026-11-17', 'Pending'),
(8, 3, 'Pink Peony Bouquet', 1, 70.00, '2026-11-08', '2026-11-18', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_materials`
--

CREATE TABLE `order_materials` (
  `usage_id` int NOT NULL,
  `order_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity_used` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_materials`
--

INSERT INTO `order_materials` (`usage_id`, `order_id`, `item_id`, `quantity_used`) VALUES
(1, 1, 1, 12),
(2, 1, 5, 1),
(3, 2, 2, 15),
(4, 2, 8, 5),
(5, 3, 3, 8),
(6, 3, 7, 2),
(7, 4, 1, 10),
(8, 4, 6, 3),
(9, 5, 1, 15),
(10, 5, 4, 10),
(11, 6, 2, 12),
(12, 6, 6, 2),
(13, 7, 4, 12),
(14, 7, 8, 8),
(15, 8, 3, 6),
(16, 8, 5, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_materials`
--
ALTER TABLE `order_materials`
  ADD PRIMARY KEY (`usage_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_materials`
--
ALTER TABLE `order_materials`
  MODIFY `usage_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `order_materials`
--
ALTER TABLE `order_materials`
  ADD CONSTRAINT `order_materials_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_materials_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
