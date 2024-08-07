-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2024 at 06:49 AM
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
-- Database: `shopsphere`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `title`, `message`, `created_at`, `updated_at`) VALUES
(2, 'Notification testing announcement ', 'Is it worikng?', '2024-08-07 02:44:21', '2024-08-07 02:44:21');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `commission_rate`, `created_at`, `updated_at`) VALUES
(1, 'Clothing', 6.00, '2024-05-31 21:55:15', '2024-05-31 21:55:15'),
(3, 'Electronics', 10.00, '2024-05-31 21:56:17', '2024-05-31 21:56:17'),
(4, 'Home & Decor', 8.00, '2024-05-31 23:35:07', '2024-05-31 23:41:00'),
(5, 'Gardening', 12.00, '2024-05-31 23:36:15', '2024-05-31 23:36:15'),
(6, 'Sports & Outdoors', 5.00, '2024-05-31 23:39:25', '2024-07-02 20:47:38'),
(7, 'Automotive and Parts', 9.00, '2024-06-01 00:08:57', '2024-06-01 00:09:14');

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `color_id` int(11) NOT NULL,
  `color_name` varchar(50) NOT NULL,
  `color_code` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`color_id`, `color_name`, `color_code`) VALUES
(1, 'Red', '#FF0000'),
(2, 'Blue', '#0000FF'),
(3, 'Green', '#00FF00'),
(4, 'Yellow', '#FFFF00'),
(5, 'Orange', '#FFA500'),
(6, 'Purple', '#800080'),
(7, 'Pink', '#FFC0CB'),
(8, 'Black', '#000000'),
(9, 'White', '#FFFFFF'),
(10, 'Gray', '#808080'),
(11, 'Brown', '#A52A2A'),
(12, 'Multicolor', NULL),
(13, 'None', NULL),
(14, 'Olive', '#808000'),
(15, 'Rust', '#B7410E'),
(16, 'Khakhi', '#F0E68C'),
(17, 'Navy', '#000080'),
(18, 'Indigo', '#4B0082'),
(19, 'Space Grey', '#A8A9AD'),
(20, 'Silver', '#C0C0C0'),
(21, 'Cream White', '#FFFDD0');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('order','announcement') DEFAULT 'order'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `seller_id`, `message`, `is_read`, `created_at`, `type`) VALUES
(1, 6, 'You have received a new order for Moon Dream Catcher with Light for 1.', 0, '2024-08-07 01:59:05', 'order'),
(2, 4, 'New Announcement: Notification testing announcement ', 0, '2024-08-07 02:44:21', 'announcement'),
(3, 5, 'New Announcement: Notification testing announcement ', 0, '2024-08-07 02:44:21', 'announcement'),
(4, 6, 'New Announcement: Notification testing announcement ', 0, '2024-08-07 02:44:21', 'announcement');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_status` enum('Pending','Unshipped','Shipped','Delivered') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `shipping_address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `shipping_status`, `created_at`, `updated_at`, `shipping_address`, `city`, `province`, `postal_code`, `phone_number`) VALUES
(5, 1, 65.54, 'Delivered', '2024-07-23 18:53:50', '2024-08-05 04:24:34', '332 Rittenhouse Rd', 'Kitchener', 'ON', 'N2E 3M4', '5197817326'),
(6, 1, 22.60, 'Unshipped', '2024-07-29 01:48:56', '2024-08-05 04:24:28', '332 Rittenhouse Rd', 'Kitchener', 'ON', 'N2E 3M4', '5197817326'),
(7, 1, 185.32, 'Pending', '2024-08-03 05:34:49', '2024-08-03 05:34:49', '332 Rittenhouse Rd', 'Kitchener', 'ON', 'N2E 3M4', '5197817326'),
(8, 1, 29.38, 'Pending', '2024-08-03 05:53:03', '2024-08-03 05:53:03', '332 Rittenhouse Rd', 'Kitchener', 'ON', 'N2E 3M4', '5197817326'),
(9, 1, 109.74, 'Pending', '2024-08-03 06:25:22', '2024-08-03 06:25:22', '332 Rittenhouse Rd', 'Kitchener', 'ON', 'N2E 3M4', '5197817326'),
(10, 1, 83.70, 'Pending', '2024-08-04 23:21:56', '2024-08-04 23:21:56', '332 Rittenhouse Rd', 'Kitchener', 'ON', 'N2E 3M4', '5197817326'),
(11, 1, 12.43, 'Pending', '2024-08-05 18:43:31', '2024-08-05 18:43:31', '332 Rittenhouse Rd', 'Kitchener', 'ON', 'N2E 3M4', '5197817326'),
(12, 1, 25.99, 'Pending', '2024-08-05 23:55:29', '2024-08-05 23:55:29', '332 Rittenhouse Rd', 'Kitchener', 'ON', 'N2E 3M4', '5197817326'),
(13, 2, 20.34, 'Pending', '2024-08-07 01:58:38', '2024-08-07 01:58:38', '332 Rittenhouse Rd', 'Kitchener', 'ON', 'N2E 3M4', '5197817326');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `seller_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `seller_id`, `variant_id`, `quantity`, `price`, `total_price`) VALUES
(4, 5, 5, 3, 2, 29.00, 58.00),
(5, 6, 5, 14, 1, 20.00, 20.00),
(6, 7, 6, 59, 1, 164.00, 164.00),
(7, 8, 4, 74, 1, 26.00, 26.00),
(8, 9, 4, 61, 2, 59.00, 118.00),
(9, 10, 4, 11, 2, 35.00, 70.00),
(10, 10, 5, 17, 1, 20.00, 20.00),
(11, 11, 5, 12, 1, 11.00, 11.00),
(12, 12, 6, 42, 1, 23.00, 23.00),
(13, 13, 6, 89, 1, 18.00, 18.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `length` decimal(5,2) DEFAULT NULL,
  `width` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `seller_id`, `category_id`, `product_name`, `description`, `length`, `width`, `height`, `weight`, `created_at`, `updated_at`) VALUES
(5, 4, 3, 'Headphones', 'Headphone Description', 30.00, 10.00, 40.00, 0.50, '2024-07-02 18:35:50', '2024-07-13 01:08:10'),
(6, 5, 7, 'Motorcycle Phone Holder', 'This bike phone mount is easy to install without tool. The extra 4 silicone pads make your phone fit better with handlebar, can add or remove the silicone pad according to the diameter of handlebar. Suit well with various types of bicycles, motorcycles, stroller, shopping cart, electric bike, indoor treadmill, spin bike.', 16.00, 9.60, 5.10, 0.26, '2024-07-02 21:15:20', '2024-07-02 21:15:20'),
(7, 4, 7, 'Extreme Tire Shine 650ML', 'Extreme Tire Shine 650ML', 27.30, 6.10, 11.80, 0.71, '2024-07-02 21:19:09', '2024-07-02 21:19:09'),
(8, 6, 7, 'Tire Inflator Portable Air Compressor', 'High-quality materials were sourced for this tire inflator as it can pump up your car\'s tires with 35 L/Min, it can inflate the 195/55/R15 car tire from 0 to 35psi under 5 minutes. Professionally calibrated to always return a reading within 1.5% of the pressure of your tire. ', 20.00, 9.00, 16.00, 1.00, '2024-07-02 21:27:25', '2024-07-02 21:27:25'),
(9, 6, 7, 'AC to DC Converter 12V 2A 24W Car Cigarette Lighter Socket', '12V 2A 24W AC/DC Car Power Converter, transform 110-240V AC from Wall Plug into 12V DC for Car charger or Car lighter Plug in, bring your car appliances back home to use conveniently, which means you can save many money.\r\nBest solution for low power power with international safety certification.', 8.00, 4.00, 3.00, 0.14, '2024-07-02 21:35:27', '2024-07-02 21:35:27'),
(11, 4, 7, 'Car Battery Charger, 12V 6A Smart Battery', 'GREAT COOLING SYSTEM\r\nExcellent heat dispersion performance with built-in cooling fan, stay cooling after long time charging, you can even leave it charging whole night.\r\nBattery Desulfator\r\nAdopt the newest pulse repair technology, restore or improve the battery performance to avoid further annoying battery failure.', 21.00, 6.50, 11.00, 0.45, '2024-07-02 21:44:54', '2024-07-14 00:55:07'),
(12, 5, 7, 'Blind Spot Mirror, 2\" Round HD Glass', 'Newest upgrade 360 degree rotate + sway adjustabe, maximize your view with wide angle in car. Helps you to forecast the next surroundings when passing, changing lanes or parking.\r\n\r\nHD real glass convex wide angle mirror\r\n2\" ultrathin slim design\r\nInstall and test within seconds\r\nMUST have for truck, car, SUV, RVs and vans', 12.70, 1.78, 7.62, 0.04, '2024-07-02 21:49:53', '2024-07-02 21:49:53'),
(13, 5, 1, 'Men\'s Slim-Fit Long-Sleeve Henley Shirt', 'We listen to customer feedback and fine-tune every detail to ensure that our clothes are more comfortable, higher quality and longer lasting – at affordable prices for the whole family.', 30.00, 10.00, 40.00, 0.50, '2024-07-02 22:13:10', '2024-07-02 22:13:10'),
(14, 5, 1, 'Women\'s Long Sleeve Hiking Shirts', 'Women\'s Sun Shirts Hoodie UPF 50+ SPF Long Sleeve Hiking Lightweight Quick Dry UV Protection Outdoor Clothing', 30.00, 10.00, 16.00, 0.50, '2024-07-02 22:19:46', '2024-07-02 22:19:46'),
(15, 4, 1, 'Men\'s Short Sleeve Button Up Shirts', 'Stylish casual short sleeve stripes button down shirt. This wardrobe-essential shirt features an easy, flattering fit. Perfect gifts for your boyfriend, son, husband, and yourself.', 30.00, 10.00, 16.00, 0.50, '2024-07-02 22:25:32', '2024-07-02 22:25:32'),
(16, 4, 1, 'Men\'s Ever soft Fleece Sweatpants & Joggers', 'This Swatpant & Jogger is made with EverSoft ring spun cotton for premium softness wash after wash. This great fleece collection offers a variety of great benefits to keep you feeling comfortable and confident. The wicking and odor protection benefits will help keep you feeling fresh. ', 27.00, 29.00, 7.00, 0.33, '2024-07-03 03:51:44', '2024-07-03 03:51:44'),
(17, 6, 1, 'Mens Classic-fit 9\" Short', 'Fine-tune every detail to ensure our clothes are more comfortable, higher quality, and longer lasting—at affordable prices for the whole family.', 34.00, 31.00, 4.00, 0.29, '2024-07-03 04:03:02', '2024-07-03 04:03:02'),
(18, 6, 1, 'Men\'s Relaxed Fit Jeans', 'Men\'s Relaxed Fit are quality, modern jeans built strong enough to last. With a straight, fuller leg and all-around relaxed fit through the seat and thigh, these jeans are created with premium flex denim that moves with you throughout the day.', 39.00, 31.00, 6.00, 0.79, '2024-07-03 04:09:27', '2024-07-03 04:09:27'),
(19, 6, 3, 'Wire-free smart security camera two-year battery life', 'Stay close to home from anywhere. See what’s happening with HD live view and infrared night vision.\r\nSpeak to people and pets with crisp two-way audio – it’s like being home even when you’re not.', 7.00, 7.00, 5.00, 0.11, '2024-07-03 04:38:45', '2024-07-03 04:38:45'),
(20, 6, 3, 'iPhone Charger Apple Charger', '2 Pack Apple Type C Wall Charger Block with 2 Pack [6FT&10FT] Long USB C to Lightning Cable for iPhone 14/13/12/12 Pro Max/11/Xs Max/XR/X, AirPods Pro', 17.00, 8.00, 3.00, 0.17, '2024-07-03 04:41:10', '2024-07-03 04:41:10'),
(21, 4, 3, 'Wi-Fi Extender (RE550)', 'Covers Up to 2800 Sq.ft and 35 Devices, 1900Mbps Dual Band Wireless Repeater, Internet Booster, Gigabit Ethernet Port', 0.00, 7.62, 6.60, 0.23, '2024-07-03 04:44:22', '2024-07-03 04:44:22'),
(22, 4, 3, 'Apple iPad (9th generation)', 'with A13 Bionic chip, 10.2-inch Retina display, 64GB, Wi-Fi, 12MP front/8MP back camera, Touch ID, all-day battery life', 0.77, 17.41, 25.06, 0.50, '2024-07-03 04:56:49', '2024-07-03 04:56:49'),
(23, 5, 3, 'Wireless Earbuds, Bluetooth Headphones', '5.3 HiFi Stereo, Wireless Earphones with ENC Noise Cancelling Mic, IP7 Waterproof in Ear Wireless Headphones, Touch Control, LED Digital Display Ear Buds Black', 10.50, 8.30, 3.60, 0.10, '2024-07-03 05:00:50', '2024-07-03 05:00:50'),
(24, 5, 3, 'Portable Charger, 20,000mAh Power Bank', 'Battery Pack with 2-Port, 15W High-Speed Charging for iPhone 15/15 Plus/15 Pro/15 Pro Max, iPhone 14/13/12 Series, Samsung Galaxy', 16.31, 8.10, 2.41, 0.47, '2024-07-03 05:04:14', '2024-07-03 05:04:14'),
(25, 6, 5, 'Japanese Weeding Sickle,Very Sharp Edge Quick Work', 'Length: 13inch\r\nMaterial: stainless steel, wooden handle\r\nDurable: Rust proof stainless steel with solid wood handle, durable and sturdy.\r\nApplication: Removing thistles, dandelions, crabgrass and other common garden weeds\r\nUse: Suitable for the weeding in garden, lawn and more.', 5.08, 7.62, 12.70, 0.21, '2024-07-04 18:57:40', '2024-07-04 18:57:40'),
(26, 6, 5, 'Garden Clippers, Professional Ratchet Pruning Shears', 'Put the plant at the very bottom of the blade, press down the handle to make the ratchet stuck in the first groove and cut the blade into the stem, slightly release the handle to make the ratchet stuck in the second groove and apply a little force again to make the blade further cut into the stem', 26.00, 10.00, 3.00, 0.26, '2024-07-04 18:59:17', '2024-07-04 18:59:17'),
(27, 4, 5, ' Garden Tools Set, 10 Piece Heavy-Duty Stainless-Steel Gardening Tools', '10-Pack Gardening Tools:\r\nThis garden tool set includes a total of 9 tools and 1 storage bag. The pretty carry bag will be an ornament to your garden.\r\n\r\nSharp and Precise:\r\nPerform heavy gardening tasks without worrying about these tools bending or breaking. They are sturdy and made of coated stainless steel to prevent rust and corrosion. They have sharp, pointed ends that make it easy to dig through hard soil and break through weeds and roots.', 33.00, 17.00, 13.00, 1.48, '2024-07-04 19:02:52', '2024-07-04 19:02:52'),
(28, 4, 5, 'Garden Tools Set, 3 Piece Heavy Duty Gardening Tools', 'A Gardening Tool Set Made to Last\r\nSturdy and Comfortable Kit The leather handle increases your comfort, and the sturdy aluminum alloy material allows it to easily help you with gardening work in any environment.\r\nThe leather handle increases your comfort, and the sturdy aluminum alloy material allows it to easily help you with gardening work in any environment.\r\n\r\nUser-friendly Craftsmanship\r\nComfortable handle design makes them suitable for kids, the aged and any other family members to plant and work in the garden or potted plants and enjoy the fun of nature.\r\n\r\nSuper Value Garden Gifts\r\nThe tools is great gift for any horticultural enthusiast, children\'s Labor experience, great for kids and seniors.', 10.00, 2.00, 50.00, 0.58, '2024-07-04 19:04:26', '2024-07-04 19:04:26'),
(29, 5, 5, 'Garden Tool Hoe and Cultivator Hand Tiller Adjustable Handle', 'Garden Hoe and Cultivator: This is a 2 in 1 hand garden hoe and cultivator combo with adjustable handle, perfect for weeding,ditching,seeding,loosen soil,cultivate vegetables and flowers, uproot weeds\r\n45\" Adjustable Handle: Optimal 45inch length for long reach and balance. Ideal for gardeners who find many long handle weeder to hard to manage\r\nHeavy Duty Garden Hoe and Rake: Constructed of premium steel, this garden tool does most jobs around the garden, lawn. Work with the toughest roots, bricks and soil without bending, cracking or falling apart', 10.00, 9.00, 115.00, 0.92, '2024-07-04 19:10:58', '2024-07-04 19:10:58'),
(30, 5, 5, 'Garden Hoe Tool,Triangle Gardening Hoe with Sharp Blade', 'Sturdy Gardening Hoe: Our hoe garden tools are made of strong alloy.Not only the blade is hard and sharp but also the link part is welded tightly.The surface has an anti-rust coating that can be used for a long time without being corroded\r\nAdjustable Length Handle: Unlike other wooden handles, we use light gauge steel to create the handle.Lightweight and more durable at the same time, the adjustable length design is versatile.Ergonomic construction is also better for the human body\r\nMultifunctional Garden Hoe: Sturdy and sharp triangular-headed digging hoe suitable for all kinds of gardening work.Such as transplanting plants, digging out weeds, cleaning flower beds,cutting trenches and more', 25.00, 5.00, 3.00, 1.38, '2024-07-04 19:13:09', '2024-07-04 19:13:09'),
(31, 5, 4, 'Vases for Pampas Grass, Creative Vase Modern Home Decor', 'The decorative vase of in modern style attracts all eyes. A must for all those who like to be cozy and want to upgrade their interior with tasteful home accessories. Whether on the living room table or on the festively decorated table, the vase in the practical 2-piece set cuts a good figure everywhere and creates a beautiful atmosphere.', 21.00, 13.00, 20.00, 0.84, '2024-07-04 19:22:03', '2024-07-04 19:22:03'),
(32, 5, 4, 'Ceramic Vase Set,Set of 3 Small Flower Vases', 'The manually measured size may have an error of about 0.4 inches.\r\nCeramic products are fragile, please do not let children touch them alone.\r\nDue to studio lighting and screen settings, the colors may differ slightly from the pictures.\r\nNote, the vase try not to fill water, not more than 1/3, the vase is handmade.', 7.60, 7.60, 28.00, 1.36, '2024-07-04 19:26:48', '2024-07-04 19:26:48'),
(33, 4, 4, 'Pack of 2 Corduroy Decorative Throw Pillow Covers', 'GREAT PROTECTORS FOR PILLOWS\r\nThe pillow covers are generally utilized as a protective cover for the your pillows, which brings you a soft touch and ultimate luxury experience. The soft touch material will keep you cozy and comfy while relaxing in your living space.\r\n\r\nIDEAL DECORATIONS\r\nThe throw pillow covers presents itself as a great fit for your living room, bedroom, patio, lawn, porch, balcony, couch or sofa. You could use an inexpensive way to change the look of your pillow cover rather than buying new throw pillows.', 45.00, 45.00, 5.00, 0.50, '2024-07-04 19:36:07', '2024-07-04 19:36:07'),
(34, 4, 4, 'Northern Lights Aurora Projector', '3 in 1 night light projector & Bluetooth speaker & white noise. With 14 different northern lights effects, you can customize the light effect, brightness, speed and soothing atmosphere. Imagine that your living room is flowing with colorful northern lights as if you are in nebulae, mysterious and No suggestions suitable power adapter for this Type-C data cable is 5V 2A, please use a 5V 2A power adapter!', 20.00, 20.00, 10.00, 0.49, '2024-07-04 19:38:54', '2024-07-04 19:38:54'),
(35, 6, 4, 'Moon Dream Catcher with Light', 'Simple dream catcher, weaving by hand, weaving with natural cotton thread. Natural cotton thread is a non-allergic material. It is a very versatile decorative pendant.The material of the dream catcher is Eco-friendly! Sleep peacefully knowing that your dream catcher is made from earth-friendly materials, It will strongly retain your good dreams, and let the bad ones slide away!The color will never fade ,they can last the whole season,and contiually be used next year.', 20.00, 20.00, 10.00, 0.50, '2024-07-04 19:43:01', '2024-07-04 19:43:01'),
(36, 6, 4, 'Artificial Potted Plant,Cute Fake Succulent Plant', 'Each lifelike mini succulent plant comes in a beautiful cement box, made of advanced PE material leaf, ooks real enough and are more lifelike naturalistic than others. Size: Total height is about 9.85in/25cm, Length is about 3.2in/8cm. However, due to manual measurement, there will be an error of 1-2 cm.\r\nThe fake succulent plant looks the same as real succulents. The small plant is well made and fits perfectly in small spaces. Livens up your room and you don\'t have to worry about the pets eating the plants.', 8.00, 14.00, 25.00, 1.02, '2024-07-04 19:44:53', '2024-07-04 19:44:53');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_id` int(11) DEFAULT NULL,
  `size_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `color_id`, `size_id`, `quantity`, `price`, `product_image`) VALUES
(2, 5, 8, 13, 0, 120.00, 'product-5.jpg'),
(3, 6, 8, 13, 500, 29.00, 'bike-mout-black.jpg'),
(4, 6, 5, 13, 100, 29.00, 'bike-mount-orange.jpg'),
(5, 7, 13, 13, 200, 10.00, 'tire-shine-liquid.jpg'),
(6, 8, 5, 13, 300, 36.00, 'tire-inflater-orange.jpg'),
(7, 8, 1, 13, 200, 36.00, 'tire-inflater-red.jpg'),
(8, 8, 2, 13, 250, 36.00, 'tire-inflater-blue.jpg'),
(9, 8, 4, 13, 100, 36.00, 'tire-inflater-yellow.jpg'),
(10, 9, 8, 13, 100, 14.00, 'car-power-convertor.jpg'),
(11, 11, 13, 13, 98, 35.00, 'car-battery-jump-starter.jpg'),
(12, 12, 13, 13, 99, 11.00, 'round-side-mirror.jpg'),
(13, 13, 9, 8, 0, 20.00, 'long-sleeve-t-shirt-navy.jpg'),
(14, 13, 9, 9, 4, 20.00, 'long-sleeve-t-shirt-navy.jpg'),
(15, 13, 9, 11, 100, 20.00, 'long-sleeve-t-shirt-navy.jpg'),
(16, 13, 8, 9, 100, 20.00, 'long-sleeve-t-shirt-black.jpg'),
(17, 13, 8, 10, 99, 20.00, 'long-sleeve-t-shirt-black.jpg'),
(18, 13, 8, 12, 100, 20.00, 'long-sleeve-t-shirt-black.jpg'),
(19, 13, 3, 9, 100, 20.00, 'long-sleeve-t-shirt-olive.jpg'),
(20, 13, 3, 10, 100, 20.00, 'long-sleeve-t-shirt-olive.jpg'),
(21, 14, 10, 8, 100, 30.00, 'women-t-shirt-long-sleeve-grey.jpg'),
(22, 14, 10, 9, 100, 30.00, 'women-t-shirt-long-sleeve-grey.jpg'),
(23, 14, 10, 10, 100, 30.00, 'women-t-shirt-long-sleeve-grey.jpg'),
(24, 14, 5, 10, 100, 30.00, 'women-t-shirt-long-sleeve-orange.jpg'),
(26, 14, 5, 12, 100, 30.00, 'women-t-shirt-long-sleeve-orange.jpg'),
(27, 14, 6, 9, 100, 30.00, 'women-t-shirt-long-sleeve-purple.jpg'),
(28, 14, 6, 10, 100, 30.00, 'women-t-shirt-long-sleeve-purple.jpg'),
(29, 15, 10, 8, 100, 29.00, 'men-stripe-shirt-gray.jpg'),
(30, 15, 10, 9, 100, 29.00, 'men-stripe-shirt-gray.jpg'),
(31, 15, 10, 11, 100, 29.00, 'men-stripe-shirt-gray.jpg'),
(32, 15, 2, 9, 100, 29.00, 'men-stripe-shirt-lue.jpg'),
(33, 15, 2, 10, 100, 29.00, 'men-stripe-shirt-lue.jpg'),
(34, 15, 2, 12, 100, 29.00, 'men-stripe-shirt-lue.jpg'),
(35, 16, 8, 10, 100, 21.00, 'men-joggers-black.jpg'),
(36, 16, 8, 11, 100, 21.00, 'men-joggers-black.jpg'),
(37, 16, 10, 8, 100, 21.00, 'men-joggers-grey.jpg'),
(38, 16, 10, 9, 100, 21.00, 'men-joggers-grey.jpg'),
(39, 16, 1, 11, 100, 100.00, 'men-joggers-red.jpg'),
(40, 16, 1, 12, 100, 21.00, 'men-joggers-red.jpg'),
(41, 17, 16, 1, 100, 23.00, 'mens-shorts-khakhi.jpg'),
(42, 17, 16, 2, 99, 23.00, 'mens-shorts-khakhi.jpg'),
(43, 17, 16, 3, 100, 23.00, 'mens-shorts-khakhi.jpg'),
(44, 17, 16, 4, 100, 23.00, 'mens-shorts-khakhi.jpg'),
(45, 17, 14, 1, 100, 23.00, 'mens-shorts-olive.jpg'),
(46, 17, 14, 5, 100, 23.00, 'mens-shorts-olive.jpg'),
(47, 17, 14, 6, 100, 23.00, 'mens-shorts-olive.jpg'),
(48, 17, 17, 2, 100, 23.00, 'mens-shorts-navy.jpg'),
(49, 17, 17, 5, 100, 23.00, 'mens-shorts-navy.jpg'),
(50, 17, 17, 7, 100, 23.00, 'mens-shorts-navy.jpg'),
(51, 18, 8, 1, 100, 45.00, 'mesn-jeans-black.jpg'),
(52, 18, 8, 2, 100, 45.00, 'mesn-jeans-black.jpg'),
(53, 18, 8, 3, 100, 45.00, 'mesn-jeans-black.jpg'),
(54, 18, 8, 5, 100, 45.00, 'mesn-jeans-black.jpg'),
(55, 18, 18, 2, 100, 52.00, 'men-jeans-indigo.jpg'),
(56, 18, 18, 4, 100, 52.00, 'men-jeans-indigo.jpg'),
(57, 18, 18, 5, 100, 52.00, 'men-jeans-indigo.jpg'),
(58, 18, 18, 7, 100, 52.00, 'men-jeans-indigo.jpg'),
(59, 19, 8, 13, 99, 164.00, 'smart-security-home-camera.jpg'),
(60, 20, 9, 13, 100, 20.00, 'iphone-chager.jpg'),
(61, 21, 13, 13, 98, 59.00, 'wifi-extender.jpg'),
(62, 22, 19, 14, 50, 379.00, 'ipad-space-grey.jpg'),
(63, 22, 19, 15, 50, 578.00, 'ipad-space-grey.jpg'),
(64, 22, 20, 14, 50, 379.00, 'ipad-space-silver.jpg'),
(65, 22, 20, 15, 50, 649.00, 'ipad-space-silver.jpg'),
(66, 23, 8, 13, 100, 49.00, 'wireless-earbuds-black.jpg'),
(67, 23, 3, 13, 100, 49.00, 'wireless-earbuds-green.jpg'),
(68, 23, 9, 13, 100, 49.00, 'wireless-earbuds-white.jpg'),
(69, 24, 8, 13, 100, 49.00, 'anker-powerbank-black.jpg'),
(70, 24, 9, 13, 100, 49.00, 'anker-powerbank-white.jpg'),
(71, 25, 13, 13, 100, 23.00, 'gardenng-japanesse-weeding.jpg'),
(72, 26, 1, 13, 100, 26.00, 'garden-clippers.jpg'),
(73, 27, 13, 13, 100, 45.00, 'gardenset-10-piece-set.jpg'),
(74, 28, 13, 13, 99, 26.00, 'gardenset-3-piece-set.jpg'),
(75, 29, 8, 16, 100, 22.00, 'heo-and-cultivater-black.jpg'),
(76, 29, 8, 17, 100, 100.00, 'heo-and-cultivater-black.jpg'),
(77, 30, 8, 13, 100, 27.00, 'gardel-hoe-adjustable.jpg'),
(78, 31, 9, 8, 100, 24.00, 'home-decor-vases-for-grass-white.jpg'),
(79, 31, 9, 9, 100, 24.00, 'home-decor-vases-for-grass-white.jpg'),
(80, 31, 8, 9, 100, 24.00, 'home-decor-vases-for-grass-black.jpg'),
(81, 31, 8, 10, 100, 24.00, 'home-decor-vases-for-grass-black.jpg'),
(82, 32, 12, 13, 100, 41.00, 'home-decor-rustic-vases-3-set-multicolor.jpg'),
(83, 32, 1, 13, 100, 41.00, 'home-decor-rustic-vases-3-set-red.jpg'),
(84, 32, 4, 13, 100, 39.00, 'home-decor-rustic-vases-3-set-yellow.jpg'),
(85, 33, 21, 13, 100, 24.00, 'decor-pillow-covers-cream-white.jpg'),
(86, 33, 15, 13, 100, 24.00, 'decor-pillow-coversdecor-pillow-covers-rust.jpg'),
(87, 33, 14, 13, 100, 24.00, 'decor-pillow-covers-olive-green.jpg'),
(88, 34, 13, 13, 100, 49.00, 'home-decor-northen-light-projector.jpg'),
(89, 35, 9, 13, 99, 18.00, 'home-decor-moon-wall-qhite.jpg'),
(90, 35, 8, 13, 100, 18.00, 'home-decor-moon-wall-black.jpg'),
(91, 35, 8, 13, 100, 18.00, 'home-decor-moon-wall-blue.jpg'),
(92, 36, 13, 13, 100, 34.00, 'home-decor-artificial-pot.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `promo_code`
--

CREATE TABLE `promo_code` (
  `promo_id` int(11) NOT NULL,
  `promo_name` varchar(50) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL,
  `status` enum('Active','Expired') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo_code`
--

INSERT INTO `promo_code` (`promo_id`, `promo_name`, `discount_percent`, `status`) VALUES
(1, 'CAP20', 20.00, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `seller_id` int(11) NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `business_email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `transit_number` varchar(5) DEFAULT NULL,
  `institution_number` varchar(3) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`seller_id`, `store_name`, `address`, `city`, `province`, `postal_code`, `business_email`, `password`, `contact_number`, `tax_id`, `bank_account_number`, `bank_name`, `transit_number`, `institution_number`, `created_at`, `updated_at`, `reset_token`, `token_expiry`) VALUES
(4, 'Test store', '123 Abc Street 2', 'Abc City 2', 'ON', 'N2E 3M3', 'test@test.com', '$2y$10$Prxb1xArWA.1twcsv89o6O9Rkk3YoxZ.jGP4qOVnzSJTdfzMXBhgu', '1234567891', '123456789', '1234567', 'CIBC', '12345', '123', '2024-06-28 02:56:20', '2024-07-02 18:55:05', NULL, NULL),
(5, 'Spergo', '101 Ottawa St. North', 'Waterloo', 'ON', 'N2E 1M1', 'spergo@gmail.com', '$2y$10$1Iiqr2QQfL0hoNM..4Q8wON6uSAFBusbLz1MqcHevxOdOTcUzpPd.', '1234567890', '123456789', '1234567', 'TD', '54321', '321', '2024-07-02 21:09:23', '2024-07-02 21:09:23', NULL, NULL),
(6, 'Maxzope', '223 Ursus Crescent', 'Surrey', 'BC', 'V3V 6L4', 'maxzope@gmail.com', '$2y$10$e2W9T6GY4w0s.dqsgs6Y2etkaGCypbRn3jhzchTfNFnl7S38JNtXa', '1234567890', '123456789', '1234567', 'Scotia', '98765', '987', '2024-07-02 21:23:18', '2024-07-02 21:23:18', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `size_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `size_name`) VALUES
(1, '28'),
(2, '30'),
(3, '32'),
(4, '34'),
(5, '36'),
(6, '38'),
(7, '40'),
(8, 'S'),
(9, 'M'),
(10, 'L'),
(11, 'XL'),
(12, 'XXL'),
(13, 'None'),
(14, '64GB'),
(15, '256GB'),
(16, '45\" inch'),
(17, '31\" inch');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Completed','Failed') NOT NULL,
  `paypal_transaction_id` varchar(100) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `order_id`, `amount`, `status`, `paypal_transaction_id`, `error_message`, `created_at`, `updated_at`) VALUES
(2, 5, 65.54, 'Completed', '61L15167L8211971T', NULL, '2024-07-23 18:54:11', '2024-07-23 18:54:11'),
(3, 6, 22.60, 'Completed', '5EN28845T2513314G', NULL, '2024-07-29 01:49:19', '2024-07-29 01:49:19'),
(4, 7, 185.32, 'Completed', '21G21756415218400', NULL, '2024-08-03 05:35:10', '2024-08-03 05:35:10'),
(5, 8, 29.38, 'Completed', '69U143463E674840R', NULL, '2024-08-03 05:53:24', '2024-08-03 05:53:24'),
(6, 9, 109.74, 'Completed', '7B309783U58301635', NULL, '2024-08-03 06:25:45', '2024-08-03 06:25:45'),
(7, 10, 83.70, 'Completed', '3PB74935GT4640027', NULL, '2024-08-04 23:22:18', '2024-08-04 23:22:18'),
(8, 11, 12.43, 'Completed', '27408473KU054793X', NULL, '2024-08-05 18:43:57', '2024-08-05 18:43:57'),
(9, 12, 25.99, 'Completed', '06N67154UY275740T', NULL, '2024-08-05 23:55:54', '2024-08-05 23:55:54'),
(10, 13, 20.34, 'Completed', '9R505771XP931405C', NULL, '2024-08-07 01:59:05', '2024-08-07 01:59:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `mobile_number` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `mobile_number`, `password`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Krunal', 'Chhabhaya', '5197817326', '$2y$10$7I15W6Y9R2TVxfaupDiBru1hMezEbmkcnm1n1SQY.1bzpCa0hYKhy', 'krunalchhabhaya7803@gmail.com', '2024-06-29 21:14:06', '2024-06-29 21:14:06'),
(2, 'Test', 'User', '1234567890', '$2y$10$jGHkh5qsF1doDMckpt0BauF5bLaw7nlvw4V3cNa9L6KeIUTkvZaxG', 'test@user.com', '2024-07-02 18:56:10', '2024-07-02 18:56:10'),
(3, 'Jon', 'Doe', '5199521325', '$2y$10$jRzWk4RTFs59Kzq.zSLqR.ap1ejM/dqtdISVETSa9WcxELmnbFC2C', 'test@example.ca', '2024-07-29 04:46:08', '2024-07-29 04:46:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`color_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `variant_id` (`variant_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `color_id` (`color_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `promo_code`
--
ALTER TABLE `promo_code`
  ADD PRIMARY KEY (`promo_id`),
  ADD UNIQUE KEY `promo_name` (`promo_name`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`seller_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `promo_code`
--
ALTER TABLE `promo_code`
  MODIFY `promo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `seller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`),
  ADD CONSTRAINT `order_items_ibfk_4` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`seller_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`seller_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_variants_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`color_id`),
  ADD CONSTRAINT `product_variants_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
