-- MySQL dump 10.13  Distrib 5.5.23, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: v6
-- ------------------------------------------------------
-- Server version	5.5.23-2

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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nick` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `nick_jabber` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(96) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `salt` varchar(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `regtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastlogin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastaction` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastupdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_on_page` bigint(20) unsigned NOT NULL DEFAULT '0',
  `validated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `groups` varchar(256) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '6,196',
  `languages` varchar(32) COLLATE utf8_unicode_ci DEFAULT 'de',
  `avatar` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `avatar_img` varchar(8) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `signature` text COLLATE utf8_unicode_ci NOT NULL,
  `display_signatures` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `myspace_name` longtext COLLATE utf8_unicode_ci NOT NULL,
  `myspace_background` longtext COLLATE utf8_unicode_ci NOT NULL,
  `myspace` longtext COLLATE utf8_unicode_ci NOT NULL,
  `priv_bookmarks` varchar(12) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'private',
  `priv_ratings` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'private',
  `priv_friends` varchar(12) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'friends',
  `priv_guestbook` varchar(10) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'users',
  `profile_views` int(11) unsigned NOT NULL DEFAULT '0',
  `open_warnings` int(11) unsigned NOT NULL DEFAULT '0',
  `trusted_by` varchar(4096) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `forum_posts` int(11) unsigned NOT NULL DEFAULT '0',
  `points` float unsigned NOT NULL DEFAULT '0',
  `emails_allowed` tinyint(1) NOT NULL DEFAULT '1',
  `email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `email_login` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `nick` (`nick`),
  UNIQUE KEY `nick_jabber` (`nick_jabber`),
  KEY `salt` (`salt`)
) ENGINE=InnoDB AUTO_INCREMENT=72332 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--
-- WHERE:  user_id=1

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'wuff','wuff','fuck@you.com','8d556263ffdbe6db12692ac0d7a89c04','71a6e17170b2d214b7fb9b8a1582f0a2','2008-06-10 10:17:34','2012-05-11 04:50:14','2012-05-11 05:16:14','2012-05-11 05:16:14','0000-00-00 00:00:00',44434953,1,'196,182,208,208,0,0,0,0,0,0,0,0,0,0,197','de,en','','','',1,'','','','private','private','friends','users',12316,1,'4082,1924,2581,2,5451',579,632.325,1,1,0,0);
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

-- Dump completed on 2012-06-03  0:16:21
