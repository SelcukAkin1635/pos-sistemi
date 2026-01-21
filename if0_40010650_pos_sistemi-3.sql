-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: sql109.infinityfree.com
-- Üretim Zamanı: 21 Oca 2026, 17:37:06
-- Sunucu sürümü: 11.4.9-MariaDB
-- PHP Sürümü: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `if0_40010650_pos_sistemi`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `adisyonlar`
--

CREATE TABLE `adisyonlar` (
  `id` int(11) NOT NULL,
  `masa_id` int(11) DEFAULT NULL,
  `acilis_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `kapali` tinyint(1) DEFAULT 0,
  `onayli` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `adisyonlar`
--

INSERT INTO `adisyonlar` (`id`, `masa_id`, `acilis_tarihi`, `kapali`, `onayli`) VALUES
(160, 13, '2025-09-26 22:27:57', 1, 1),
(161, 1, '2025-09-27 07:31:08', 1, 1),
(162, 13, '2025-09-28 12:08:26', 0, 0),
(163, 5, '2025-09-28 12:26:59', 1, 1),
(164, 11, '2025-09-28 12:35:18', 1, 1),
(165, 4, '2025-09-28 12:44:45', 1, 1),
(166, 3, '2025-09-28 13:07:38', 1, 1),
(167, 5, '2025-09-28 14:46:17', 1, 1),
(172, 6, '2025-09-28 15:14:41', 0, 0),
(173, 7, '2025-10-24 17:11:11', 1, 1),
(174, 7, '2025-10-26 19:05:25', 1, 1),
(175, 8, '2025-10-26 19:28:06', 0, 0),
(176, 7, '2025-10-26 19:28:17', 1, 1),
(177, 5, '2025-10-31 11:56:17', 1, 1),
(178, 1, '2025-11-07 22:29:21', 1, 1),
(179, 1, '2025-11-07 22:33:43', 1, 1),
(180, 1, '2025-11-07 22:34:51', 0, 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `adisyon_detay`
--

CREATE TABLE `adisyon_detay` (
  `id` int(11) NOT NULL,
  `adisyon_id` int(11) DEFAULT NULL,
  `urun_id` int(11) DEFAULT NULL,
  `adet` int(11) DEFAULT 1,
  `aciklama` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `adisyon_detay`
--

INSERT INTO `adisyon_detay` (`id`, `adisyon_id`, `urun_id`, `adet`, `aciklama`) VALUES
(190, 160, 128, 8, NULL),
(191, 163, 122, 5, NULL),
(192, 164, 123, 9, NULL),
(193, 165, 122, 8, 'asdasdasdasdasdasd'),
(194, 165, 127, 5, 'asdasdasdsadas'),
(195, 166, 121, 8, 'a'),
(196, 166, 127, 8, NULL),
(197, 167, 122, 6, 'gazoz buzlu bardakta yanında nane ile servis edilsin.'),
(198, 173, 124, 5, NULL),
(199, 174, 125, 5, NULL),
(200, 176, 123, 8, NULL),
(201, 177, 123, 5, NULL),
(202, 161, 141, 1, NULL),
(203, 161, 145, 2, NULL),
(204, 178, 128, 60, 'Bol cilekli olsunssss'),
(205, 179, 225, 150, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `masalar`
--

CREATE TABLE `masalar` (
  `id` int(11) NOT NULL,
  `masa_no` int(11) NOT NULL,
  `durum` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `masalar`
--

INSERT INTO `masalar` (`id`, `masa_no`, `durum`) VALUES
(1, 1, 0),
(2, 2, 0),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 0),
(7, 7, 0),
(8, 8, 0),
(9, 9, 0),
(10, 10, 0),
(11, 11, 0),
(12, 12, 0),
(13, 13, 0),
(14, 14, 0),
(15, 15, 0),
(16, 16, 0),
(17, 17, 0),
(18, 18, 0),
(19, 19, 0),
(20, 20, 0),
(21, 21, 0),
(22, 22, 0),
(23, 23, 0),
(24, 24, 0),
(25, 25, 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `odemeler`
--

CREATE TABLE `odemeler` (
  `id` int(11) NOT NULL,
  `adisyon_id` int(11) DEFAULT NULL,
  `odeme_tutari` decimal(10,2) DEFAULT NULL,
  `odeme_yontemi` varchar(50) DEFAULT NULL,
  `tarih` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `odemeler`
--

INSERT INTO `odemeler` (`id`, `adisyon_id`, `odeme_tutari`, `odeme_yontemi`, `tarih`) VALUES
(70, 160, '345.60', 'Kart', '2025-09-26 22:28:19'),
(71, 164, '340.20', 'Kart', '2025-09-28 12:40:25'),
(72, 163, '135.00', 'Kart', '2025-09-28 12:45:54'),
(73, 176, '302.40', 'Kart', '2025-10-26 19:29:04'),
(74, 161, '166.32', 'Nakit', '2025-11-02 10:20:17'),
(75, 178, '2592.00', 'Kart', '2025-11-07 22:31:30');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler`
--

CREATE TABLE `urunler` (
  `id` int(11) NOT NULL,
  `urun_adi` varchar(100) DEFAULT NULL,
  `fiyat` decimal(10,2) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urunler`
--

INSERT INTO `urunler` (`id`, `urun_adi`, `fiyat`, `kategori`) VALUES
(119, 'Kola', '30.00', 'İçecekler'),
(120, 'Ayran', '20.00', 'İçecekler'),
(121, 'Su', '10.00', 'İçecekler'),
(122, 'Gazoz', '25.00', 'İçecekler'),
(123, 'Meyve Suyu', '35.00', 'İçecekler'),
(124, 'Limonata', '28.00', 'İçecekler'),
(125, 'Soğuk Çay', '32.00', 'İçecekler'),
(126, 'Soda', '15.00', 'İçecekler'),
(127, 'Enerji İçeceği', '45.00', 'İçecekler'),
(128, 'Milkshake', '40.00', 'İçecekler'),
(129, 'Çay', '15.00', 'Sıcak İçecekler'),
(130, 'Türk Kahvesi', '30.00', 'Sıcak İçecekler'),
(131, 'Espresso', '35.00', 'Sıcak İçecekler'),
(132, 'Latte', '45.00', 'Sıcak İçecekler'),
(133, 'Cappuccino', '42.00', 'Sıcak İçecekler'),
(134, 'Sıcak Çikolata', '38.00', 'Sıcak İçecekler'),
(135, 'Americano', '40.00', 'Sıcak İçecekler'),
(136, 'Filtre Kahve', '32.00', 'Sıcak İçecekler'),
(137, 'Bitki Çayı', '28.00', 'Sıcak İçecekler'),
(138, 'Salep', '36.00', 'Sıcak İçecekler'),
(139, 'Mercimek Çorbası', '55.00', 'Çorbalar'),
(140, 'Ezogelin Çorbası', '58.00', 'Çorbalar'),
(141, 'Domates Çorbası', '50.00', 'Çorbalar'),
(142, 'Tavuk Çorbası', '60.00', 'Çorbalar'),
(143, 'Mantar Çorbası', '62.00', 'Çorbalar'),
(144, 'Yoğurt Çorbası', '48.00', 'Çorbalar'),
(145, 'Tarhana Çorbası', '52.00', 'Çorbalar'),
(146, 'Balık Çorbası', '70.00', 'Çorbalar'),
(147, 'Sebze Çorbası', '54.00', 'Çorbalar'),
(148, 'İşkembe Çorbası', '75.00', 'Çorbalar'),
(149, 'Çoban Salata', '65.00', 'Salatalar'),
(150, 'Mevsim Salata', '60.00', 'Salatalar'),
(151, 'Sezar Salata', '95.00', 'Salatalar'),
(152, 'Ton Balıklı Salata', '100.00', 'Salatalar'),
(153, 'Peynirli Salata', '80.00', 'Salatalar'),
(154, 'Tavuklu Salata', '90.00', 'Salatalar'),
(155, 'Roka Salatası', '70.00', 'Salatalar'),
(156, 'Avokado Salatası', '110.00', 'Salatalar'),
(157, 'Yunan Salatası', '85.00', 'Salatalar'),
(158, 'Kısır', '55.00', 'Salatalar'),
(159, 'Izgara Tavuk', '130.00', 'Ana Yemekler'),
(160, 'Köfte', '140.00', 'Ana Yemekler'),
(161, 'Bonfile', '220.00', 'Ana Yemekler'),
(162, 'Biftek', '200.00', 'Ana Yemekler'),
(163, 'Kuzu Pirzola', '230.00', 'Ana Yemekler'),
(164, 'Tavuk Şiş', '135.00', 'Ana Yemekler'),
(165, 'Adana Kebap', '160.00', 'Ana Yemekler'),
(166, 'Urfa Kebap', '160.00', 'Ana Yemekler'),
(167, 'Karışık Izgara', '250.00', 'Ana Yemekler'),
(168, 'Balık Izgara', '210.00', 'Ana Yemekler'),
(169, 'Margarita', '120.00', 'Pizzalar'),
(170, 'Sucuklu Pizza', '140.00', 'Pizzalar'),
(171, 'Karışık Pizza', '160.00', 'Pizzalar'),
(172, 'Vejetaryen Pizza', '135.00', 'Pizzalar'),
(173, 'Tavuklu Pizza', '150.00', 'Pizzalar'),
(174, 'Peynirli Pizza', '145.00', 'Pizzalar'),
(175, 'BBQ Tavuk Pizza', '170.00', 'Pizzalar'),
(176, 'Ton Balıklı Pizza', '165.00', 'Pizzalar'),
(177, 'Pepperoni Pizza', '180.00', 'Pizzalar'),
(178, 'Mantarlı Pizza', '155.00', 'Pizzalar'),
(179, 'Klasik Hamburger', '110.00', 'Hamburgerler'),
(180, 'Cheeseburger', '120.00', 'Hamburgerler'),
(181, 'Double Burger', '150.00', 'Hamburgerler'),
(182, 'Tavuk Burger', '115.00', 'Hamburgerler'),
(183, 'Veggie Burger', '125.00', 'Hamburgerler'),
(184, 'BBQ Burger', '135.00', 'Hamburgerler'),
(185, 'Bacon Burger', '145.00', 'Hamburgerler'),
(186, 'Texas Burger', '160.00', 'Hamburgerler'),
(187, 'Fish Burger', '130.00', 'Hamburgerler'),
(188, 'Jalapeno Burger', '140.00', 'Hamburgerler'),
(189, 'Cheesecake', '90.00', 'Tatlılar'),
(190, 'Tiramisu', '95.00', 'Tatlılar'),
(191, 'Sufle', '85.00', 'Tatlılar'),
(192, 'Baklava', '100.00', 'Tatlılar'),
(193, 'Künefe', '105.00', 'Tatlılar'),
(194, 'Profiterol', '80.00', 'Tatlılar'),
(195, 'Magnolia', '85.00', 'Tatlılar'),
(196, 'Dondurma', '70.00', 'Tatlılar'),
(197, 'Meyve Tabağı', '75.00', 'Tatlılar'),
(198, 'Pasta Dilimi', '65.00', 'Tatlılar'),
(199, 'Humus', '60.00', 'Mezeler'),
(200, 'Haydari', '55.00', 'Mezeler'),
(201, 'Acılı Ezme', '50.00', 'Mezeler'),
(202, 'Cacık', '45.00', 'Mezeler'),
(203, 'Şakşuka', '65.00', 'Mezeler'),
(204, 'Kısır', '55.00', 'Mezeler'),
(205, 'Patlıcan Ezme', '70.00', 'Mezeler'),
(206, 'Fava', '60.00', 'Mezeler'),
(207, 'Zeytinyağlı Yaprak Sarma', '75.00', 'Mezeler'),
(208, 'Biber Dolması', '70.00', 'Mezeler'),
(209, 'Serpme Kahvaltı (2 Kişilik)', '250.00', 'Kahvaltılıklar'),
(210, 'Menemen', '80.00', 'Kahvaltılıklar'),
(211, 'Omlet', '70.00', 'Kahvaltılıklar'),
(212, 'Sucuklu Yumurta', '85.00', 'Kahvaltılıklar'),
(213, 'Peynir Tabağı', '90.00', 'Kahvaltılıklar'),
(214, 'Zeytin Tabağı', '65.00', 'Kahvaltılıklar'),
(215, 'Bal Kaymak', '95.00', 'Kahvaltılıklar'),
(216, 'Kahvaltı Tabağı', '120.00', 'Kahvaltılıklar'),
(217, 'Gözleme', '100.00', 'Kahvaltılıklar'),
(218, 'Simit & Peynir', '55.00', 'Kahvaltılıklar'),
(219, 'Mocha', '80.00', 'Sıcak İçecekler'),
(220, 'Supangle', '40.00', 'Tatlılar'),
(221, 'Mozaik Pasta', '75.00', 'Tatlılar'),
(222, 'Sütlaç', '60.00', 'Tatlılar'),
(223, 'Kazandibi', '70.00', 'Tatlılar');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','kasiyer','garson','mutfak') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'garson1', '81dc9bdb52d04dc20036dbd8313ed055', 'garson'),
(2, 'kasiyer1', '81dc9bdb52d04dc20036dbd8313ed055', 'kasiyer'),
(3, 'admin1', '81dc9bdb52d04dc20036dbd8313ed055', 'admin'),
(4, 'SelçukA', '81dc9bdb52d04dc20036dbd8313ed055', 'kasiyer'),
(7, 'mutfak1', '81dc9bdb52d04dc20036dbd8313ed055', 'mutfak');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `adisyonlar`
--
ALTER TABLE `adisyonlar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `masa_id` (`masa_id`);

--
-- Tablo için indeksler `adisyon_detay`
--
ALTER TABLE `adisyon_detay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adisyon_id` (`adisyon_id`),
  ADD KEY `urun_id` (`urun_id`);

--
-- Tablo için indeksler `masalar`
--
ALTER TABLE `masalar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `odemeler`
--
ALTER TABLE `odemeler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `urunler`
--
ALTER TABLE `urunler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `adisyonlar`
--
ALTER TABLE `adisyonlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- Tablo için AUTO_INCREMENT değeri `adisyon_detay`
--
ALTER TABLE `adisyon_detay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- Tablo için AUTO_INCREMENT değeri `masalar`
--
ALTER TABLE `masalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Tablo için AUTO_INCREMENT değeri `odemeler`
--
ALTER TABLE `odemeler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Tablo için AUTO_INCREMENT değeri `urunler`
--
ALTER TABLE `urunler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `adisyonlar`
--
ALTER TABLE `adisyonlar`
  ADD CONSTRAINT `adisyonlar_ibfk_1` FOREIGN KEY (`masa_id`) REFERENCES `masalar` (`id`);

--
-- Tablo kısıtlamaları `adisyon_detay`
--
ALTER TABLE `adisyon_detay`
  ADD CONSTRAINT `adisyon_detay_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
