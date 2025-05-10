-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 12:00 AM
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
-- Database: `e-shopmanage`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteProduct` (IN `productId` INT)   BEGIN
    DELETE FROM products WHERE product_id = productId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteProducts` (IN `p_product_id` INT)   BEGIN

    DELETE FROM inventory
    WHERE product_id = p_product_id;


    DELETE FROM order_details
    WHERE product_id = p_product_id;


    DELETE FROM products
    WHERE product_id = p_product_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllOrdersWithDetails` ()   BEGIN
    SELECT 
        o.order_id,
        o.user_id,
        o.total_price,
        o.order_date,
        o.status,
        p.product_name,
        p.image_url,
        od.quantity,
        od.subtotal
    FROM orders o
    LEFT JOIN order_details od ON o.order_id = od.order_id
    LEFT JOIN products p ON od.product_id = p.product_id
    ORDER BY o.order_date DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllProductInventory` ()   BEGIN
    SELECT 
        p.product_id,
        p.product_name,
        p.size,
        p.color,
        p.price,
        p.stock_quantity AS current_stock,
        c.category_name
    FROM 
        products p
    JOIN 
        categories c ON p.category_id = c.category_id
    ORDER BY 
        p.product_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllUsers` ()   BEGIN
    SELECT user_id, name, email, contact_number, address
    FROM users;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertOrder` (IN `p_user_id` INT, IN `p_total_price` DECIMAL(10,2), IN `p_order_date` DATE, IN `p_status` VARCHAR(50), IN `p_order_items` JSON)   BEGIN
    DECLARE new_order_id INT;

    -- Insert into the order table
    INSERT INTO orders (user_id, total_price, order_date, status)
    VALUES (p_user_id, p_total_price, p_order_date, p_status);

    SET new_order_id = LAST_INSERT_ID();

    -- Insert each item from the JSON array into order_items table
    -- Assuming your table order_items has fields: order_id, product_id, quantity
    SET @i = 0;
    WHILE @i < JSON_LENGTH(p_order_items) DO
        SET @product_id = JSON_UNQUOTE(JSON_EXTRACT(p_order_items, CONCAT('$[', @i, '].product_id')));
        SET @quantity = JSON_UNQUOTE(JSON_EXTRACT(p_order_items, CONCAT('$[', @i, '].quantity')));

        INSERT INTO order_items (order_id, product_id, quantity)
        VALUES (new_order_id, @product_id, @quantity);

        SET @i = @i + 1;
    END WHILE;

    -- Return the new order ID
    SELECT new_order_id AS order_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertProducts` (IN `p_name` VARCHAR(255), IN `p_category_id` INT, IN `p_size` VARCHAR(50), IN `p_color` VARCHAR(50), IN `p_price` DECIMAL(10,2), IN `p_stock_quantity` INT, IN `p_description` TEXT, IN `p_image_url` VARCHAR(255))   BEGIN
    INSERT INTO products (product_name, category_id, size, color, price, stock_quantity, description, image_url)
    VALUES (p_name, p_category_id, p_size, p_color, p_price, p_stock_quantity, p_description, p_image_url);

    -- 🔥 After inserting, return the new product
    SELECT * FROM products WHERE product_id = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegisterAdmin` (IN `p_name` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_password` VARCHAR(255), IN `p_contact` VARCHAR(20), IN `p_address` VARCHAR(255))   BEGIN
    INSERT INTO admin (name, email, password, contact_number, address)
    VALUES (p_name, p_email, p_password, p_contact, p_address);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegisterUser` (IN `p_name` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_password` VARCHAR(100), IN `p_contact_number` INT(20), IN `p_address` VARCHAR(255))   BEGIN
    INSERT INTO `users` (name, email, password, contact_number, address)
    VALUES (p_name, p_email, p_password, p_contact_number, p_address);
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchProducts` (IN `searchQuery` VARCHAR(255), IN `categoryFilter` VARCHAR(255))   BEGIN
    SELECT * FROM products
    WHERE product_name LIKE CONCAT('%', searchQuery, '%')
    AND (category_id = categoryFilter OR categoryFilter = 'all');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateProduct` (IN `p_product_id` INT, IN `p_product_name` VARCHAR(100), IN `p_category_id` INT, IN `p_size` ENUM('XS','S','M','L','XL','XXL'), IN `p_color` VARCHAR(50), IN `p_price` DECIMAL(10,2), IN `p_stock_quantity` INT, IN `p_description` TEXT)   BEGIN
    UPDATE products
    SET 
        product_name = p_product_name,
        category_id = p_category_id,
        size = p_size,
        color = p_color,
        price = p_price,
        stock_quantity = p_stock_quantity,
        description = p_description
    WHERE product_id = p_product_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`, `contact_number`, `address`) VALUES
(9, 'denays', 'denays@gmail.com', '$2y$10$DD183WmQnubrGCIHk2pZy.MAHknJcvzdeC0WigSvS6W8nxF/T4cna', '12312321', 'denays'),
(10, 'Axle Melendres', 'axlemelendres5@gmail.com', '$2y$10$Tc2z5DQwblTZ5cWsYS.Dx.kyMR3VmPVSOfOhT3vShrapUCm909x4y', '093123123123213', 'Pangao, Lipa City, Batangas');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(4, 'Blouses'),
(5, 'Dresses'),
(6, 'Hoodies'),
(7, 'Jackets'),
(8, 'Long sleeves'),
(1, 'Pants'),
(9, 'Polo'),
(10, 'Sando'),
(11, 'Shorts'),
(12, 'Skirts'),
(2, 'T-shirts'),
(3, 'Underwear');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity_in` int(11) NOT NULL,
  `quantity_out` int(11) NOT NULL DEFAULT 0,
  `stock_level` int(11) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `product_id`, `quantity_in`, `quantity_out`, `stock_level`, `last_updated`) VALUES
(34, 60, 125, 0, 125, '2025-04-25 20:59:08'),
(63, 89, 24, 0, 24, '2025-04-29 17:29:39'),
(65, 91, 125, 0, 125, '2025-04-29 17:31:53'),
(66, 92, 51, 0, 51, '2025-04-29 17:34:42'),
(74, 100, 12, 0, 12, '2025-05-03 15:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Shipped','Delivered','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `order_date`, `status`) VALUES
(10, 19, 1150.00, '2025-05-10 15:00:35', 'Pending'),
(11, 19, 1150.00, '2025-05-10 15:02:58', 'Pending'),
(12, 19, 1150.00, '2025-05-10 15:02:58', 'Delivered'),
(13, 19, 2300.00, '2025-05-10 15:04:10', 'Cancelled'),
(16, 19, 4600.00, '2025-05-10 15:38:39', 'Pending'),
(17, 19, 1150.00, '2025-05-10 15:42:40', 'Pending'),
(18, 19, 1150.00, '2025-05-10 15:42:40', 'Pending'),
(19, 19, 2300.00, '2025-05-10 18:13:35', 'Pending'),
(20, 19, 5750.00, '2025-05-10 18:21:49', 'Delivered');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_details_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_details_id`, `order_id`, `product_id`, `quantity`, `subtotal`) VALUES
(1, 10, 60, 1, 1150.00),
(2, 11, 60, 1, 1150.00),
(3, 12, 60, 1, 1150.00),
(4, 13, 60, 2, 2300.00),
(7, 16, 100, 2, 4600.00),
(8, 17, 60, 1, 1150.00),
(9, 18, 60, 1, 1150.00),
(10, 19, 60, 2, 2300.00),
(11, 20, 60, 5, 5750.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `size` enum('XS','S','M','L','XL','XXL') DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category_id`, `size`, `color`, `price`, `stock_quantity`, `description`, `image_url`) VALUES
(60, 'Nike Dri-Fit Challenger', 11, 'M', 'Black', 1150.00, 111, 'Men\'s 18cm (approx.) 2-in-1 Versatile Shorts', 'uploads/1745614748_Nike Dri-Fit Challenger.jpg'),
(89, 'NIKE Men\'s M Nk Club Pq Matchup Polo Shirt', 9, 'XL', 'White', 1995.00, 24, 'shirt', 'uploads/1745947779_NIKE Men\'s M Nk Club Pq Matchup Polo Shirt.jpg'),
(91, 'Under Armour Men\'s Tac Elite', 9, 'M', 'Black', 3640.00, 125, 'UA', 'uploads/1745947913_Under Armour Men\'s Tac Elite.jpg'),
(92, 'Ralph Lauren Lace Ruffled Blouse', 4, 'M', 'White', 9090.00, 51, 'Blouse', 'uploads/1745948082_Ralph Lauren Lace Ruffled Blouses in White.jpg'),
(100, 'Ralph Lauren Ribbed-Knit Cotton Tank Top', 10, 'L', 'Orange', 2300.00, 10, 'her', 'uploads/1746286123_Ralph Lauren Ribbed-Knit Cotton Tank Top.jpg');

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `after_product_insert` AFTER INSERT ON `products` FOR EACH ROW BEGIN
    INSERT INTO inventory (
        product_id,
        quantity_in,
        quantity_out,
        stock_level,
        last_updated
    ) VALUES (
        NEW.product_id,
        NEW.stock_quantity,
        0,
        NEW.stock_quantity,
        NOW()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`review_id`, `user_id`, `product_id`, `order_id`, `rating`, `comment`, `created_at`) VALUES
(4, 19, 60, 12, 1, 'bad', '2025-05-11 01:16:16'),
(5, 19, 60, 20, 5, 'the quality is good, will buy again :>', '2025-05-11 02:22:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `contact_number` bigint(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `logged_in` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `contact_number`, `address`, `status`, `last_login`, `logged_in`) VALUES
(19, 'Axle Melendres', 'axlemelendres1@gmail.com', '$2y$10$XJt.DaNABL834EEnNcTw.e9i.CYb0BKA/7NC6/EAJj9uGB.dcGOt2', 9095773207, 'Pangao, Lipa City, Batangas', 'active', '2025-05-10 23:42:25', 1),
(20, 'denayks', 'denayks@gmail.com', '$2y$10$V4cizxeXsUg7vG.TlcXS2OM/Ro5r.BrCJeE2Q5OF9w0BLbPkh4OMC', 12312, 'lodlod', 'active', '2025-05-10 20:54:45', 1),
(21, 'Neil Tan', 'neiltan1@gmail.com', '$2y$10$Kyalgopsae8Ffo4Yh8g4GO3Tt.APp.9xg1XM4KWzdReOrDZqfk.KS', 90657732071, 'Tambo, Lipa City, Batangas', 'active', '2025-05-11 05:56:04', 1),
(22, 'Justine Kyle Balubal', 'justinekyle@gmail.com', '$2y$10$dHTNRmRJ07STlmFlkpPle.cPVkjADIvjHimSPutwag65JCjyxsG2W', 9065773207, 'Bagong Pook, Lipa City, Batangas', 'active', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_details_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password` (`password`),
  ADD UNIQUE KEY `contact_number` (`contact_number`),
  ADD UNIQUE KEY `contact_number_2` (`contact_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
