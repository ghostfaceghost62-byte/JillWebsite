-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: hotelreservation_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(80) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,'LOGIN','user',1,'User logged in','::1','2026-08-18 10:35:40'),(2,6,'LOGIN','user',6,'User logged in','::1','2026-08-18 10:55:41'),(3,1,'LOGIN','user',1,'User logged in','::1','2026-08-18 10:57:24'),(4,6,'CREATE_HOTEL_RESERVATION','reservation',1,'Created HTL-2026-000001','::1','2026-08-18 11:04:27'),(5,1,'LOGIN','user',1,'User logged in','::1','2026-08-18 11:05:20'),(6,1,'RESERVATION_CONFIRMED','reservation',1,'Updated HTL-2026-000001','::1','2026-08-18 11:05:24'),(7,1,'RESERVATION_CONFIRMED','reservation',1,'Updated HTL-2026-000001','::1','2026-08-18 11:05:26'),(8,6,'LOGIN','user',6,'User logged in','::1','2026-08-18 11:05:30'),(9,6,'LOGIN','user',6,'User logged in','::1','2026-08-21 15:35:28'),(10,6,'CREATE_HOTEL_RESERVATION','reservation',2,'Created HTL-2026-000002','::1','2026-08-21 15:35:53'),(11,6,'LOGIN','user',6,'User logged in','::1','2026-09-03 16:14:09'),(12,6,'LOGIN','user',6,'User logged in','::1','2026-09-03 16:31:19'),(13,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 16:31:29'),(14,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 16:34:34'),(15,1,'RESERVATION_CONFIRMED','reservation',2,'Updated HTL-2026-000002','::1','2026-09-03 16:34:46'),(16,1,'RESERVATION_CONFIRMED','reservation',2,'Updated HTL-2026-000002','::1','2026-09-03 16:34:47'),(17,1,'RESERVATION_CONFIRMED','reservation',2,'Updated HTL-2026-000002','::1','2026-09-03 16:34:48'),(18,1,'RESERVATION_CONFIRMED','reservation',2,'Updated HTL-2026-000002','::1','2026-09-03 16:34:49'),(19,1,'RESERVATION_CONFIRMED','reservation',2,'Updated HTL-2026-000002','::1','2026-09-03 16:34:50'),(20,1,'RESERVATION_CHECKED_IN','reservation',2,'Updated HTL-2026-000002','::1','2026-09-03 16:34:53'),(21,6,'LOGIN','user',6,'User logged in','::1','2026-09-03 16:35:02'),(22,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 16:36:45'),(23,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 16:37:57'),(24,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 16:44:04'),(25,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 18:18:25'),(26,1,'RESERVATION_CONFIRMED','reservation',2,'Updated HTL-2026-000002','::1','2026-09-03 18:27:45'),(27,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 18:48:48'),(28,6,'LOGIN','user',6,'User logged in','::1','2026-09-03 18:50:55'),(29,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 19:55:21'),(30,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 20:18:18'),(31,6,'LOGIN','user',6,'User logged in','::1','2026-09-03 20:18:54'),(32,6,'LOGIN','user',6,'User logged in','::1','2026-09-03 20:22:52'),(33,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 20:23:05'),(34,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 20:39:44'),(35,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 20:50:53'),(36,6,'LOGIN','user',6,'User logged in','::1','2026-09-03 20:59:37'),(37,6,'LOGIN','user',6,'User logged in','::1','2026-09-03 21:04:56'),(38,7,'LOGIN','user',7,'User logged in','::1','2026-09-03 21:22:02'),(39,7,'CREATE_HOTEL_RESERVATION','reservation',3,'Created HTL-2026-000003','::1','2026-09-03 21:23:26'),(40,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 21:24:06'),(41,1,'RESERVATION_CONFIRMED','reservation',3,'Updated status of HTL-2026-000003 to CONFIRMED','::1','2026-09-03 21:24:41'),(42,7,'LOGIN','user',7,'User logged in','::1','2026-09-03 21:29:04'),(43,7,'CANCEL_RESERVATION','reservation',3,'Cancelled HTL-2026-000003','::1','2026-09-03 21:36:19'),(44,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 21:37:25'),(45,1,'RESERVATION_REJECTED','reservation',3,'Updated status of HTL-2026-000003 to REJECTED','::1','2026-09-03 22:10:32'),(46,1,'RESERVATION_CANCELLED','reservation',3,'Updated status of HTL-2026-000003 to CANCELLED','::1','2026-09-03 22:10:35'),(47,7,'LOGIN','user',7,'User logged in','::1','2026-09-03 22:33:27'),(48,7,'CREATE_HOTEL_RESERVATION','reservation',4,'Created HTL-2026-000004','::1','2026-09-03 22:34:11'),(49,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 22:34:26'),(50,7,'LOGIN','user',7,'User logged in','::1','2026-09-03 22:35:13'),(51,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 22:46:19'),(52,7,'LOGIN','user',7,'User logged in','::1','2026-09-03 22:48:05'),(53,7,'LOGIN','user',7,'User logged in','::1','2026-09-03 22:49:55'),(54,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 22:49:59'),(55,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 23:06:23'),(56,7,'LOGIN','user',7,'User logged in','::1','2026-09-03 23:06:28'),(57,7,'LOGIN','user',7,'User logged in','::1','2026-09-03 23:06:35'),(58,1,'LOGIN','user',1,'User logged in','::1','2026-09-03 23:17:56'),(59,7,'LOGIN','user',7,'User logged in','::1','2026-09-04 02:13:49'),(60,7,'CREATE_HOTEL_RESERVATION','reservation',5,'Created HTL-2026-000005','::1','2026-09-04 02:15:07'),(61,1,'LOGIN','user',1,'User logged in','::1','2026-09-04 02:15:22'),(62,1,'RESERVATION_CONFIRMED','reservation',5,'Updated status of HTL-2026-000005 to CONFIRMED','::1','2026-09-04 02:15:32'),(63,7,'LOGIN','user',7,'User logged in','::1','2026-09-04 02:15:42'),(64,1,'LOGIN','user',1,'User logged in','::1','2026-09-04 02:15:52'),(65,8,'LOGIN','user',8,'User logged in','::1','2026-09-04 02:19:24'),(66,1,'LOGIN','user',1,'User logged in','::1','2026-09-05 07:18:30'),(67,1,'RESERVATION_CHECKED_IN','reservation',5,'Updated status of HTL-2026-000005 to CHECKED_IN','::1','2026-09-05 07:20:25'),(68,1,'RESERVATION_CONFIRMED','reservation',5,'Updated status of HTL-2026-000005 to CONFIRMED','::1','2026-09-05 07:20:40'),(69,1,'UPDATE_ROOM_STATUS','room',1,'Set room status to OCCUPIED','::1','2026-09-05 07:20:49'),(70,1,'UPDATE_ROOM_STATUS','room',2,'Set room status to MAINTENANCE','::1','2026-09-05 07:21:01'),(71,1,'UPDATE_ROOM_STATUS','room',2,'Status set to NEEDS_CLEANING','::1','2026-09-05 07:22:40'),(72,1,'LOGIN','user',1,'User logged in','::1','2026-09-05 08:41:29'),(73,1,'LOGIN','user',1,'User logged in','::1','2026-09-05 08:44:31'),(74,1,'RESERVATION_CANCELLED','reservation',5,'Updated status of HTL-2026-000005 to CANCELLED','::1','2026-09-05 08:46:10'),(75,1,'RESERVATION_CHECKED_OUT','reservation',5,'Updated status of HTL-2026-000005 to CHECKED_OUT','::1','2026-09-05 08:46:20'),(76,1,'RESERVATION_REJECTED','reservation',5,'Updated status of HTL-2026-000005 to REJECTED','::1','2026-09-05 08:46:25'),(77,1,'RESERVATION_CANCELLED','reservation',3,'Updated status of HTL-2026-000003 to CANCELLED','::1','2026-09-05 08:46:29'),(78,1,'RESERVATION_CHECKED_IN','reservation',3,'Updated status of HTL-2026-000003 to CHECKED_IN','::1','2026-09-05 08:46:33'),(79,1,'RESERVATION_CHECKED_OUT','reservation',3,'Updated status of HTL-2026-000003 to CHECKED_OUT','::1','2026-09-05 08:46:38'),(80,1,'RESERVATION_CONFIRMED','reservation',1,'Updated status of HTL-2026-000001 to CONFIRMED','::1','2026-09-05 08:46:42'),(81,1,'UPDATE_ROOM_STATUS','room',1,'Set room status to CHECKED_OUT','::1','2026-09-05 08:47:00'),(82,1,'UPDATE_ROOM_STATUS','room',1,'Set room status to MAINTENANCE','::1','2026-09-05 08:47:06'),(83,1,'UPDATE_ROOM_STATUS','room',1,'Set room status to INACTIVE','::1','2026-09-05 08:47:18'),(84,9,'LOGIN','user',9,'User logged in','::1','2026-09-05 17:40:10'),(85,10,'LOGIN','user',10,'User logged in','::1','2026-09-05 17:41:50'),(86,12,'LOGIN','user',12,'User logged in','::1','2026-09-06 00:45:27'),(87,12,'CREATE_HOTEL_RESERVATION','reservation',6,'Created HTL-2026-000006','::1','2026-09-06 00:47:27'),(88,1,'LOGIN','user',1,'User logged in','::1','2026-09-06 00:51:34'),(89,1,'LOGIN','user',1,'User logged in','::1','2026-09-06 00:52:59'),(90,1,'LOGIN','user',1,'User logged in','::1','2026-09-12 07:06:36'),(91,14,'LOGIN','user',14,'User logged in','::1','2026-09-12 07:25:14'),(92,14,'CREATE_HOTEL_RESERVATION','reservation',7,'Created HTL-2026-000007','::1','2026-09-12 07:27:53'),(93,14,'MODIFY_RESERVATION','reservation',7,'Modified dates/guests for HTL-2026-000007','::1','2026-09-12 07:28:43'),(94,1,'LOGIN','user',1,'User logged in','::1','2026-09-12 07:29:40'),(95,1,'RESERVATION_CONFIRMED','reservation',7,'Updated status of HTL-2026-000007 to CONFIRMED','::1','2026-09-12 07:31:36'),(96,1,'UPDATE_ROOM_STATUS','room',1,'Status set to AVAILABLE','::1','2026-09-12 07:33:13'),(97,14,'LOGIN','user',14,'User logged in','::1','2026-09-12 07:49:06'),(98,7,'LOGIN','user',7,'User logged in','::1','2026-09-12 08:30:30'),(99,7,'LOGIN','user',7,'User logged in','::1','2026-09-12 08:41:33'),(100,1,'LOGIN','user',1,'User logged in','::1','2026-09-12 08:42:50'),(101,15,'LOGIN','user',15,'User logged in','::1','2026-09-12 09:10:15'),(102,15,'CREATE_HOTEL_RESERVATION','reservation',8,'Created HTL-2026-000008','::1','2026-09-12 09:12:52'),(103,1,'LOGIN','user',1,'User logged in','::1','2026-09-12 09:13:25'),(104,1,'RESERVATION_CONFIRMED','reservation',8,'Updated status of HTL-2026-000008 to CONFIRMED','::1','2026-09-12 09:14:08'),(105,1,'RESERVATION_CHECKED_IN','reservation',8,'Updated status of HTL-2026-000008 to CHECKED_IN','::1','2026-09-12 09:14:10'),(106,1,'RESERVATION_CHECKED_OUT','reservation',8,'Updated status of HTL-2026-000008 to CHECKED_OUT','::1','2026-09-12 09:14:12'),(107,1,'RESERVATION_REJECTED','reservation',8,'Updated status of HTL-2026-000008 to REJECTED','::1','2026-09-12 09:14:15'),(108,1,'RESERVATION_CONFIRMED','reservation',8,'Updated status of HTL-2026-000008 to CONFIRMED','::1','2026-09-12 09:14:20'),(109,7,'LOGIN','user',7,'User logged in','::1','2026-09-12 09:14:26'),(110,15,'LOGIN','user',15,'User logged in','::1','2026-09-12 09:14:32'),(111,1,'LOGIN','user',1,'User logged in','::1','2026-09-12 09:14:37'),(112,1,'UPDATE_ROOM_STATUS','room',2,'Set room status to INACTIVE','::1','2026-09-12 09:15:24');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `add_ons`
--

DROP TABLE IF EXISTS `add_ons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `add_ons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `add_ons`
--

LOCK TABLES `add_ons` WRITE;
/*!40000 ALTER TABLE `add_ons` DISABLE KEYS */;
INSERT INTO `add_ons` VALUES (1,'Airport Pickup','One-way private airport transfer to the hotel.',1200.00,1,'2026-09-03 22:06:17'),(2,'Daily Breakfast Buffet','Access to the morning continental and local breakfast spread per guest/day.',450.00,1,'2026-09-03 22:06:17'),(3,'Extra Bed / Rollaway','Additional single rollaway bed setup for the room, per night.',800.00,1,'2026-09-03 22:06:17'),(4,'Spa Access Package','Day pass for sauna, steam room, and a 60-minute massage.',1500.00,1,'2026-09-03 22:06:17');
/*!40000 ALTER TABLE `add_ons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `amenities`
--

DROP TABLE IF EXISTS `amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amenities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(80) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `amenities`
--

LOCK TABLES `amenities` WRITE;
/*!40000 ALTER TABLE `amenities` DISABLE KEYS */;
INSERT INTO `amenities` VALUES (1,'Free Wi-Fi','High-speed wireless internet','wifi','2026-08-18 10:07:48'),(2,'Swimming Pool','Outdoor pool access','pool','2026-08-18 10:07:48'),(3,'Air Conditioning','Individually controlled climate','snowflake','2026-08-18 10:07:48'),(4,'Parking','Secure on-site parking','car','2026-08-18 10:07:48'),(5,'Breakfast','Daily breakfast included','coffee','2026-08-18 10:07:48'),(6,'Gym','24-hour fitness centre','dumbbell','2026-08-18 10:07:48'),(7,'Restaurant','All-day dining','utensils','2026-08-18 10:07:48'),(8,'Room Service','In-room dining','bell','2026-08-18 10:07:48'),(9,'Smart TV','Streaming-ready television','tv','2026-08-18 10:07:48'),(10,'Balcony','Private outdoor space','sun','2026-08-18 10:07:48');
/*!40000 ALTER TABLE `amenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currency_rates`
--

DROP TABLE IF EXISTS `currency_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency_rates` (
  `currency` varchar(10) NOT NULL,
  `rate` decimal(18,6) NOT NULL,
  `fetched_at` datetime NOT NULL,
  PRIMARY KEY (`currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currency_rates`
--

LOCK TABLES `currency_rates` WRITE;
/*!40000 ALTER TABLE `currency_rates` DISABLE KEYS */;
INSERT INTO `currency_rates` VALUES ('AUD',0.022243,'2026-09-12 15:19:25'),('EUR',0.013771,'2026-09-12 15:19:25'),('GBP',0.011816,'2026-09-12 15:19:25'),('JPY',2.456652,'2026-09-12 15:19:25'),('PHP',1.000000,'2026-09-12 15:19:25'),('SGD',0.020235,'2026-09-12 15:19:25'),('USD',0.015950,'2026-09-12 15:19:25');
/*!40000 ALTER TABLE `currency_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_verifications`
--

DROP TABLE IF EXISTS `email_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_verifications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `verification_code_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `email_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_verifications`
--

LOCK TABLES `email_verifications` WRITE;
/*!40000 ALTER TABLE `email_verifications` DISABLE KEYS */;
INSERT INTO `email_verifications` VALUES (1,6,'$2y$10$P7ej0GQbzJ1etn4heMR/peakUf2RyaXcrZwCHEbIvHRr3eavE8Hv.','2026-08-18 19:10:30','2026-08-18 18:55:36',0,'2026-08-18 10:55:30'),(2,7,'$2y$10$UzLuU17VtdrqQnOc49bCGOAQakuiZaNCRlHyc.5TY6v4ns3KlZCqW','2026-09-04 05:36:28','2026-09-04 05:21:38',0,'2026-09-03 21:21:28'),(3,8,'$2y$10$r1peQpSLZXU11EttgQQviOcN7sCjcqr3Z/jChzUKfcOBpg7v2KAWq','2026-09-04 10:34:16','2026-09-04 10:19:22',0,'2026-09-04 02:19:16'),(4,9,'$2y$10$8tXYEBv60jXdaMAvitlWw.11hxc6wG6HgNGEgaBMctbw4euCQxNxa','2026-09-06 01:54:58','2026-09-06 01:40:07',0,'2026-09-05 17:39:58'),(5,10,'$2y$10$LAF86sRCXC9baGYkIrXHceXlxFiMD/mjkUFjbWHIHkX3JA971PBCy','2026-09-06 01:55:40','2026-09-06 01:41:28',0,'2026-09-05 17:40:40'),(6,11,'$2y$10$TGUC66Sbv7sS0IYQt7eoUu.NL9nHi7lFIYgqv.ajcZypTgBqptLgy','2026-09-06 02:03:34','2026-09-06 01:48:47',0,'2026-09-05 17:48:34'),(7,12,'$2y$10$lPHG/363dPR.iuUctcTIcOexKtdd0vH3b5WhsHuK6PjV9T2piuel2','2026-09-06 09:00:04','2026-09-06 08:45:22',0,'2026-09-06 00:45:04'),(8,14,'$2y$10$/aKM5bE2DFnZnBqHD8NbnOLrt4q2j4YqerVbHN1xKYspRQWiUluRe','2026-09-12 15:40:00','2026-09-12 15:25:11',0,'2026-09-12 07:25:00'),(9,15,'$2y$10$87skCncjtSN9QWia/KIuzOGmnpGth2CiLWsFKWutoKCLG1NLczopO','2026-09-12 17:24:36','2026-09-12 17:10:13',0,'2026-09-12 09:09:36');
/*!40000 ALTER TABLE `email_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotel_settings`
--

DROP TABLE IF EXISTS `hotel_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotel_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(80) DEFAULT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotel_settings`
--

LOCK TABLES `hotel_settings` WRITE;
/*!40000 ALTER TABLE `hotel_settings` DISABLE KEYS */;
INSERT INTO `hotel_settings` VALUES (1,'hotel_name','HOTELRESERVE'),(2,'tax_rate','0.12'),(3,'check_in_time','15:00'),(4,'check_out_time','12:00'),(5,'cancellation_policy','Free cancellation up to 24 hours before check-in.');
/*!40000 ALTER TABLE `hotel_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(160) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`,`attempted_at`),
  KEY `email` (`email`,`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(30) DEFAULT 'SYSTEM',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`is_read`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,6,'Reservation received','Your stay reservation HTL-2026-000001 is pending confirmation.','RESERVATION',0,'2026-08-18 11:04:27'),(2,6,'Reservation update','Your reservation HTL-2026-000001 is now CONFIRMED.','RESERVATION',0,'2026-08-18 11:05:24'),(3,6,'Reservation update','Your reservation HTL-2026-000001 is now CONFIRMED.','RESERVATION',0,'2026-08-18 11:05:26'),(4,6,'Reservation received','Your stay reservation HTL-2026-000002 is pending confirmation.','RESERVATION',0,'2026-08-21 15:35:53'),(5,6,'Reservation update','Your reservation HTL-2026-000002 is now CONFIRMED.','RESERVATION',0,'2026-09-03 16:34:46'),(6,6,'Reservation update','Your reservation HTL-2026-000002 is now CONFIRMED.','RESERVATION',0,'2026-09-03 16:34:47'),(7,6,'Reservation update','Your reservation HTL-2026-000002 is now CONFIRMED.','RESERVATION',0,'2026-09-03 16:34:48'),(8,6,'Reservation update','Your reservation HTL-2026-000002 is now CONFIRMED.','RESERVATION',0,'2026-09-03 16:34:49'),(9,6,'Reservation update','Your reservation HTL-2026-000002 is now CONFIRMED.','RESERVATION',0,'2026-09-03 16:34:50'),(10,6,'Reservation update','Your reservation HTL-2026-000002 is now CHECKED_IN.','RESERVATION',0,'2026-09-03 16:34:53'),(11,6,'Reservation update','Your reservation HTL-2026-000002 is now CONFIRMED.','RESERVATION',0,'2026-09-03 18:27:45'),(12,7,'Reservation received','Your stay reservation HTL-2026-000003 is pending confirmation.','RESERVATION',0,'2026-09-03 21:23:26'),(13,7,'Reservation Update','Your reservation HTL-2026-000003 is now CONFIRMED.','RESERVATION',0,'2026-09-03 21:24:41'),(14,7,'Reservation cancelled','Your reservation HTL-2026-000003 has been cancelled.','RESERVATION',0,'2026-09-03 21:36:19'),(15,7,'Reservation Update','Your reservation HTL-2026-000003 is now REJECTED.','RESERVATION',0,'2026-09-03 22:10:32'),(16,7,'Reservation Update','Your reservation HTL-2026-000003 is now CANCELLED.','RESERVATION',0,'2026-09-03 22:10:35'),(17,7,'Reservation received','Your stay reservation HTL-2026-000004 is pending confirmation.','RESERVATION',0,'2026-09-03 22:34:11'),(18,7,'Reservation received','Your stay reservation HTL-2026-000005 is pending confirmation.','RESERVATION',0,'2026-09-04 02:15:07'),(19,7,'Reservation Update','Your reservation HTL-2026-000005 is now CONFIRMED.','RESERVATION',0,'2026-09-04 02:15:32'),(20,7,'Reservation Update','Your reservation HTL-2026-000005 is now CHECKED_IN.','RESERVATION',0,'2026-09-05 07:20:25'),(21,7,'Reservation Update','Your reservation HTL-2026-000005 is now CONFIRMED.','RESERVATION',0,'2026-09-05 07:20:40'),(22,7,'Reservation Update','Your reservation HTL-2026-000005 is now CANCELLED.','RESERVATION',0,'2026-09-05 08:46:10'),(23,7,'Reservation Update','Your reservation HTL-2026-000005 is now CHECKED_OUT.','RESERVATION',0,'2026-09-05 08:46:20'),(24,7,'Reservation Update','Your reservation HTL-2026-000005 is now REJECTED.','RESERVATION',0,'2026-09-05 08:46:25'),(25,7,'Reservation Update','Your reservation HTL-2026-000003 is now CANCELLED.','RESERVATION',0,'2026-09-05 08:46:29'),(26,7,'Reservation Update','Your reservation HTL-2026-000003 is now CHECKED_IN.','RESERVATION',0,'2026-09-05 08:46:33'),(27,7,'Reservation Update','Your reservation HTL-2026-000003 is now CHECKED_OUT.','RESERVATION',0,'2026-09-05 08:46:38'),(28,6,'Reservation Update','Your reservation HTL-2026-000001 is now CONFIRMED.','RESERVATION',0,'2026-09-05 08:46:42'),(29,12,'Reservation received','Your stay reservation HTL-2026-000006 is pending confirmation.','RESERVATION',0,'2026-09-06 00:47:27'),(30,14,'Reservation received','Your stay reservation HTL-2026-000007 is pending confirmation.','RESERVATION',0,'2026-09-12 07:27:53'),(31,14,'Reservation Update','Your reservation HTL-2026-000007 is now CONFIRMED.','RESERVATION',0,'2026-09-12 07:31:36'),(32,15,'Reservation received','Your stay reservation HTL-2026-000008 is pending confirmation.','RESERVATION',0,'2026-09-12 09:12:52'),(33,15,'Reservation Update','Your reservation HTL-2026-000008 is now CONFIRMED.','RESERVATION',0,'2026-09-12 09:14:08'),(34,15,'Reservation Update','Your reservation HTL-2026-000008 is now CHECKED_IN.','RESERVATION',0,'2026-09-12 09:14:10'),(35,15,'Reservation Update','Your reservation HTL-2026-000008 is now CHECKED_OUT.','RESERVATION',0,'2026-09-12 09:14:12'),(36,15,'Reservation Update','Your reservation HTL-2026-000008 is now REJECTED.','RESERVATION',0,'2026-09-12 09:14:15'),(37,15,'Reservation Update','Your reservation HTL-2026-000008 is now CONFIRMED.','RESERVATION',0,'2026-09-12 09:14:20');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `payment_reference` varchar(60) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('CASH','GCASH','CREDIT_CARD','DEBIT_CARD','BANK_TRANSFER') NOT NULL DEFAULT 'CASH',
  `payment_status` enum('PENDING','PAID','FAILED','REFUNDED') NOT NULL DEFAULT 'PENDING',
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_reference` (`payment_reference`),
  KEY `reservation_id` (`reservation_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'PAY-HTL-2026-000001',5600.00,'CASH','PENDING',NULL,'2026-08-18 11:04:27'),(2,2,'PAY-HTL-2026-000002',2800.00,'CASH','PENDING',NULL,'2026-08-21 15:35:53'),(3,3,'PAY-HTL-2026-000003',5600.00,'CASH','PENDING',NULL,'2026-09-03 21:23:26'),(4,4,'PAY-HTL-2026-000004',33600.00,'CASH','PENDING',NULL,'2026-09-03 22:34:11'),(5,5,'PAY-HTL-2026-000005',43624.00,'CASH','PENDING',NULL,'2026-09-04 02:15:07'),(6,6,'PAY-HTL-2026-000006',94584.00,'CASH','PENDING',NULL,'2026-09-06 00:47:27'),(7,7,'PAY-HTL-2026-000007',37856.00,'CASH','PENDING',NULL,'2026-09-12 07:27:53'),(8,8,'PAY-HTL-2026-000008',13944.00,'CASH','PENDING',NULL,'2026-09-12 09:12:52');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_add_ons`
--

DROP TABLE IF EXISTS `reservation_add_ons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation_add_ons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `add_on_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_id` (`reservation_id`),
  KEY `add_on_id` (`add_on_id`),
  CONSTRAINT `reservation_add_ons_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservation_add_ons_ibfk_2` FOREIGN KEY (`add_on_id`) REFERENCES `add_ons` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_add_ons`
--

LOCK TABLES `reservation_add_ons` WRITE;
/*!40000 ALTER TABLE `reservation_add_ons` DISABLE KEYS */;
INSERT INTO `reservation_add_ons` VALUES (1,5,1,1,1200.00),(2,5,2,1,450.00),(3,5,3,1,800.00),(4,5,4,1,1500.00),(5,6,1,1,1200.00),(6,6,2,1,450.00),(7,6,3,1,800.00),(8,6,4,1,1500.00),(9,7,3,1,800.00),(10,7,4,1,1500.00),(11,8,1,1,1200.00),(12,8,2,1,450.00),(13,8,3,1,800.00),(14,8,4,1,1500.00);
/*!40000 ALTER TABLE `reservation_add_ons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_number` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `guests` int(11) NOT NULL,
  `adults` int(11) NOT NULL,
  `children` int(11) DEFAULT 0,
  `nights` int(11) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('PENDING','CONFIRMED','CHECKED_IN','CHECKED_OUT','CANCELLED','REJECTED','EXPIRED') DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_number` (`reservation_number`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`),
  KEY `status` (`status`),
  KEY `check_in` (`check_in`,`check_out`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,'HTL-2026-000001',6,2,'2026-08-18','2026-08-20',1,1,0,2,2500.00,5000.00,600.00,0.00,5600.00,'','CONFIRMED','2026-08-18 11:04:27','2026-08-18 11:05:24'),(2,'HTL-2026-000002',6,1,'2026-08-21','2026-08-22',2,2,0,1,2500.00,2500.00,300.00,0.00,2800.00,'','CONFIRMED','2026-08-21 15:35:53','2026-09-03 18:27:45'),(3,'HTL-2026-000003',7,1,'2026-09-20','2026-09-22',1,1,0,2,2500.00,5000.00,600.00,0.00,5600.00,'','CHECKED_OUT','2026-09-03 21:23:26','2026-09-05 08:46:38'),(4,'HTL-2026-000004',7,1,'2026-09-11','2026-09-23',2,2,0,12,2500.00,30000.00,3600.00,0.00,33600.00,'','PENDING','2026-09-03 22:34:11','2026-09-03 22:34:11'),(5,'HTL-2026-000005',7,3,'2026-09-14','2026-09-24',1,1,0,10,3500.00,38950.00,4674.00,0.00,43624.00,'','REJECTED','2026-09-04 02:15:06','2026-09-05 08:46:25'),(6,'HTL-2026-000006',12,4,'2026-09-06','2026-09-29',2,2,0,23,3500.00,84450.00,10134.00,0.00,94584.00,'','PENDING','2026-09-06 00:47:27','2026-09-06 00:47:27'),(7,'HTL-2026-000007',14,3,'2026-09-13','2026-09-22',2,1,1,9,3500.00,33800.00,4056.00,0.00,37856.00,'Food request chicken breast','CONFIRMED','2026-09-12 07:27:53','2026-09-12 07:31:36'),(8,'HTL-2026-000008',15,7,'2026-09-12','2026-09-13',2,2,0,1,8500.00,12450.00,1494.00,0.00,13944.00,'','CONFIRMED','2026-09-12 09:12:52','2026-09-12 09:14:20');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL DEFAULT 5,
  `title` varchar(160) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_room_res` (`user_id`,`reservation_id`),
  KEY `room_id` (`room_id`),
  KEY `reservation_id` (`reservation_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_amenities`
--

DROP TABLE IF EXISTS `room_amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_amenities` (
  `room_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL,
  PRIMARY KEY (`room_id`,`amenity_id`),
  KEY `amenity_id` (`amenity_id`),
  CONSTRAINT `room_amenities_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_amenities`
--

LOCK TABLES `room_amenities` WRITE;
/*!40000 ALTER TABLE `room_amenities` DISABLE KEYS */;
INSERT INTO `room_amenities` VALUES (1,1),(1,3),(1,5),(1,9),(2,1),(2,3),(2,5),(2,9),(3,1),(3,3),(3,5),(3,9),(4,1),(4,3),(4,5),(4,9),(5,1),(5,3),(5,5),(5,9),(6,1),(6,3),(6,5),(6,9),(7,1),(7,3),(7,5),(7,9);
/*!40000 ALTER TABLE `room_amenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_images`
--

DROP TABLE IF EXISTS `room_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_images`
--

LOCK TABLES `room_images` WRITE;
/*!40000 ALTER TABLE `room_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `room_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_types`
--

DROP TABLE IF EXISTS `room_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `max_guests` int(11) NOT NULL,
  `bed_type` varchar(80) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_types`
--

LOCK TABLES `room_types` WRITE;
/*!40000 ALTER TABLE `room_types` DISABLE KEYS */;
INSERT INTO `room_types` VALUES (1,'Standard Room','Comfortable essentials for a restful stay',2500.00,2,'Queen Bed','28 sqm','2026-08-18 10:07:48','2026-08-18 10:07:48'),(2,'Deluxe Room','An elevated stay with refined comfort',3500.00,2,'King Bed','35 sqm','2026-08-18 10:07:48','2026-08-18 10:07:48'),(3,'Superior Room','Spacious room with city views',4200.00,3,'King Bed','42 sqm','2026-08-18 10:07:48','2026-08-18 10:07:48'),(4,'Family Room','Designed for memorable family stays',5200.00,4,'Two Queen Beds','48 sqm','2026-08-18 10:07:48','2026-08-18 10:07:48'),(5,'Executive Suite','A private, premium suite',8500.00,2,'King Bed','65 sqm','2026-08-18 10:07:48','2026-08-18 10:07:48');
/*!40000 ALTER TABLE `room_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_number` varchar(20) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `title` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `max_guests` int(11) NOT NULL,
  `bed_type` varchar(80) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `floor` varchar(20) DEFAULT NULL,
  `status` enum('AVAILABLE','OCCUPIED','BOOKED','CHECKED_IN','CHECKED_OUT','MAINTENANCE','NEEDS_CLEANING','INSPECTED','INACTIVE') NOT NULL DEFAULT 'AVAILABLE',
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_number` (`room_number`),
  KEY `room_type_id` (`room_type_id`),
  KEY `status` (`status`),
  CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'101',1,'Standard Queen','A quiet, welcoming room with essential comforts.',2500.00,2,'Queen Bed','28 sqm','1','AVAILABLE',1,'2026-08-18 10:07:48','2026-09-12 07:33:13'),(2,'102',1,'Standard Queen','A peaceful base for business or leisure.',2500.00,2,'Queen Bed','28 sqm','1','INACTIVE',0,'2026-08-18 10:07:48','2026-09-12 09:15:24'),(3,'201',2,'Deluxe King','A refined king room with relaxing city views.',3500.00,2,'King Bed','35 sqm','2','AVAILABLE',1,'2026-08-18 10:07:48','2026-08-18 10:07:48'),(4,'202',2,'Deluxe King','Premium comfort for two guests.',3500.00,2,'King Bed','35 sqm','2','AVAILABLE',1,'2026-08-18 10:07:48','2026-08-18 10:07:48'),(5,'301',3,'Superior King','Generous space with an elegant lounge corner.',4200.00,3,'King Bed','42 sqm','3','AVAILABLE',1,'2026-08-18 10:07:48','2026-08-18 10:07:48'),(6,'401',4,'Family Escape','Space and comfort for the entire family.',5200.00,4,'Two Queen Beds','48 sqm','4','AVAILABLE',1,'2026-08-18 10:07:48','2026-08-18 10:07:48'),(7,'501',5,'Executive Suite','A luxurious private suite for special stays.',8500.00,2,'King Bed','65 sqm','5','AVAILABLE',1,'2026-08-18 10:07:48','2026-08-18 10:07:48');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_favorites`
--

DROP TABLE IF EXISTS `user_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_favorites` (
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`room_id`),
  KEY `fk_user_favorites_room` (`room_id`),
  CONSTRAINT `fk_user_favorites_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_favorites`
--

LOCK TABLES `user_favorites` WRITE;
/*!40000 ALTER TABLE `user_favorites` DISABLE KEYS */;
INSERT INTO `user_favorites` VALUES (1,1,'2026-08-18 10:57:24'),(1,3,'2026-09-05 07:21:19'),(14,3,'2026-09-12 07:26:11'),(15,1,'2026-09-12 09:11:41');
/*!40000 ALTER TABLE `user_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_preferences`
--

DROP TABLE IF EXISTS `user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_preferences` (
  `user_id` int(11) NOT NULL,
  `theme` enum('light','dark') NOT NULL DEFAULT 'light',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_user_preferences_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_preferences`
--

LOCK TABLES `user_preferences` WRITE;
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
INSERT INTO `user_preferences` VALUES (1,'light','2026-09-12 08:43:10'),(6,'light','2026-09-03 21:18:08'),(7,'dark','2026-09-12 08:42:28'),(8,'light','2026-09-04 02:19:33'),(14,'dark','2026-09-12 07:25:34'),(15,'dark','2026-09-12 09:11:58');
/*!40000 ALTER TABLE `user_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_search_history`
--

DROP TABLE IF EXISTS `user_search_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_search_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `destination` varchar(160) NOT NULL DEFAULT '',
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `guests` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `room_type_id` int(11) DEFAULT NULL,
  `max_price` decimal(10,2) DEFAULT NULL,
  `amenities_json` text DEFAULT NULL,
  `sort_by` varchar(30) NOT NULL DEFAULT 'recommended',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_search_history_user_created` (`user_id`,`created_at`),
  KEY `fk_user_search_history_room_type` (`room_type_id`),
  CONSTRAINT `fk_user_search_history_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_user_search_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_search_history`
--

LOCK TABLES `user_search_history` WRITE;
/*!40000 ALTER TABLE `user_search_history` DISABLE KEYS */;
INSERT INTO `user_search_history` VALUES (1,1,'','2026-09-01','2026-09-03',2,1,NULL,NULL,'recommended','2026-08-18 10:57:24'),(3,7,'','2026-09-06','2026-09-23',6,NULL,NULL,NULL,'recommended','2026-09-03 21:31:53'),(4,7,'','2026-09-13','2026-09-23',3,NULL,NULL,NULL,'recommended','2026-09-03 21:32:26'),(5,7,'','2026-09-13','2026-09-23',3,NULL,NULL,NULL,'recommended','2026-09-03 21:32:40'),(6,7,'','2026-09-13','2026-09-23',3,NULL,NULL,NULL,'recommended','2026-09-03 21:32:47'),(7,12,'','2026-09-06','2026-09-07',2,NULL,NULL,NULL,'recommended','2026-09-06 00:46:35'),(8,14,'','2026-09-13','2026-09-23',8,1,NULL,NULL,'recommended','2026-09-12 07:50:14'),(9,15,'','2026-09-13','2026-09-18',2,NULL,NULL,NULL,'recommended','2026-09-12 09:10:55');
/*!40000 ALTER TABLE `user_search_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('CUSTOMER','ADMIN') NOT NULL DEFAULT 'CUSTOMER',
  `status` enum('PENDING_VERIFICATION','ACTIVE','SUSPENDED','DEACTIVATED') NOT NULL DEFAULT 'PENDING_VERIFICATION',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `housekeeping` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `email_2` (`email`),
  KEY `role` (`role`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Hotel','Administrator','admin@hotelreserve.local','$2y$10$Hte8bM7krMGe73ASGr/uKeTmZ1Wlruode5ExlRvHBpsephxv2oTnO','+63 900 000 0000',NULL,'ADMIN','ACTIVE','2026-08-18 10:07:48','2026-09-12 09:14:37',1,'2026-08-18 18:07:48','2026-09-12 17:14:37',0),(2,'Maria','Manager','manager@hotelreserve.local','$2y$10$Hte8bM7krMGe73ASGr/uKeTmZ1Wlruode5ExlRvHBpsephxv2oTnO','+63 900 000 0001',NULL,'ADMIN','ACTIVE','2026-08-18 10:07:48','2026-08-18 10:07:48',1,'2026-08-18 18:07:48',NULL,0),(3,'Ava','Reyes','ava@guest.test','$2y$10$UoHyfw/uUilYooIJpE4NK..weYiUd3BGiRJhHGnopdHQ5mC8vj/bW','09170000001',NULL,'CUSTOMER','ACTIVE','2026-08-18 10:07:48','2026-08-18 10:07:48',0,NULL,NULL,0),(4,'Ben','Cruz','ben@guest.test','$2y$10$UoHyfw/uUilYooIJpE4NK..weYiUd3BGiRJhHGnopdHQ5mC8vj/bW','09170000002',NULL,'CUSTOMER','ACTIVE','2026-08-18 10:07:48','2026-08-18 10:07:48',0,NULL,NULL,0),(5,'Cara','Lim','cara@guest.test','$2y$10$UoHyfw/uUilYooIJpE4NK..weYiUd3BGiRJhHGnopdHQ5mC8vj/bW','09170000003',NULL,'CUSTOMER','ACTIVE','2026-08-18 10:07:48','2026-08-18 10:07:48',0,NULL,NULL,0),(6,'Yani','Xian','ghostfaceghost62@gmail.com','$2y$10$HcVfIo1gpxsE7bTlhdZV0OTcl7CwWhhJTBudVHA.jteKEg05aZG42','ianpogi@gmail.com','1158 Camachilest Pinagbuhatan','CUSTOMER','ACTIVE','2026-08-18 10:55:30','2026-09-03 21:04:56',1,'2026-08-18 18:55:36','2026-09-04 05:04:56',0),(7,'IAN LAURENZ EPE','Xian','arope@gmail.com','$2y$10$a47OiLKpDyaWpqT2zArk1eByQflHCSk5xXWfih2Gas68fshAtGGJm','+639198629364','1158 Camachilest Pinagbuhatan','CUSTOMER','ACTIVE','2026-09-03 21:21:28','2026-09-12 09:14:26',1,'2026-09-04 05:21:38','2026-09-12 17:14:26',0),(8,'IAN LAURENZ EPE','Xian','fuckmyass@gmail.com','$2y$10$giYbf1m.pZjcGTXY2eh0qOwREJ2Y5KUv1KqNnc6w2OS3YY3osSprS','+639198629364','1158 Camachilest Pinagbuhatan','CUSTOMER','ACTIVE','2026-09-04 02:19:16','2026-09-04 02:19:24',1,'2026-09-04 10:19:22','2026-09-04 10:19:24',0),(9,'IAN LAURENZ EPE','Xian','testingngrok@gmail.com','$2y$10$KQ1R6yFbPu8k/su9V5vqe.zA7uxW5vyeqdCy2YoOzKWO8Pn5Sc9nO','+639198629364','1158 Camachilest Pinagbuhatan','CUSTOMER','ACTIVE','2026-09-05 17:39:58','2026-09-05 17:40:10',1,'2026-09-06 01:40:07','2026-09-06 01:40:10',0),(10,'Ardus','Oxford','phunsawatleo@gmail.com','$2y$10$icVVXxjWI9KqhOUUSOpBS.PFkkDQGeqHLAcUXsCpcbWI6iUQ7luMq','09760774597','','CUSTOMER','ACTIVE','2026-09-05 17:40:40','2026-09-05 17:41:50',1,'2026-09-06 01:41:28','2026-09-06 01:41:50',0),(11,'Hatdog','Hshsgs','jahsjshsu@gmail.com','$2y$10$N6.1KQAHVHWuVuXLgJ26EeiSLeFw49b0XbCO.wINcy6hoQpCCf77G','0929273736','','CUSTOMER','ACTIVE','2026-09-05 17:48:34','2026-09-05 17:48:47',1,'2026-09-06 01:48:47',NULL,0),(12,'Rogiel Mae','Lantaca','rogielmaeclantaca@gmail.com','$2y$10$0BQGq0AqnBrF24LVw39L.OEohaZgVk/A5azDvpGF50R8ZLP/WdocG','+639627439428','Unicenthoa Infante St. Centennial 1A','CUSTOMER','ACTIVE','2026-09-06 00:45:04','2026-09-06 00:45:27',1,'2026-09-06 08:45:22','2026-09-06 08:45:27',0),(14,'JIll','Lantaca','jill@gmail.com','$2y$10$nZzNySlpeZoi8mn2yS6zXu6Rb52O9629wGkltwXvH8Kj3j4qNDYlC','09627439428','1158 Camachilest Pinagbuhatan','CUSTOMER','ACTIVE','2026-09-12 07:24:59','2026-09-12 07:49:06',1,'2026-09-12 15:25:11','2026-09-12 15:49:06',0),(15,'IAN LAURENZ EPE','Xian','ian62@gmail.com','$2y$10$vQdzQc0kaYONwakqiVYjLOQ9ZnQS/eit65sgxlJMqYhEm1Zwl9wDu','+639198629364','1158 Camachilest Pinagbuhatan','CUSTOMER','ACTIVE','2026-09-12 09:09:36','2026-09-12 09:14:32',1,'2026-09-12 17:10:13','2026-09-12 17:14:32',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-13  4:32:59

