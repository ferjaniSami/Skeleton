-- MySQL dump 10.13  Distrib 5.5.33, for osx10.6 (i386)
--
-- Host: localhost    Database: fu2
-- ------------------------------------------------------
-- Server version	5.5.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `fu2`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `fu2` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `fu2`;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `locked` tinyint(1) DEFAULT '0',
  `author` bigint(20) DEFAULT NULL,
  `author_translation` bigint(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `status_translation` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL,
  `title` tinytext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,NULL,NULL,NULL,1,1,'2014-09-24 18:27:41','2014-09-24 23:18:33','Title EN'),(2,NULL,NULL,NULL,1,1,'2014-09-24 23:19:49','2014-09-24 23:19:49','Title 2 FR'),(3,NULL,NULL,NULL,1,1,'2014-09-24 23:20:17','2014-09-24 23:20:17','Title 3 ES'),(5,NULL,1,1,1,1,'2014-09-25 12:59:17','2014-09-25 13:14:53','Title IT');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_translations`
--

DROP TABLE IF EXISTS `news_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `news_translation_idx` (`locale`,`object_class`,`field`,`foreign_key`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_translations`
--

LOCK TABLES `news_translations` WRITE;
/*!40000 ALTER TABLE `news_translations` DISABLE KEYS */;
INSERT INTO `news_translations` VALUES (1,'en','Admin\\Entity\\News','status_translation','1','1'),(2,'en','Admin\\Entity\\News','title','1','Title EN'),(3,'fr','Admin\\Entity\\News','author_translation','1',NULL),(4,'fr','Admin\\Entity\\News','status_translation','1','1'),(5,'fr','Admin\\Entity\\News','title','1','Title FR'),(6,'es','Admin\\Entity\\News','author_translation','1',NULL),(7,'es','Admin\\Entity\\News','status_translation','1','1'),(8,'es','Admin\\Entity\\News','title','1','Title ES'),(9,'intl-fr','Admin\\Entity\\News','author_translation','1',NULL),(10,'intl-fr','Admin\\Entity\\News','status_translation','1','1'),(11,'intl-fr','Admin\\Entity\\News','title','1','Title INT FR'),(12,'fr','Admin\\Entity\\News','status_translation','2','1'),(13,'fr','Admin\\Entity\\News','title','2','Title 2 FR'),(14,'es','Admin\\Entity\\News','status_translation','3','1'),(15,'es','Admin\\Entity\\News','title','3','Title 3 ES'),(16,'it','Admin\\Entity\\News','author_translation','5','1'),(17,'it','Admin\\Entity\\News','status_translation','5','1'),(18,'it','Admin\\Entity\\News','title','5','Title IT edit'),(19,'intl-it','Admin\\Entity\\News','status_translation','5','1'),(20,'intl-it','Admin\\Entity\\News','title','5','Title IT intl');
/*!40000 ALTER TABLE `news_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `label` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `acl` longtext COLLATE utf8_unicode_ci NOT NULL,
  `locked` tinyint(1) DEFAULT '0',
  `author` bigint(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL,
  `territories` longtext COLLATE utf8_unicode_ci NOT NULL,
  `langs` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Webmaster','','webmaster','[\"Admin-Controller-Index_index\",\"Admin-Controller-User_index\",\"Admin-Controller-User_edit\",\"Admin-Controller-News_index\",\"Admin-Controller-News_translations\",\"Admin-Controller-News_add\",\"Admin-Controller-News_add-translation\",\"Admin-Controller-News_edit\",\"Admin-Controller-News_edit-translation\"]',NULL,1,1,'2014-09-22 13:23:37','2014-09-25 18:16:19','[\"intl-en\",\"intl-es\",\"fr-fr\"]','[\"it\"]');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `territories`
--

DROP TABLE IF EXISTS `territories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `territories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `url_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `langs` longtext COLLATE utf8_unicode_ci NOT NULL,
  `default_lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `locked` tinyint(1) DEFAULT '0',
  `author` bigint(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `territories`
--

LOCK TABLES `territories` WRITE;
/*!40000 ALTER TABLE `territories` DISABLE KEYS */;
INSERT INTO `territories` VALUES (1,'International','intl','[\"en\",\"es\",\"fr\",\"it\"]','en',1,1,1,'2014-09-22 23:06:34','2014-09-24 12:34:26'),(2,'France','fr','[\"fr\"]','fr',NULL,1,1,'2014-09-24 19:51:16','2014-09-24 19:51:16');
/*!40000 ALTER TABLE `territories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mail` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `login` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `super_admin` tinyint(1) DEFAULT '0',
  `roles` longtext COLLATE utf8_unicode_ci,
  `locked` tinyint(1) DEFAULT '0',
  `author` bigint(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'dev@uzik.com','admin','9eac77d5f83da9f0ab7f328b235f172d57f87a0d','Uzik',1,NULL,1,1,1,'2014-09-19 18:25:00','2014-09-25 16:45:17'),(2,'dev@uzik.com','webmaster','9eac77d5f83da9f0ab7f328b235f172d57f87a0d','Webmaster',0,'[\"1\"]',NULL,1,1,'2014-09-22 13:24:14','2014-09-25 18:06:02');
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

-- Dump completed on 2014-09-25 18:45:11
