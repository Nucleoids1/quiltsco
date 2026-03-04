-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2026 at 11:15 AM
-- Server version: 8.4.8
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `location`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_canada_provinces`
--

CREATE TABLE IF NOT EXISTS `data_canada_provinces` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `iso2` char(2) NOT NULL,
  `name` char(80) NOT NULL,
  `name_english` char(80) NOT NULL,
  `name_french` char(80) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `iso2` (`iso2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_countries`
--

CREATE TABLE IF NOT EXISTS `data_countries` (
  `iso3` char(3) NOT NULL DEFAULT '',
  `iso2` char(2) DEFAULT NULL,
  `tld` char(2) DEFAULT NULL,
  `name` char(50) DEFAULT NULL,
  `isono` varchar(255) DEFAULT NULL,
  `capital` char(50) DEFAULT NULL,
  `region` char(50) DEFAULT NULL,
  `currency` char(50) DEFAULT NULL,
  `currency_code` char(3) DEFAULT NULL,
  `population` int DEFAULT NULL,
  PRIMARY KEY (`iso3`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_countries_allow`
--

CREATE TABLE IF NOT EXISTS `data_countries_allow` (
  `iso2` char(2) NOT NULL,
  PRIMARY KEY (`iso2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_countries_bad`
--

CREATE TABLE IF NOT EXISTS `data_countries_bad` (
  `iso2` char(2) NOT NULL,
  PRIMARY KEY (`iso2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_usa_states`
--

CREATE TABLE IF NOT EXISTS `data_usa_states` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `iso2` char(2) NOT NULL,
  `name` char(80) NOT NULL,
  `name_english` char(80) NOT NULL,
  `name_french` char(80) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `iso2` (`iso2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip2location`
--

CREATE TABLE IF NOT EXISTS `ip2location` (
  `ip_from` int UNSIGNED NOT NULL,
  `ip_to` int UNSIGNED NOT NULL,
  `country` char(2) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`ip_from`,`ip_to`),
  UNIQUE KEY `ip_to` (`ip_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip2location_full`
--

CREATE TABLE IF NOT EXISTS `ip2location_full` (
  `ip_from` int UNSIGNED NOT NULL,
  `ip_to` int UNSIGNED NOT NULL,
  `country` char(2) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `region` varchar(64) NOT NULL,
  `city` varchar(64) NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `zip` varchar(64) NOT NULL,
  `timezone` varchar(64) NOT NULL,
  PRIMARY KEY (`ip_from`,`ip_to`),
  UNIQUE KEY `ip_to` (`ip_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maxmind_countries`
--

CREATE TABLE IF NOT EXISTS `maxmind_countries` (
  `i_from` char(15) NOT NULL,
  `i_to` char(15) NOT NULL,
  `ip14` smallint UNSIGNED NOT NULL,
  `ip_from` int UNSIGNED NOT NULL,
  `ip_to` int UNSIGNED NOT NULL,
  `country_code2` char(2) NOT NULL,
  `country_name` varchar(50) NOT NULL,
  KEY `IP14` (`ip14`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maxmind_location`
--

CREATE TABLE IF NOT EXISTS `maxmind_location` (
  `locId` int UNSIGNED NOT NULL,
  `country` char(3) NOT NULL,
  `region` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `postalCode` varchar(16) NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `metroCode` smallint UNSIGNED NOT NULL,
  `areaCode` smallint UNSIGNED NOT NULL,
  PRIMARY KEY (`locId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maxmind_location_blocks`
--

CREATE TABLE IF NOT EXISTS `maxmind_location_blocks` (
  `ip14` smallint UNSIGNED NOT NULL,
  `ip_from` int UNSIGNED NOT NULL,
  `ip_to` int UNSIGNED NOT NULL,
  `locId` int UNSIGNED NOT NULL,
  KEY `IP14` (`ip14`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
