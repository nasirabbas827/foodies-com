-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2023 at 09:51 AM
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
-- Database: `foodies_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `CategoryName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `UserID`, `CategoryName`) VALUES
(1, 3, 'Rice Chinese '),
(2, 3, 'Pasta');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `restaurantID` int(11) DEFAULT NULL,
  `sender` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `userID`, `restaurantID`, `sender`, `message`, `timestamp`, `reply`) VALUES
(11, 1, 1, 'Food Lover 1', 'Hello', '2023-12-22 12:13:31', 'HAHA'),
(12, 1, 1, 'Food Lover 1', 'Hello', '2023-12-22 12:18:01', 'YEs'),
(13, 1, 1, 'Food Lover 1', 'Helloe', '2023-12-22 12:34:54', 'This is new reply'),
(14, 1, 2, 'Food Lover 1', 'Menu Items q ni ha? ', '2023-12-23 06:57:35', 'Pata ni'),
(15, 1, 1, 'Food Lover 1', 'HE', '2023-12-23 08:43:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `RatingID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `RestaurantID` int(11) NOT NULL,
  `Rating` int(11) NOT NULL,
  `Comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`RatingID`, `UserID`, `RestaurantID`, `Rating`, `Comment`) VALUES
(2, 1, 1, 5, 'The Restaurant was Good');

-- --------------------------------------------------------

--
-- Table structure for table `menuitems`
--

CREATE TABLE `menuitems` (
  `MenuItemID` int(11) NOT NULL,
  `RestaurantID` int(11) DEFAULT NULL,
  `ItemName` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `ImageURL` varchar(255) DEFAULT NULL,
  `SpecialDiscount` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`MenuItemID`, `RestaurantID`, `ItemName`, `Description`, `Price`, `CategoryID`, `ImageURL`, `SpecialDiscount`) VALUES
(1, 1, 'Chinse Rice', 'asefsad', 234965.00, 1, 'uploads/menu/Creating a Complete Website Using Chatgpt part 4.png', 'For Family'),
(2, 1, 'sd', 'tesaf', 65.00, 2, 'uploads/menu/Creating a Complete Website Using Chatgpt part 4.png', 'none');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `MenuItemID` int(11) NOT NULL,
  `RestaurantID` int(11) NOT NULL,
  `DeliveryAddress` varchar(255) NOT NULL,
  `PaymentStatus` varchar(50) NOT NULL,
  `OrderStatus` varchar(50) NOT NULL,
  `OrderDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `UserID`, `MenuItemID`, `RestaurantID`, `DeliveryAddress`, `PaymentStatus`, `OrderStatus`, `OrderDate`) VALUES
(1, 1, 1, 1, 'dsfefs', 'Cash on Delivery', 'Feedback Provided', '2023-12-23 06:16:47'),
(2, 1, 1, 1, 'dsfefs', 'Cash on Delivery', 'Pending', '2023-12-23 06:17:57');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `RestaurantID` int(11) NOT NULL,
  `OwnerID` int(11) DEFAULT NULL,
  `RestaurantName` varchar(255) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `RestaurantImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`RestaurantID`, `OwnerID`, `RestaurantName`, `Location`, `RestaurantImage`) VALUES
(1, 3, 'Nasirs Restaurant', 'Karachi', 'uploads/How to make SRS.jpg'),
(2, 3, 'Nasirs Restaurant', 'Jampur', 'uploads/Creating a Complete Website Using Chatgpt part 6.png'),
(3, 3, 'Nasirs Restaurant', 'Jampur', 'uploads/HOW TO MAKE Design Document USING CHATGPT.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `age` int(11) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `age`, `user_type`, `status`) VALUES
(1, 'Food Lover 1', '$2y$10$t9FyxjdD0CXW0SHNkRsGp.pgIgHvYAjlwP6e/qcYWX8Cc6Tu3T/mS', 'food@gmail.com', '665423', 23, 'food_lover', 'approved'),
(3, 'Resturant Owner', '$2y$10$phTrh5hBTpelJYcU/UI3IOdo9DADMnKQKVG9vdE3WJkTQvH/oYOUu', 'resturant@gmail.com', '665423865', 23, 'restaurant_owner', 'approved'),
(4, 'NASIR ABBAS', '$2y$10$WJbuN4lryrwQspbJOVUmWuP6zmeWeCTls0gzcDZl74LAC7Mlk2qAe', 'res2@gmail.com', '', 0, 'restaurant_owner', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategoryID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`RatingID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `RestaurantID` (`RestaurantID`);

--
-- Indexes for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD PRIMARY KEY (`MenuItemID`),
  ADD KEY `RestaurantID` (`RestaurantID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `MenuItemID` (`MenuItemID`),
  ADD KEY `RestaurantID` (`RestaurantID`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`RestaurantID`),
  ADD KEY `OwnerID` (`OwnerID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `RatingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menuitems`
--
ALTER TABLE `menuitems`
  MODIFY `MenuItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `RestaurantID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`);

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`RestaurantID`) REFERENCES `restaurants` (`RestaurantID`);

--
-- Constraints for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD CONSTRAINT `menuitems_ibfk_1` FOREIGN KEY (`RestaurantID`) REFERENCES `restaurants` (`RestaurantID`),
  ADD CONSTRAINT `menuitems_ibfk_2` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`CategoryID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`MenuItemID`) REFERENCES `menuitems` (`MenuItemID`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`RestaurantID`) REFERENCES `restaurants` (`RestaurantID`);

--
-- Constraints for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD CONSTRAINT `restaurants_ibfk_1` FOREIGN KEY (`OwnerID`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
