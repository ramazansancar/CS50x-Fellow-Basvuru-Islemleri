-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 07 Ağu 2020, 15:25:47
-- Sunucu sürümü: 10.1.32-MariaDB
-- PHP Sürümü: 5.6.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `formlar`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `katilimcilar`
--

CREATE TABLE `katilimcilar` (
  `uye_id` int(11) NOT NULL,
  `uye_tarih` datetime NOT NULL,
  `uye_mail` varchar(255) NOT NULL,
  `uye_adsoyad` varchar(255) NOT NULL,
  `uye_sehir` varchar(25) NOT NULL,
  `uye_cinsiyet` varchar(50) NOT NULL,
  `uye_telefon` varchar(20) NOT NULL,
  `uye_dogum_tarihi` date NOT NULL,
  `uye_kariyer_okul` longtext NOT NULL,
  `uye_aciklama` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `katilimcilar`
--
ALTER TABLE `katilimcilar`
  ADD PRIMARY KEY (`uye_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `katilimcilar`
--
ALTER TABLE `katilimcilar`
  MODIFY `uye_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
