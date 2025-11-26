-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 26, 2025 lúc 10:38 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `onlinepetshop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`, `remember_token`, `token_expires`) VALUES
(1, 'admin', '$2y$10$Tn/0xi2s.OElsEgYTieQX.mcyzYyuBcGYHB8f3eQKkaBH8JRvhh8e', '2025-04-04 22:17:42', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `address` varchar(255) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(13, 2, 10, 1, '2025-04-06 17:45:06'),
(16, 1, 11, 1, '2025-04-22 17:45:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(30) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Dogs Food', NULL),
(2, 'Clothes', NULL),
(3, 'Toys', NULL),
(4, 'Bath', NULL),
(5, 'Accessories', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `email`, `address`, `city`, `postal_code`, `phone`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'user1 nguyen', 'user1@gmail.com', '68/2A', 'tphcm', '1111', '32434324', 115.96, 'delivered', '2025-04-05 23:01:04', '2025-04-05 23:24:01'),
(2, 1, 'user1 nguyen', 'user1@gmail.com', '68/2A', 'tphcm', '1111', '32434324', 9.99, 'delivered', '2025-04-05 23:23:33', '2025-04-06 17:36:26'),
(3, 2, 'user2', 'user2@gmail.com', '68/2A', 'tphcm', '1111', '32434324', 23.98, 'pending', '2025-04-06 02:03:42', '2025-04-06 02:03:42'),
(4, 1, 'user1 nguyen', 'user1@gmail.com', '68/2A', 'tphcm', '1112', '32434324', 115.97, 'processing', '2025-04-19 20:57:23', '2025-04-22 19:52:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 11, 1, 79.99),
(2, 1, 8, 3, 11.99),
(3, 2, 5, 1, 9.99),
(4, 3, 8, 2, 11.99),
(5, 4, 12, 2, 17.99),
(6, 4, 11, 1, 79.99);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `category_id` int(30) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `stock`, `featured`, `category_id`) VALUES
(1, 'PEDIGREE Dry Dog Kibble for Large Dogs - Beef and Vegetable Flavor', 'Specially formulated for large dogs, providing complete and balanced nutrition.', 29.99, 'public/images/products/6807fc6c1b4f3.jpg', 5, 0, 1),
(2, 'Ganador Dog Kibble for Puppies and Adult Dogs', 'Nutritionally balanced food suitable for both puppies and adult dogs.', 39.99, 'public\\images\\ganador.jpg', 5, 0, 1),
(3, 'KEOS Dog Kibble 1.5kg Bag - Keos+ Digestive Support with Beef and Chicken Flavor', 'High-quality dog kibble designed to support digestive health.', 45.99, 'public\\images\\keos.jpg', 5, 0, 1),
(4, 'Stainless Steel Anti-Tip Pet Bowl for Dogs and Cats', 'Suitable for feeding and drinking, providing convenience and stability during mealtime.', 19.99, 'public\\images\\batan.jpg', 5, 0, 5),
(5, 'Dog Chew Toy in Tennis Ball Shape, Helps Clean Dog\'s Teeth (1 Ball)', 'This chew toy is designed in the shape of a tennis ball, providing a fun and engaging way for dogs to chew while also helping to clean their teeth.\r\nPerfect for promoting dental health by reducing plaque and tartar buildup.\r\nIdeal for dogs that love to chew, ensuring both entertainment and oral hygiene.', 9.99, 'public\\images\\ball.jpg', 4, 0, 3),
(6, 'Olive Essence Pet Shampoo for Dogs and Cats - 450ml', 'Helps maintain soft, shiny, and healthy fur while providing a gentle cleanse.', 14.99, 'public\\images\\olive.jpg', 5, 0, 4),
(7, 'Leash and Collar Set for Dogs and Cats (1kg to 5kg) - 1cm Width, Durable Nylon Fabric, Safe and Reli', 'Suitable for pets weighing from 1kg to 5kg.\r\nMade of durable nylon fabric, ensuring strength and reliability.\r\n1cm wide leash and collar, designed for comfort and security.\r\nPerfect for daily walks, providing safety and control for your pets.\r\nProduced in Vietnam, ensuring high quality and craftsmanship.', 24.99, 'public\\images\\daydatcho.jpg', 5, 0, 2),
(8, 'Smartheart 400g - Grilled Beef Flavor Adult Dog Food - Thailand', 'Specially formulated for adult dogs with a delicious grilled beef flavor.', 11.99, 'public\\images\\smartheart.jpg', 0, 0, 1),
(9, 'Orgo Chew Bone for Dogs – Cleans Teeth and Freshens Breath', 'Specially designed to clean dogs\' teeth, helping to reduce plaque and tartar buildup.\r\nFreshens your dog\'s breath, keeping their mouth smelling pleasant.\r\nMade from durable, safe materials, perfect for satisfying your dog\'s chewing needs.', 12.99, 'public\\images\\xuong-gam.jpg', 5, 0, 1),
(10, 'Rubber Chew Bone for Dogs', 'A durable chew toy made of rubber, designed to satisfy your dog\'s natural chewing instinct.\r\nHelps promote dental health by reducing plaque and tartar buildup.\r\nSafe and non-toxic, perfect for dogs of all sizes.', 19.99, 'public\\images\\xuongdochoi.jpg', 5, 0, 3),
(11, 'Smartheart Thailand Dog Paté 400g', 'SmartHeart Dog Paté 400g - Beef & Chicken Flavor\r\nDelicious and Easy to Eat Beef & Chicken Flavor\r\nStorage: Keep in the refrigerator and use within 2 days after opening.\r\nNutritional Formula: SmartHeart Paté for dogs is formulated to meet the nutritional standards established by the Association of American Feed Control Officials (AAFCO) and is produced in an ISO 9001 certified facility.\r\nManufactured and Distributed by: Perfect Companion Vietnam Co., Ltd.\r\nFor Pet Use Only.\r\nStorage Instructions: Store the food in a cool, dry place. Avoid moisture. Once opened, keep in the refrigerator at or below 4°C and consume within 3 days.\r\nShelf Life: 3 years from the date of manufacture.', 79.99, 'public\\images\\pate.jpg', 3, 0, 1),
(12, 'SOS Bath Shampoo for Dogs and Cats 530ml helps smooth the fur and leaves a pleasant fragrance', 'Key Features: \r\n● Professional Fur Care Product: SOS Shampoo is specially designed to nourish and clean the fur of dogs and cats. \r\n● Soft and Smooth Fur: The special formula helps nourish the fur, making it soft and smooth, giving your pets a beautiful and healthy coat. \r\n● Deep Cleaning: SOS Shampoo removes dirt, residue, and unpleasant odors from the fur, keeping your pets clean and smelling fresh. \r\n● Specialized Shampoo for Dogs and Cats: This SOS Shampoo is formulated to keep your pet\'s fur soft and smooth. The deodorizing and antibacterial formula helps retain moisture in your pet\'s skin and leaves a long-lasting fragrance without harming their skin.\r\n\r\nDISTRIBUTOR: SOS Shampoo is exclusively distributed in Vietnam by Ky Nam Trading Development Co., Ltd.', 17.99, 'public\\images\\sos.jpg', 3, 1, 4),
(18, 'Thức Ăn Hạt Cho Chó Con Poodle Royal Canin Poodle Puppy', 'Thương hiệu: Royal Canin\r\nPhù hợp cho: Chó con Poodle (2 - 10 tháng tuổi)\r\nRoyal Canin Poodle Puppy là loại thức ăn cho chó dinh dưỡng được thiết kế dành riêng cho chó con Poodle của bạn. Thức ăn Poodle này tùy chỉnh được thiết kế cho mõm và hàm thẳng của Poodle, giúp chúng dễ dàng nhặt và nhai. Hạt Royal Canin chứa một hỗn hợp các chất chống oxy hóa độc quyền và vitamin E hỗ trợ hệ thống miễn dịch đang phát triển của Poodle puppy và giữ cho cơ thể chó phát triển khỏe mạnh. Royal Canin Poodle còn hỗ trợ sức khỏe của da và lông cũng như chăm sóc hệ tiêu hóa trong giai đoạn chó con của Poodle. Khi Poodle của bạn hơn 10 tháng tuổi, hãy chuyển sang các loại hạt cho Poodle khác của Royal Canin để có đầy đủ dinh dưỡng cho những năm trường thành tiếp theo.\r\nLợi ích:\r\n\r\nGIÚP CHÓ PHÁT TRIỂN KHỎE MẠNH: Một phức hợp chất chống oxy hóa độc quyền, bao gồm vitamin E, giúp hỗ trợ sự phát triển của hệ thống miễn dịch ở chó con.\r\nTỐT CHO DA VÀ LÔNG:  EPA và DHA từ dầu cá giúp thúc đẩy làn da và bộ lông khỏe mạnh giúp nuôi dưỡng bộ lông xoăn đang phát triển của chó con\r\nHỖ TRỢ TIÊU HÓA: Hỗ trợ tiêu hóa khỏe mạnh ở chó con và thúc đẩy chất lượng phân tối ưu với protein và prebiotic chất lượng cao.\r\nHÌNH DÁNG KIBBLE CHUYÊN DỤNG: Thiết kế kibble độc đáo giúp Poodle dễ dàng nhặt và nhai thức ăn. \r\nThành phần dinh dưỡng\r\n\r\nThành phần: Bột phụ phẩm từ thịt gà, gạo nấu bia, gluten lúa mì, mỡ gà, ngô, bột gluten ngô, lúa mì, hương vị tự nhiên, bột củ cải khô, dầu cá, monocalcium phosphate, dầu thực vật, natri silico aluminat, canxi sulfat, kali clorua, vỏ hạt mã đề, canxi cacbonat, muối, fructooligosacarit, natri tripolyphotphat, vitamin [DL-alpha tocopherol axetat (nguồn vitamin E), chất bổ sung niacin, L-ascorbyl-2-polyphotphat (nguồn vitamin C), D-canxi pantothenate, biotin, pyridoxine hydrochloride (vitamin B6), bổ sung riboflavin, thiamine mononitrate (vitamin B1), vitamin A axetat, axit folic, bổ sung vitamin B12, bổ sung vitamin D3], men thủy phân (nguồn betaglucans), DL-methionine, L-lysine , choline clorua, taurine, cystine, khoáng chất vi lượng [kẽm proteinate, kẽm oxit, mangan proteinate, sắt sulfat, mangan oxit, đồng sulfat, canxi iodate, natri selenit, đồng proteinate], chiết xuất cúc vạn thọ (Tagetes erecta L.), yucca schidigera chiết xuất, L-carnitine, carotene, chiết xuất hương thảo, được bảo quản bằng hỗn hợp tocopherols và axit xitric.', 20.00, 'public/images/products/6807fd20b8f15.png', 5, 1, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `services`
--

INSERT INTO `services` (`id`, `name`, `price`, `duration_minutes`, `description`) VALUES
(1, 'Full Grooming', 25.00, 60, 'Bath, haircut, and nail trim'),
(2, 'Pet Spa & Massage', 40.00, 60, 'Relaxing massage and aromatic bath'),
(3, 'Health Checkup', 15.00, 60, 'Basic health screening at home');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(50) DEFAULT 'groomer',
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `role`, `is_active`) VALUES
(1, 'Alice Smith', 'alice@woofwoof.com', 'Groomer', 1),
(2, 'Bob Jones', 'bob@woofwoof.com', 'Trainer', 1),
(3, 'Dr. Sarah Lee', 'sarah@woofwoof.com', 'Veterinarian', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `store_locations`
--

CREATE TABLE `store_locations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `hours` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `state` varchar(50) DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `full_name`, `phone`, `address`, `city`, `postal_code`, `updated_at`, `state`, `last_activity`, `last_login`, `avatar`, `google_id`, `remember_token`, `token_expires`) VALUES
(1, 'user1', 'user1@gmail.com', '$2y$10$2MzpotsMrUbSAx5TYkMfQufy7IRwBubWIlI4g/ZCfrbnNqzyvVcAC', '2025-04-04 21:49:36', 'user1 nguyen', '32434324', '68/2A', 'tphcm', '1112', '2025-11-26 09:36:09', NULL, '2025-11-26 09:36:09', '2025-11-26 09:34:02', 'public/images/avatars/avatar_1_67f1c09bb795b.jpg', NULL, NULL, NULL),
(2, 'user2', 'user2@gmail.com', '$2y$10$6J4TvmWFCA2o08k4QamEy.5U7BaB5XAe4goou2BVaq2TEKZmyiSgS', '2025-04-05 17:00:17', NULL, NULL, NULL, NULL, NULL, '2025-04-06 22:59:19', NULL, '2025-04-06 22:59:19', NULL, 'public/images/avatars/avatar_2_67f1e05ab1d89.jpg', NULL, NULL, NULL),
(3, 'phankhanhnhan01', 'phankhanhnhan01@gmail.com', '$2y$10$V2WKQTjIfSplFNKbheTSuuDTXYlMtncDEDQWhZzYI4w/eLeI770wS', '2025-04-06 01:32:38', 'Nhân Phan', NULL, NULL, NULL, NULL, '2025-11-26 09:29:08', NULL, '2025-11-26 09:29:08', '2025-11-26 09:28:28', 'https://lh3.googleusercontent.com/a/ACg8ocIbDIbyks-c0qGWVK-Vq44Xfus5vtRh0ro4k6aVLnHAORBIdg=s96-c', '103729317953199544120', NULL, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_fk` (`category_id`);

--
-- Chỉ mục cho bảng `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `store_locations`
--
ALTER TABLE `store_locations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `store_locations`
--
ALTER TABLE `store_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
