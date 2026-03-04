-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2026 at 03:42 AM
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
-- Database: `quiltsco`
--

-- --------------------------------------------------------

--
-- Table structure for table `community`
--

CREATE TABLE IF NOT EXISTS `community` (
  `community_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `community_name` char(32) NOT NULL,
  `community_creator` int UNSIGNED NOT NULL,
  `community_created_on` datetime NOT NULL,
  `community_deleted` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`community_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_banned_ips`
--

CREATE TABLE IF NOT EXISTS `community_banned_ips` (
  `community_id` smallint UNSIGNED NOT NULL DEFAULT '0',
  `ip` int(10) UNSIGNED ZEROFILL NOT NULL DEFAULT '0000000000',
  `added_on` timestamp NULL DEFAULT NULL,
  `last_used_on` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `expires_on` datetime DEFAULT NULL,
  PRIMARY KEY (`community_id`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_banned_users`
--

CREATE TABLE IF NOT EXISTS `community_banned_users` (
  `community_id` int UNSIGNED NOT NULL DEFAULT '0',
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `added_on` timestamp NULL DEFAULT NULL,
  `last_used_on` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `expires_on` datetime DEFAULT NULL,
  PRIMARY KEY (`community_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_forums`
--

CREATE TABLE IF NOT EXISTS `community_forums` (
  `forum_id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `section_id` int UNSIGNED NOT NULL,
  `forum_name_english` char(40) NOT NULL,
  `forum_name_french` char(40) NOT NULL,
  `forum_description_english` char(255) NOT NULL,
  `forum_description_french` char(255) NOT NULL,
  `forum_order_id` tinyint UNSIGNED NOT NULL,
  `forum_deleted` tinyint UNSIGNED NOT NULL,
  `forum_locked` tinyint UNSIGNED NOT NULL,
  `forum_automated` tinyint UNSIGNED NOT NULL,
  `forum_threads` int UNSIGNED NOT NULL,
  `forum_messages` int UNSIGNED NOT NULL,
  `forum_last_message_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`forum_id`),
  KEY `section_id` (`section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_forums_permissions`
--

CREATE TABLE IF NOT EXISTS `community_forums_permissions` (
  `forum_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `permission` char(15) NOT NULL,
  PRIMARY KEY (`forum_id`,`user_id`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_messages`
--

CREATE TABLE IF NOT EXISTS `community_messages` (
  `message_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `thread_id` int UNSIGNED NOT NULL,
  `message_user_id` int UNSIGNED NOT NULL,
  `message_posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `message_ip` int(10) UNSIGNED ZEROFILL NOT NULL,
  `message_mood` int UNSIGNED NOT NULL,
  `message_deleted` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`message_id`),
  UNIQUE KEY `reply_id` (`thread_id`,`message_id`),
  KEY `message_user_id` (`message_user_id`,`thread_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_messages_bodies`
--

CREATE TABLE IF NOT EXISTS `community_messages_bodies` (
  `message_id` int UNSIGNED NOT NULL,
  `message_body` mediumtext NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_messages_rating`
--

CREATE TABLE IF NOT EXISTS `community_messages_rating` (
  `message_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `vote` tinyint NOT NULL,
  PRIMARY KEY (`message_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_messages_updates`
--

CREATE TABLE IF NOT EXISTS `community_messages_updates` (
  `update_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_id` int UNSIGNED NOT NULL,
  `update_user_id` int UNSIGNED NOT NULL,
  `update_body` mediumtext NOT NULL,
  `update_posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_ip` int(10) UNSIGNED ZEROFILL NOT NULL,
  PRIMARY KEY (`update_id`),
  UNIQUE KEY `message_id` (`message_id`,`update_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_permissions`
--

CREATE TABLE IF NOT EXISTS `community_permissions` (
  `community_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `permission` char(32) NOT NULL,
  PRIMARY KEY (`community_id`,`user_id`,`permission`),
  KEY `permission` (`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_sections`
--

CREATE TABLE IF NOT EXISTS `community_sections` (
  `section_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `community_id` int UNSIGNED NOT NULL,
  `section_order_id` tinyint UNSIGNED NOT NULL,
  `section_name_english` char(40) NOT NULL,
  `section_name_french` char(40) NOT NULL,
  `section_deleted` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`section_id`),
  KEY `community_id` (`community_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_sections_permissions`
--

CREATE TABLE IF NOT EXISTS `community_sections_permissions` (
  `section_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `permission` char(15) NOT NULL,
  PRIMARY KEY (`section_id`,`user_id`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_smileys`
--

CREATE TABLE IF NOT EXISTS `community_smileys` (
  `name` char(10) NOT NULL,
  `filename` char(15) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_threads`
--

CREATE TABLE IF NOT EXISTS `community_threads` (
  `thread_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `forum_id` int UNSIGNED NOT NULL,
  `thread_user_id` int UNSIGNED NOT NULL,
  `thread_posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `thread_last_user_id` int UNSIGNED NOT NULL,
  `thread_last_posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `thread_title` char(70) NOT NULL,
  `thread_sticky` tinyint NOT NULL,
  `thread_locked` tinyint UNSIGNED NOT NULL,
  `thread_views` int UNSIGNED NOT NULL,
  `thread_messages` int UNSIGNED NOT NULL,
  `thread_vote` int UNSIGNED NOT NULL,
  `thread_quilt` int UNSIGNED NOT NULL,
  `thread_tile` int UNSIGNED NOT NULL,
  `thread_first_message_id` int UNSIGNED NOT NULL,
  `thread_last_message_id` int UNSIGNED NOT NULL,
  `thread_deleted` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`thread_id`),
  KEY `user_id` (`thread_user_id`),
  KEY `forum_id` (`forum_id`,`thread_sticky`,`thread_last_posted_on`),
  KEY `thread_quilt` (`thread_quilt`),
  KEY `thread_tile` (`thread_tile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_threads_categories`
--

CREATE TABLE IF NOT EXISTS `community_threads_categories` (
  `id` tinyint UNSIGNED NOT NULL,
  `name` char(20) NOT NULL,
  `worksafe` tinyint UNSIGNED NOT NULL,
  `positive` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_threads_pointers`
--

CREATE TABLE IF NOT EXISTS `community_threads_pointers` (
  `thread_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `message_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`thread_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_threads_ratings`
--

CREATE TABLE IF NOT EXISTS `community_threads_ratings` (
  `thread_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `category_id` tinyint UNSIGNED NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`thread_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `user_id` mediumint UNSIGNED NOT NULL,
  `friend_id` mediumint UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`,`friend_id`),
  KEY `friend_id` (`friend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE IF NOT EXISTS `gallery_images` (
  `image_id` mediumint UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`image_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geo_cities`
--

CREATE TABLE IF NOT EXISTS `geo_cities` (
  `city_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `city_name` char(40) NOT NULL DEFAULT '',
  `region_id` int UNSIGNED DEFAULT '0',
  `latitude` double(10,6) DEFAULT '0.000000',
  `longitude` double(10,6) DEFAULT '0.000000',
  PRIMARY KEY (`city_id`),
  UNIQUE KEY `city_name` (`city_name`,`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geo_countries`
--

CREATE TABLE IF NOT EXISTS `geo_countries` (
  `country_id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_code` char(2) DEFAULT NULL,
  `country_name` char(25) DEFAULT NULL,
  `reg_name` char(20) DEFAULT NULL,
  PRIMARY KEY (`country_id`),
  UNIQUE KEY `country_code` (`country_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geo_regions`
--

CREATE TABLE IF NOT EXISTS `geo_regions` (
  `region_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `region_name` char(40) NOT NULL DEFAULT '',
  `country_id` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `country_name` char(40) DEFAULT NULL,
  PRIMARY KEY (`region_id`),
  KEY `country_id` (`country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ignored`
--

CREATE TABLE IF NOT EXISTS `ignored` (
  `user_id` mediumint UNSIGNED NOT NULL,
  `link_id` mediumint UNSIGNED NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`,`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `width` int UNSIGNED NOT NULL DEFAULT '0',
  `height` int UNSIGNED NOT NULL DEFAULT '0',
  `file_type` char(16) NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `commented_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `views` int UNSIGNED NOT NULL DEFAULT '0',
  `views_ip` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Table structure for table `images_binaries_00000001`
--

CREATE TABLE IF NOT EXISTS `images_binaries_00000001` (
  `image_id` mediumint UNSIGNED NOT NULL,
  `full` mediumblob NOT NULL,
  `thumb` mediumblob NOT NULL,
  `original` mediumblob NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Table structure for table `images_binaries_rotate`
--

CREATE TABLE IF NOT EXISTS `images_binaries_rotate` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `image_id` mediumint UNSIGNED NOT NULL,
  `full` mediumblob NOT NULL,
  `thumb` mediumblob NOT NULL,
  `original` mediumblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images_categories`
--

CREATE TABLE IF NOT EXISTS `images_categories` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL,
  `worksafe` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images_categories_rating`
--

CREATE TABLE IF NOT EXISTS `images_categories_rating` (
  `image_id` mediumint UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `category_id` tinyint UNSIGNED NOT NULL,
  `posted_on` datetime NOT NULL,
  PRIMARY KEY (`image_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images_comments`
--

CREATE TABLE IF NOT EXISTS `images_comments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `link_id` mediumint UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `parent` int UNSIGNED NOT NULL DEFAULT '0',
  `level` tinyint UNSIGNED NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent` (`parent`,`id`),
  KEY `link_id` (`link_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Table structure for table `images_hashes`
--

CREATE TABLE IF NOT EXISTS `images_hashes` (
  `image_id` mediumint UNSIGNED NOT NULL,
  `full_hash` char(40) NOT NULL,
  `full_size` int UNSIGNED NOT NULL,
  `thumb_hash` char(40) NOT NULL,
  `thumb_size` int UNSIGNED NOT NULL,
  `original_hash` char(40) NOT NULL,
  `original_size` int UNSIGNED NOT NULL,
  PRIMARY KEY (`image_id`),
  UNIQUE KEY `hash` (`full_hash`,`full_size`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images_rating`
--

CREATE TABLE IF NOT EXISTS `images_rating` (
  `image_id` mediumint UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `posted_on` datetime NOT NULL,
  `vote` tinyint NOT NULL,
  PRIMARY KEY (`image_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` char(25) NOT NULL,
  `email` char(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `main_image_id` mediumint UNSIGNED NOT NULL DEFAULT '0',
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `username` (`username`) USING BTREE,
  KEY `email` (`email`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Table structure for table `members_comments`
--

CREATE TABLE IF NOT EXISTS `members_comments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `link_id` mediumint UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `parent` int UNSIGNED NOT NULL DEFAULT '0',
  `posted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent` (`parent`,`id`),
  KEY `link_id` (`link_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Table structure for table `members_create`
--

CREATE TABLE IF NOT EXISTS `members_create` (
  `email` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` mediumint UNSIGNED NOT NULL,
  `cache` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ip` char(15) NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`),
  UNIQUE KEY `cache` (`cache`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`,`posted_on`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members_extras`
--

CREATE TABLE IF NOT EXISTS `members_extras` (
  `user_id` smallint UNSIGNED NOT NULL,
  `fullname` char(80) NOT NULL,
  `gender` tinyint UNSIGNED NOT NULL,
  `birthday` date NOT NULL,
  `country` char(25) NOT NULL,
  `region` char(40) NOT NULL,
  `city` char(40) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `profile` mediumtext NOT NULL,
  `website` char(255) NOT NULL,
  `aim` char(50) NOT NULL,
  `icq` int UNSIGNED NOT NULL,
  `msn` char(50) NOT NULL,
  `gtalk` char(50) NOT NULL,
  `yahoo` char(50) NOT NULL,
  `privacy` tinyint UNSIGNED NOT NULL,
  `notification` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members_laston`
--

CREATE TABLE IF NOT EXISTS `members_laston` (
  `user_id` smallint UNSIGNED NOT NULL DEFAULT '0',
  `laston` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`),
  KEY `laston` (`laston`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members_moods`
--

CREATE TABLE IF NOT EXISTS `members_moods` (
  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` smallint UNSIGNED NOT NULL,
  `mood` char(20) NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members_online`
--

CREATE TABLE IF NOT EXISTS `members_online` (
  `sha1` char(40) NOT NULL,
  `sha2` char(40) NOT NULL,
  `closed` tinyint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `firston` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `laston` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `page` longtext NOT NULL,
  `pageviews` int UNSIGNED NOT NULL DEFAULT '0',
  `ip` int UNSIGNED NOT NULL DEFAULT '0',
  `user_agent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`sha1`,`sha2`),
  KEY `laston` (`laston`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` smallint UNSIGNED NOT NULL DEFAULT '0',
  `recipiant_id` smallint UNSIGNED NOT NULL DEFAULT '0',
  `body` mediumtext NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `viewed` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `hidden` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `recipiant_id` (`recipiant_id`,`id`),
  KEY `sender_id` (`sender_id`,`id`),
  KEY `sender_id_2` (`sender_id`,`recipiant_id`,`id`),
  KEY `recipiant_id_2` (`recipiant_id`,`sender_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages_email`
--

CREATE TABLE IF NOT EXISTS `messages_email` (
  `user_id` int UNSIGNED NOT NULL,
  `link_id` int UNSIGNED NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages_index`
--

CREATE TABLE IF NOT EXISTS `messages_index` (
  `receiver_id` int UNSIGNED NOT NULL,
  `sender_id` int UNSIGNED NOT NULL,
  `last_received` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_sent` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `message_id_received` int UNSIGNED NOT NULL,
  `message_id_sent` int UNSIGNED NOT NULL,
  PRIMARY KEY (`receiver_id`,`last_received`,`last_sent`,`sender_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages_last`
--

CREATE TABLE IF NOT EXISTS `messages_last` (
  `user_id` mediumint UNSIGNED NOT NULL,
  `message_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `name` char(40) NOT NULL DEFAULT '',
  `type` char(1) NOT NULL,
  `filename` char(64) NOT NULL,
  `highlight` char(16) NOT NULL,
  `permission` char(20) NOT NULL DEFAULT '',
  `membership` tinyint UNSIGNED NOT NULL,
  `secure` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`name`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `user_id` smallint UNSIGNED NOT NULL,
  `permission` char(20) NOT NULL,
  `ip` char(15) NOT NULL,
  PRIMARY KEY (`user_id`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quilts`
--

CREATE TABLE IF NOT EXISTS `quilts` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL,
  `description` mediumtext NOT NULL,
  `quilt_width` smallint UNSIGNED NOT NULL,
  `quilt_height` smallint UNSIGNED NOT NULL,
  `tile_width` smallint UNSIGNED NOT NULL,
  `tile_height` smallint UNSIGNED NOT NULL,
  `score_minimum` int NOT NULL,
  `score_maximum` int NOT NULL,
  `timelimit` int UNSIGNED NOT NULL,
  `side_pixels` tinyint UNSIGNED NOT NULL,
  `level` tinyint UNSIGNED NOT NULL,
  `show_all` tinyint UNSIGNED NOT NULL,
  `work_on_all` tinyint UNSIGNED NOT NULL,
  `start_anywhere` tinyint UNSIGNED NOT NULL,
  `multiple` tinyint UNSIGNED NOT NULL,
  `photographs_allowed` tinyint UNSIGNED NOT NULL,
  `edge_wrap` tinyint UNSIGNED NOT NULL,
  `moderated` tinyint UNSIGNED NOT NULL,
  `active` tinyint UNSIGNED NOT NULL,
  `finished` tinyint UNSIGNED NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data_thumb` mediumblob NOT NULL,
  `data_full_jpg` mediumblob NOT NULL,
  `data_full_png` mediumblob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modified_on` (`modified_on`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Table structure for table `quilts_comments`
--

CREATE TABLE IF NOT EXISTS `quilts_comments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `link_id` mediumint UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `parent` int UNSIGNED NOT NULL DEFAULT '0',
  `posted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent` (`parent`,`id`),
  KEY `link_id` (`link_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Table structure for table `quilts_invites`
--

CREATE TABLE IF NOT EXISTS `quilts_invites` (
  `user_id` mediumint UNSIGNED NOT NULL,
  `quilt_id` mediumint UNSIGNED NOT NULL,
  `active` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`,`quilt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quilts_permissions`
--

CREATE TABLE IF NOT EXISTS `quilts_permissions` (
  `user_id` mediumint UNSIGNED NOT NULL,
  `quilt_id` mediumint UNSIGNED NOT NULL,
  `permission` char(16) NOT NULL,
  `active` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`,`quilt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_code_cache`
--

CREATE TABLE IF NOT EXISTS `security_code_cache` (
  `cache` bigint UNSIGNED NOT NULL,
  `code` char(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`cache`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_code_last`
--

CREATE TABLE IF NOT EXISTS `security_code_last` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `code` char(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stats_ips`
--

CREATE TABLE IF NOT EXISTS `stats_ips` (
  `ip` int(10) UNSIGNED ZEROFILL NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `country` char(2) NOT NULL,
  `used_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ip`,`user_id`),
  KEY `used_on` (`used_on`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tiles`
--

CREATE TABLE IF NOT EXISTS `tiles` (
  `tile_id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
  `quilt_id` mediumint UNSIGNED NOT NULL,
  `matrix_x` tinyint UNSIGNED NOT NULL,
  `matrix_y` tinyint UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `comment` char(255) NOT NULL,
  `started_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `seconds` int UNSIGNED NOT NULL,
  `borders` char(36) NOT NULL,
  `visibility` tinyint NOT NULL,
  `deleted` tinyint UNSIGNED NOT NULL,
  `views` bigint NOT NULL,
  `cache_display` int UNSIGNED NOT NULL,
  `data_tile` mediumblob NOT NULL,
  PRIMARY KEY (`tile_id`),
  KEY `quilt_id` (`quilt_id`,`deleted`,`matrix_x`,`matrix_y`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Table structure for table `tiles_comments`
--

CREATE TABLE IF NOT EXISTS `tiles_comments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `link_id` smallint UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `parent` int UNSIGNED NOT NULL DEFAULT '0',
  `posted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent` (`parent`,`id`),
  KEY `link_id` (`link_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tiles_pending`
--

CREATE TABLE IF NOT EXISTS `tiles_pending` (
  `quilt_id` mediumint UNSIGNED NOT NULL,
  `matrix_x` tinyint UNSIGNED NOT NULL,
  `matrix_y` tinyint UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `started_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `due_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `borders` char(36) NOT NULL,
  PRIMARY KEY (`quilt_id`,`matrix_x`,`matrix_y`),
  KEY `user_id` (`user_id`,`due_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tracker_bugs`
--

CREATE TABLE IF NOT EXISTS `tracker_bugs` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `category` int UNSIGNED NOT NULL,
  `priority` tinyint UNSIGNED NOT NULL DEFAULT '5',
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `assigned_to` int NOT NULL,
  `summary` char(80) NOT NULL,
  `description` mediumtext NOT NULL,
  `views` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tracker_bugs_categories`
--

CREATE TABLE IF NOT EXISTS `tracker_bugs_categories` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_english` char(80) NOT NULL,
  `category_french` char(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tracker_bugs_comments`
--

CREATE TABLE IF NOT EXISTS `tracker_bugs_comments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` mediumint UNSIGNED NOT NULL,
  `link_id` mediumint UNSIGNED NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`link_id`),
  KEY `link_id` (`link_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tracker_bugs_images`
--

CREATE TABLE IF NOT EXISTS `tracker_bugs_images` (
  `image_id` int UNSIGNED NOT NULL,
  `tracker_id` int UNSIGNED NOT NULL,
  `user_id` smallint UNSIGNED NOT NULL,
  `posted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY (`image_id`,`tracker_id`),
  KEY `event_id` (`tracker_id`),
  KEY `posted_on` (`posted_on`,`user_id`,`image_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tracker_bugs_status`
--

CREATE TABLE IF NOT EXISTS `tracker_bugs_status` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `status_english` char(40) NOT NULL,
  `status_french` char(40) NOT NULL,
  `class` char(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
