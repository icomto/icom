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
-- Table structure for table `ajax_update`
--

DROP TABLE IF EXISTS `ajax_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ajax_update` (
  `id` int(10) unsigned NOT NULL,
  `Tc` bigint(20) unsigned NOT NULL DEFAULT '0',
  `N` bigint(20) unsigned NOT NULL DEFAULT '0',
  `i` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banned_ips`
--

DROP TABLE IF EXISTS `banned_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banned_ips` (
  `ip` binary(16) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_posts`
--

DROP TABLE IF EXISTS `forum_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_posts` (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastedit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lasteditor` int(11) unsigned NOT NULL,
  `thanks` text CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `name` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `thread` (`thread_id`),
  KEY `uid` (`user_id`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `content` (`content`),
  FULLTEXT KEY `name__content` (`name`,`content`)
) ENGINE=MyISAM AUTO_INCREMENT=916432 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
INSERT INTO `forum_posts` VALUES (916432,127072,1,'2012-06-04 22:10:53','0000-00-00 00:00:00',0,'','Test','123'),(916433,127073,1,'2012-06-04 22:28:25','0000-00-00 00:00:00',0,'','iCom ist Open Source','[news_introduce]196[/news_introduce]');
/*!40000 ALTER TABLE `forum_posts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `forum_reported_posts`
--

DROP TABLE IF EXISTS `forum_reported_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_reported_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timeclosed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `open` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `was_good_ticket` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL,
  `closer_uid` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `reason` text COLLATE utf8_unicode_ci,
  `namespace` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'def',
  PRIMARY KEY (`id`),
  KEY `open` (`open`),
  KEY `uid` (`user_id`),
  KEY `closer_uid` (`closer_uid`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12859 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_sections`
--

DROP TABLE IF EXISTS `forum_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_sections` (
  `section_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lft` int(11) unsigned NOT NULL,
  `rgt` int(11) unsigned NOT NULL,
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `name_de` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_en` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description_de` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description_en` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `allow_content` tinyint(1) NOT NULL DEFAULT '0',
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `allow_threads` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `mods` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `mod_groups` varchar(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `read_groups` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `write_groups` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) unsigned NOT NULL DEFAULT '0',
  `is_fsk18` tinyint(1) NOT NULL DEFAULT '0',
  `namespace` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'def',
  `has_childs` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `num_threads` int(11) unsigned NOT NULL DEFAULT '0',
  `num_threads_de` int(11) unsigned NOT NULL DEFAULT '0',
  `num_threads_en` int(11) unsigned NOT NULL DEFAULT '0',
  `num_posts` int(11) unsigned NOT NULL DEFAULT '0',
  `num_posts_de` int(11) unsigned NOT NULL DEFAULT '0',
  `num_posts_en` int(11) unsigned NOT NULL DEFAULT '0',
  `lastthread` int(11) unsigned NOT NULL,
  `lastthread_de` int(11) unsigned NOT NULL,
  `lastthread_en` int(11) unsigned NOT NULL,
  `points` float unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`section_id`),
  KEY `parent` (`parent`),
  KEY `lastthread` (`lastthread`),
  KEY `position` (`position`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `namespace` (`namespace`),
  FULLTEXT KEY `read_groups` (`read_groups`),
  FULLTEXT KEY `write_groups` (`write_groups`),
  FULLTEXT KEY `mods` (`mods`),
  FULLTEXT KEY `mod_groups` (`mod_groups`)
) ENGINE=MyISAM AUTO_INCREMENT=246 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_sections`
--

LOCK TABLES `forum_sections` WRITE;
/*!40000 ALTER TABLE `forum_sections` DISABLE KEYS */;
INSERT INTO `forum_sections` VALUES (1,2,29,0,'Teamintern','Teamboard','kkkkkk',NULL,1,'',1,'','221,3','1,2,3,175,221,202,194,154,187,212,215','1,2,3,175,221,202,194,154,187,212,215',1,0,'team',1,0,0,0,0,0,0,3,3,0,1),(2,3,4,1,'Admintalk',NULL,NULL,NULL,0,'',0,'','221','1,2','1,2',1,0,'team',0,7,7,1,30,30,1,108931,108931,72891,0),(3,5,6,1,'Moderatorentalk',NULL,'',NULL,0,'',1,'','221','1,3,2,175,221','1,2,3,175,221',2,0,'team',0,116,116,1,1764,1764,10,11789,126930,37552,1),(4,9,10,1,'[ALT] Uploadertalk',NULL,NULL,NULL,0,'',1,'','221','','',4,0,'team',0,10,10,0,35,35,0,7603,7603,0,1),(5,66,75,0,'Hilfe','Helpdesk',NULL,NULL,0,'',0,'','169,178,187,184,194','0','196',4,0,'def',1,0,0,0,0,0,0,7,7,0,1),(6,34,35,194,'Allgemeine Bekanntmachungen','General Announcements',NULL,NULL,0,'',1,'','175,3,221','0,221,199,215,208','1,2,3,175,221,187',1,0,'def',0,56,56,0,2111,2111,0,118580,118580,0,1),(7,51,52,48,'Verbesserungsvorschl�ge & Feedback','Feedback','Probleme, Anregungen, Lob, Tadel zur Struktur und den Funktionen der Seite','Tell us what you\'re thinking about the site',0,'',1,'','221','196,0,221,199,215,208','196,221,187',3,0,'def',0,1009,1006,8,16022,16013,242,127050,127050,119484,1),(8,189,190,39,'Gespr�chsecke','Miscellaneous','Vermischtes das nirgendwo anders passt','Miscellaneous stuff',0,'',1,'','','0','196',0,0,'def',0,1903,1901,15,64424,64399,480,100955,127058,124593,0.1),(9,71,72,5,'Tutorials','Tutorials','Anleitungen zu den verschiedensten Sachen','Several Tutorials, Tipps and Tricks',0,'',1,'','','0','196',3,0,'def',0,721,720,6,13816,13809,25,27601,27601,125331,1),(10,76,81,0,'Up- und Downloads',NULL,'',NULL,0,'',0,'','','0','196',5,0,'def',1,0,0,0,0,0,0,0,0,0,1),(11,86,87,197,'OneKlick- und Streamhoster','OneKlick- and Streamhoster','News und Infos �ber die Hoster','News, infos and discussions',0,'',1,'','','0','196',2,0,'def',0,702,702,3,12880,12880,16,127017,127017,118352,1),(12,88,89,197,'Alternative Downloadm�glichkeiten','Other ways to download Warez','Torrent, Usenet, SFT etc...','Torrent, Usenet, SFT etc...',0,'',1,'','','0','196',3,0,'def',0,210,210,0,2870,2870,0,127003,127003,0,1),(13,77,78,10,'Suche',NULL,'Suche nach allen m�glichen Downloads',NULL,0,'',1,'','','','',2,0,'def',0,0,0,0,0,0,0,9043,9043,0,1),(14,82,165,0,'Diskussionen','Discussions',NULL,NULL,0,'',0,'','','0,196','196',6,0,'def',1,0,0,0,0,0,0,8,8,0,1),(15,84,85,197,'Netzwelt','World Wide Web','Alles �bers Netz','All about the web',0,'',1,'','','0','196',1,0,'def',0,1520,1520,15,23675,23675,271,126985,127038,123326,1),(16,150,151,213,'Politik und Wirtschaft','Politics & Economy','Aktuelle politische Themen und Diskussionen',NULL,0,'',1,'','','0','196',1,0,'def',0,139,139,0,4226,4226,0,127008,127008,0,1),(17,152,153,213,'Gesellschaft & Kultur','Society & Culture','Die heutige Gesellschaft und deren Kultur',NULL,0,'',1,'','','0','196',3,0,'def',0,150,150,3,5121,5121,19,126898,126898,125914,1),(18,156,157,213,'Sport','Sports','Diskussionen, News',NULL,0,'',1,'','','0','196',6,0,'def',0,242,242,4,11283,11283,64,46744,126803,89120,1),(19,158,159,213,'Wissenschaft & �kologie','Science',NULL,NULL,0,'',1,'','','0','196',7,0,'def',0,55,55,0,1130,1130,0,127029,127029,0,1),(21,154,155,213,'Religion und Philosophie','Religion & Philosophy','Diskussionen �ber jegliche Religionen',NULL,0,'',1,'','','0','196',4,0,'def',0,41,41,0,2283,2283,0,124115,124115,0,1),(23,162,163,213,'Andere','Other Discussions','Kein passendes Forum gefunden!',NULL,0,'',1,'','','0','196',9,0,'def',0,1665,1665,7,22097,22097,121,46844,126993,123647,1),(215,169,178,185,'Handel','Trading',NULL,NULL,0,'',0,'','','208,196','208,196',2,0,'def',1,1,1,0,0,0,0,56681,56681,0,1),(25,94,95,199,'Hardware','Hardware',NULL,NULL,0,'',1,'','','0','196',1,0,'def',0,3038,3037,8,33430,33419,74,127020,127056,127054,1),(26,96,97,199,'Anwendungssoftware','Software',NULL,NULL,0,'',1,'','','0','196',2,0,'def',0,1981,1981,9,16183,16183,81,127060,127060,123573,1),(27,98,99,199,'Windows','Windows',NULL,NULL,0,'',1,'','','0','196',3,0,'def',0,1809,1809,10,19004,19004,67,102676,126976,126961,1),(28,120,121,201,'PC','PC Games','Diskussion, Probleme beim Installieren, Cracken',NULL,0,'',1,'','','0','196',2,0,'def',0,2241,2241,18,24130,24130,278,126947,127043,123956,1),(29,100,101,199,'Apple','Apple',NULL,NULL,0,'',1,'','','0','196',4,0,'def',0,398,398,2,3424,3424,4,127036,127036,93650,1),(30,102,103,199,'Linux','Linux',NULL,NULL,0,'',1,'','','0','196',5,0,'def',0,105,105,0,952,952,0,126185,126185,0,1),(31,130,131,201,'Konsolen (Altes Forum)',NULL,'ACHTUNG: Dieses Forum wird aufgel�st und gel�scht',NULL,0,'',0,'','','','',7,0,'def',0,0,0,0,0,0,0,2594,2594,0,1),(32,122,123,201,'PS2 und PS3','PS2 & PS3',NULL,NULL,0,'',1,'','','0','196',3,0,'def',0,649,649,2,7634,7634,14,127033,127033,114062,1),(35,124,125,201,'Wii','Wii',NULL,NULL,0,'',1,'','','0','196',4,0,'def',0,212,211,1,2146,2145,1,124242,125273,58834,1),(36,128,129,201,'Handhelds','Handhelds',NULL,NULL,0,'',1,'','','0','196',6,0,'def',0,272,272,10,2461,2461,48,126984,126984,107728,1),(38,126,127,201,'Xbox und Xbox360','Xbox & Xbox360',NULL,NULL,0,'',1,'','','0','196',5,0,'def',0,1013,1013,7,11793,11793,37,127055,127055,126957,1),(39,188,195,0,'Offtopic','Offtopic',NULL,NULL,0,'',0,'','175,3','0,196','196',9,0,'def',1,0,0,0,0,0,0,13,13,0,1),(40,191,192,39,'Fun','Fun','Witze, Bilder, Videos','Jokes, funny videos and pictures',0,'',1,'','','196','196',1,0,'def',0,559,558,13,38335,38329,682,113101,127044,123976,0.1),(41,46,47,194,'Gewinnspiele','Prize games',NULL,NULL,0,'',1,'','221','196,221,199,215,208','196,221,187',6,0,'def',0,84,84,0,8591,8591,0,127070,127070,0,0.5),(42,160,161,213,'Reallife','Reallife','Das echte Leben',NULL,0,'',1,'','','196','196',8,0,'def',0,1303,1302,8,27174,27168,179,127047,127047,124036,0.7),(45,193,194,39,'M�lleimer','Trash Bin','Sinnloses Zeug...','Spam and pointless threads',0,'',1,'','175,3','0','196',6,0,'tras',0,995,994,7,79072,79067,54,92199,127031,125371,0.1),(46,67,68,5,'Up- und Download Probleme','Up-and downloadproblems','Probleme beim Download, Tipps und Tricks','Problems with downloading, tips and tricks',0,'',1,'','','0','196',1,0,'def',0,2312,2309,7,19626,19609,75,96906,96906,126496,1),(47,79,80,10,'Gefunden',NULL,'Erledigte Threads aus dem Forum Suche',NULL,0,'',1,'','','','',3,0,'def',0,11,11,0,74,74,0,15725,15725,0,1),(48,30,55,0,'Regeln und Bekanntmachungen','Rules & announcements',NULL,NULL,1,'',0,'','175,3,221','196,0,221,180,175,3,1,2,199,215,208','196,221,1,2,175,3,180,187',2,0,'def',1,0,0,0,0,0,0,0,0,0,1),(49,31,32,48,'Regeln','Rules','Unsere Regeln - Bitte sorgf�ltig durchlesen!','Please read carefully',0,'',1,'','221','0,221,199,215,208','1,2,175,3,221,187',1,0,'def',0,2,2,0,2,2,0,2543,2543,0,1),(50,73,74,5,'W�nsch-dir-was Informationen',NULL,'Hier findet man den Status der Userw�nsche',NULL,0,'',1,'','','','',4,0,'def',0,32,32,0,241,241,0,18487,18487,0,1),(53,21,22,1,'Teamintern',NULL,NULL,NULL,0,'',1,'','3,221','1,202,2,3,194,221','1,202,2,3,194,221',10,0,'team',0,135,134,3,2058,2048,16,127064,127064,127064,1),(55,7,8,1,'Supermoderatorentalk',NULL,NULL,NULL,1,'foobar test 12\r\njalla jalla',1,'','221','1,2,175,221','1,2,175,221',3,0,'team',0,12,12,0,169,169,0,92447,92447,0,1),(56,11,12,1,'Invite-Code Anfragen',NULL,NULL,NULL,0,'',1,'','221','1','1',5,0,'team',0,389,389,0,819,819,0,7312,7312,0,1),(57,104,105,199,'Programmierung','Programming','C++, Java, HTML, PHP, Python, Perl und sogar TurboPascal wird hier besprochen','C++, Java, HTML, PHP, Python etc.',0,'',1,'','','0','196',6,0,'def',0,382,382,5,3939,3939,116,126831,126831,90219,1),(58,106,107,199,'Design, Grafik- und Webdesign','GFX & Web Design',NULL,NULL,0,'',1,'','','0','196',7,0,'def',0,572,572,4,9133,9133,25,127049,127049,116414,1),(59,108,109,199,'Server & Webspace','Server & Webspace','Alles rund ums Hosting und Betreiben von Homepages',NULL,0,'',1,'','','0','196',8,0,'def',0,207,207,4,1695,1695,24,126343,126343,124338,1),(60,38,39,194,'iCom Gameserver','iCom Gameserver','Alles �ber unsere Gameserver',NULL,0,'',1,'','169,178,221','196,221,199,215,208','196,221,187',4,0,'def',0,66,66,0,2000,2000,0,115641,125219,0,1),(245,196,197,0,NULL,NULL,NULL,NULL,0,'',1,'','','','',10,0,'def',0,0,0,0,0,0,0,0,0,0,1),(158,13,14,1,'Designertalk',NULL,NULL,NULL,0,'',1,'','221','1,2,154,175,221','1,2,154,175,221',6,0,'team',0,12,12,1,235,235,18,33372,33372,33372,1),(160,15,16,1,'Radio',NULL,NULL,NULL,0,'',1,'','221','1,2,187,175,3,221','1,2,187,175,3,221',7,0,'team',0,8,8,0,236,236,0,55312,55312,0,1),(161,36,37,194,'iCom Radio','iCom Radio','Alles �ber unser Radio',NULL,0,'',1,'','187,221','196,221,199,215,208','196,221,187',3,0,'def',0,40,40,1,625,625,4,127070,127070,35801,1),(162,17,18,1,'M�ll',NULL,'Threads die zu scheisse waren um sie zu l�schen :D',NULL,0,'',1,'','221','1,2,3,175,221','1,2,3,175,221',8,0,'team',0,112,112,8,2469,2469,85,126930,126930,61212,1),(164,110,111,199,'Handys und Smartphones','Mobile- & Smartphones',NULL,NULL,0,'',1,'','','0','196',9,0,'def',0,857,856,10,8120,8117,103,126997,126997,103049,1),(165,146,147,207,'Unterhaltung (altes Forum)',NULL,'ACHTUNG: Dieses Forum wird aufgel�st und gel�scht',NULL,0,'',1,'','','','',7,0,'def',0,0,0,0,0,0,0,44553,44553,0,1),(173,1,198,99999999,'root',NULL,'',NULL,0,'',1,'','','','',0,0,'def',0,0,0,0,0,0,0,0,0,0,1),(175,40,45,194,'iComPedia','iComPedia','Alles �ber unser Wiki',NULL,0,'',1,'','194,221','221,199,215,208','221,187',5,0,'def',1,6,6,0,60,60,0,21571,21571,0,1),(176,41,42,175,'Artikelw�sche',NULL,'Hier k�nnt Ihr Euch Artikel w�nschen',NULL,0,'',1,'','194,221','221,199,215,208','221,187',1,0,'def',0,2,2,0,3,3,0,48884,48884,0,1),(177,43,44,175,'Diskussionen',NULL,'Diskussionen �ber Wiki-Artikel',NULL,0,'',1,'','194,221','221,199,215,208','221,187',2,0,'def',0,6,6,0,27,27,0,36113,36113,0,1),(178,180,187,0,'Level 2','Level 2','Dieses Forum sehen nur unsere Stammuser',NULL,0,'',0,'','','1,2,3,175,208','1,2,3,175,208',8,0,'def',1,0,0,0,0,0,0,0,0,0,1),(179,181,182,178,'Allgemein','General',NULL,NULL,0,'',1,'','','1,2,3,175,208','1,2,3,175,208',1,0,'def',0,79,79,3,2970,2970,10,126931,126931,117260,1),(180,183,184,178,'Handel','Trading',NULL,NULL,0,'',1,'','','1,2,3,175,208','1,2,3,175,208',2,0,'def',0,80,80,1,754,754,1,126963,126963,88705,1),(182,56,65,0,'News Forum','News',NULL,NULL,0,'',0,'','175,3,215','0','196',3,0,'news',1,0,0,0,0,0,0,58085,58085,0,1),(183,60,61,237,'Allgemeine News','General news','Politik, Wissenschaft, Technik usw...','Politics, science, tech etc...',0,'',1,'','175,3,215','0','196',1,0,'news',0,1814,1807,29,33512,33465,385,127070,127070,127002,1),(184,62,63,237,'Scene News','Scene news','Neuigkeiten aus der Hacking- und Warez Szene',NULL,0,'',1,'','175,3,215','0','196',2,0,'news',0,414,413,10,7530,7529,180,127070,127070,97002,1),(185,166,179,0,'Handel','Trading',NULL,NULL,0,'',0,'','3','208,196','208,196,2,1,175,3',7,0,'def',1,0,0,0,0,0,0,46049,46049,0,1),(186,167,168,185,'Regeln','Rules','Spezielle Regeln beim Handeln','Specific rules for trading',0,'',1,'','3','196','2,1,175,3',1,0,'def',0,3,2,1,3,2,1,127066,127066,52350,1),(187,174,175,215,'Tausche','Exchange',NULL,NULL,0,'',1,'','3','208','208',3,0,'def',0,101,101,0,739,739,0,125195,125195,0,1),(188,170,171,215,'Biete','Offers',NULL,NULL,0,'',1,'','3','208','208',1,0,'def',0,486,485,9,5853,5846,148,126546,126801,125534,1),(189,172,173,215,'Suche','Requests',NULL,NULL,0,'',1,'','3','196','196',2,0,'def',0,605,605,6,4454,4454,66,127001,127001,114926,1),(190,185,186,178,'Tutorials','Tutorials',NULL,NULL,0,'',1,'','','1,2,3,175,208','1,2,3,175,208',3,0,'def',0,9,9,0,642,642,0,121582,121582,0,1),(191,176,177,215,'Verschenke','Give Aways',NULL,NULL,0,'',1,'','3','196','196',4,0,'def',0,233,233,7,2179,2179,73,127052,127052,116478,1),(194,33,50,48,'Bekanntmachungen','Announcements','Ank�ndigungen und Neuigkeiten rund um iLoad','News and announcements about iLoad',0,'',0,'','221','0,221,196,180,175,3,1,2,199,215,208','1,2,3,175,221,196,180,187',2,0,'def',1,0,0,0,0,0,0,0,0,0,1),(195,53,54,48,'Vorstellungen','Introductions','Hier k�nnt ihr Euch vorstellen','Say hello and tell us a little about yourself',0,'',1,'','221','196,221,199,215,208','196,221,187',4,0,'def',0,250,243,8,7322,7236,100,126977,127042,119481,1),(196,69,70,5,'Forenhilfe','Forum support','Hilfe f�r Neulinge','Help for newbies',0,'',1,'','','0','196',2,0,'def',0,991,989,17,7959,7946,164,126970,126994,126917,1),(214,138,139,207,'TV','Television',NULL,NULL,0,'',1,'','','0','196',3,0,'def',0,201,201,3,2102,2102,7,101042,126511,106924,1),(197,83,92,14,'Netzwelt','World Wide Web','Alles aus dem World Wide Web',NULL,0,'',0,'','','0','196',1,0,'def',1,0,0,0,0,0,0,0,0,0,1),(198,90,91,197,'Downtimes und invites','Downtimes & Invites',NULL,NULL,0,'',1,'','','0','196',4,0,'def',0,32,32,1,409,409,4,126627,126627,99836,1),(199,93,116,14,'Technik','Computers & Technologies','Hilfe, Kaufberatung und Diskussionen rund um die Technik',NULL,0,'',0,'','','0','196',2,0,'def',1,0,0,0,0,0,0,0,0,0,1),(200,112,113,199,'Sicherheit & Anonymit�t','Security & Anonymity',NULL,NULL,0,'',1,'','','0','196',10,0,'def',0,130,130,0,1465,1465,0,126460,126460,0,1),(201,117,132,14,'Gaming','Gaming','Hilfe und Diskussionen zu alten und neuen Games',NULL,0,'',0,'','','0','196',3,0,'def',1,0,0,0,0,0,0,0,0,0,1),(213,149,164,14,'Andere','Other Discussions',NULL,NULL,0,'',0,'','','0,196','196',5,0,'def',1,0,0,0,0,0,0,0,0,0,1),(202,118,119,201,'Spielediskussionen','Gaming Discussion',NULL,NULL,0,'',1,'','','0','196',1,0,'def',0,363,363,2,6278,6278,13,127046,127046,122254,1),(218,19,20,1,'Chatmodtalk',NULL,NULL,NULL,0,'',1,'','221','1,221,202','2,221,202',9,0,'team',0,0,0,0,0,0,0,0,0,0,1),(207,133,148,14,'Unterhaltung','Entertainment',NULL,NULL,0,'',0,'','','0','196',4,0,'def',1,0,0,0,0,0,0,0,0,0,1),(208,134,135,207,'Filme','Movies',NULL,NULL,0,'',1,'','','0','196',1,0,'def',0,761,761,5,16918,16918,45,126969,959,110773,1),(209,136,137,207,'Serien','TV Shows',NULL,NULL,0,'',1,'','','0','196',2,0,'def',0,201,201,1,2968,2968,14,124181,127051,92148,1),(210,142,143,207,'B�cher','Books',NULL,NULL,0,'',1,'','','0','196',5,0,'def',0,40,40,0,374,374,0,126875,126875,0,1),(211,140,141,207,'Musik','Music',NULL,NULL,0,'',1,'','','0','196',4,0,'def',0,920,920,10,11576,11576,401,127022,127022,125383,1),(212,144,145,207,'Kaufempfehlungen','Shopping Tips',NULL,NULL,0,'',1,'','','0','196',6,0,'def',0,146,146,0,1851,1851,0,111454,126219,0,1),(219,114,115,199,'Kaufberatung',NULL,'',NULL,0,'',1,'','','0','196',11,0,'def',0,749,749,3,8928,8928,49,127059,127059,118075,1),(230,48,49,194,'VIP\'s',NULL,NULL,NULL,0,'',1,'','221','180,175,3,1,2,221,199,215,208','180,175,3,1,2,221,187',7,0,'def',0,1,1,1,85,85,85,60667,60667,60667,1),(244,27,28,1,'Zwischenlager',NULL,'Alles von dem man net weiss obs noch okay is oder schon nimmer geht',NULL,0,'',1,'','3,221','1,2,3','1,2,3',13,0,'team',0,2,2,0,9,9,0,127014,127014,0,1),(233,23,24,1,'iLoad Times',NULL,NULL,NULL,0,'',1,'','221','212,1,175,2,3,221','212,1,175,2,3,221',11,0,'team',0,2,2,0,78,78,0,64497,65374,0,1),(235,25,26,1,'News',NULL,NULL,NULL,0,'',1,'','221','1,2,215,175,3,221','215,1,2,175,3,221',12,0,'team',0,20,20,0,538,538,0,110591,121588,0,1),(236,57,58,182,'News',NULL,'News vom iLoad News Team',NULL,0,'',1,'','3,175,215','0','196',3,0,'news',0,138,138,1,3593,3593,5,127071,127071,113856,1),(237,59,64,182,'User-News',NULL,'Von Usern eingestellte News',NULL,0,'',0,'','175,3,215','0','196',4,0,'news',1,0,0,0,0,0,0,81034,81034,0,1);
/*!40000 ALTER TABLE `forum_sections` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `forum_threads`
--

DROP TABLE IF EXISTS `forum_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_threads` (
  `thread_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) unsigned NOT NULL,
  `lang` char(2) COLLATE utf8_unicode_ci DEFAULT 'de',
  `lang_de` tinyint(1) DEFAULT '0',
  `lang_en` tinyint(1) DEFAULT '0',
  `open` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `closed_by_mod` tinyint(1) NOT NULL DEFAULT '1',
  `state` varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `state2` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '9normal',
  `priority` int(11) NOT NULL DEFAULT '0',
  `num_posts` int(11) unsigned NOT NULL DEFAULT '0',
  `num_hits` int(11) unsigned NOT NULL DEFAULT '0',
  `firstpost` int(11) unsigned NOT NULL,
  `lastpost` int(11) unsigned NOT NULL,
  PRIMARY KEY (`thread_id`),
  KEY `section` (`section_id`),
  KEY `firstpost` (`firstpost`),
  KEY `lastpost` (`lastpost`),
  KEY `state` (`state`),
  KEY `state2` (`state2`),
  KEY `id_section` (`thread_id`,`section_id`),
  KEY `lang` (`lang`),
  KEY `num_hits` (`num_hits`),
  KEY `lang_de` (`lang_de`),
  KEY `lang_en` (`lang_en`),
  KEY `priority` (`priority`),
  KEY `priority__lastpost` (`priority`,`lastpost`)
) ENGINE=InnoDB AUTO_INCREMENT=127072 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_threads`
--

LOCK TABLES `forum_threads` WRITE;
/*!40000 ALTER TABLE `forum_threads` DISABLE KEYS */;
INSERT INTO `forum_threads` VALUES (127072,2,'de',1,0,1,0,'','9normal',0,1,0,1,1),(127073,236,'de',1,0,1,1,'','9normal',0,1,0,916433,916433);
/*!40000 ALTER TABLE `forum_threads` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `forum_threads_visited_guests`
--

DROP TABLE IF EXISTS `forum_threads_visited_guests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_threads_visited_guests` (
  `thread_id` int(10) unsigned NOT NULL,
  `guest_id` binary(16) NOT NULL,
  PRIMARY KEY (`thread_id`,`guest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_threads_visited_users`
--

DROP TABLE IF EXISTS `forum_threads_visited_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_threads_visited_users` (
  `thread_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`thread_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_de` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_en` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `weight` int(11) NOT NULL DEFAULT '0',
  `email` tinyint(1) NOT NULL DEFAULT '0',
  `newswriter` tinyint(1) NOT NULL DEFAULT '0',
  `commentmaster` tinyint(1) NOT NULL DEFAULT '0',
  `shoutboxmaster` tinyint(1) NOT NULL DEFAULT '0',
  `developer` tinyint(1) NOT NULL DEFAULT '0',
  `groupmanager` tinyint(1) NOT NULL DEFAULT '0',
  `usermanager` tinyint(1) NOT NULL DEFAULT '0',
  `user_warnings` tinyint(1) NOT NULL DEFAULT '0',
  `forum_admin` tinyint(1) NOT NULL DEFAULT '0',
  `forum_mod` tinyint(1) NOT NULL DEFAULT '0',
  `forum_super_mod` tinyint(1) NOT NULL DEFAULT '0',
  `community_master` tinyint(1) NOT NULL DEFAULT '0',
  `inviter` tinyint(1) NOT NULL DEFAULT '0',
  `radio` tinyint(1) NOT NULL DEFAULT '0',
  `radio_admin` tinyint(1) NOT NULL DEFAULT '0',
  `guestbook_master` tinyint(1) NOT NULL DEFAULT '0',
  `wiki_admin` tinyint(1) NOT NULL DEFAULT '0',
  `wiki_mod` tinyint(1) NOT NULL DEFAULT '0',
  `report_page` tinyint(1) NOT NULL DEFAULT '0',
  `noads` tinyint(1) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name_de`),
  KEY `weight` (`weight`),
  KEY `public` (`public`)
) ENGINE=MyISAM AUTO_INCREMENT=233 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Admin',NULL,1,1,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0),(2,'Co-Admin',NULL,1,2,0,0,0,0,0,0,0,0,0,0,0,1,1,0,0,1,0,1,0,1,0),(3,'Moderator',NULL,1,4,0,0,1,1,0,0,0,1,0,1,1,1,1,0,0,1,0,0,1,1,0),(6,'Benutzer','User',1,90,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(154,'Designer',NULL,1,10,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(158,'Banned',NULL,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1),(177,'_shoutboxmaster',NULL,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(178,'BF3-Admin',NULL,1,61,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(168,'_mods',NULL,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,1,0),(175,'Supermoderator',NULL,1,3,0,0,1,1,0,0,0,1,0,1,1,1,1,0,0,1,0,0,0,1,0),(180,'VIP',NULL,1,71,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(181,'_newswriter',NULL,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(182,'_noad',NULL,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(187,'Radioadmin',NULL,1,40,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,1,0),(190,'_user_warnings',NULL,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,1,0),(192,'Geburtstagskinder',NULL,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(194,'Wiki Moderator',NULL,1,6,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,1,0),(195,'Am Pranger',NULL,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(196,'Alle Mitglieder','All Members',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(197,'Level 2','Level 2',1,85,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(199,'Supporter',NULL,1,35,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(201,'H','Trader',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(202,'Chatmoderator',NULL,1,9,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(204,'Gast-DJ','Guest-DJ',1,41,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(226,'G','Guests and members',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(208,'Level 2 (alle)','Level 2 (alle)',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(212,'iCom Times',NULL,1,36,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(215,'News','News',1,34,0,1,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,1,0),(221,'_mod_undercoder','_mod_undercoder',0,0,0,1,1,1,0,0,0,1,0,1,1,1,1,0,0,0,0,1,0,1,0),(222,'_poweradmin',NULL,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,0,1,0),(224,'Troll','Troll',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `guest_sessions`
--

DROP TABLE IF EXISTS `guest_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guest_sessions` (
  `id` binary(16) NOT NULL,
  `ip` binary(16) NOT NULL,
  `lasttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `num_used` bigint(20) unsigned NOT NULL DEFAULT '0',
  `validated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `guest_sessions_blocked`
--

DROP TABLE IF EXISTS `guest_sessions_blocked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guest_sessions_blocked` (
  `ip` binary(16) NOT NULL,
  `t` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `referer` varchar(100) NOT NULL,
  `user_agent` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `guests`
--

DROP TABLE IF EXISTS `guests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guests` (
  `id` binary(16) NOT NULL,
  `lasttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` char(8) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `bullshit` int(10) unsigned NOT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ext` char(4) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `ext_thumb` char(4) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'jpg',
  `name` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) unsigned NOT NULL,
  `width` smallint(5) unsigned NOT NULL,
  `height` smallint(5) unsigned NOT NULL,
  `hits_image` bigint(20) unsigned NOT NULL DEFAULT '0',
  `hits_thumb` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `bullshit` (`bullshit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invite_codes`
--

DROP TABLE IF EXISTS `invite_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invite_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(12) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `used` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49437 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invite_requests`
--

DROP TABLE IF EXISTS `invite_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invite_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `requesttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `editingtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `email` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'requested',
  `code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53717 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ipcounter`
--

DROP TABLE IF EXISTS `ipcounter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipcounter` (
  `ip` int(10) unsigned NOT NULL,
  `hits` bigint(20) unsigned NOT NULL DEFAULT '1',
  `all_hits` bigint(20) unsigned NOT NULL DEFAULT '1',
  `firsttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lasttime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_on_site` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lang_table`
--

DROP TABLE IF EXISTS `lang_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lang_table` (
  `namespace` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `hash` char(8) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `lang` char(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'de',
  `static` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `used` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`hash`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=223726 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `news_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `cover` char(8) NOT NULL,
  `name` varchar(200) NOT NULL,
  `introduce_content` text NOT NULL,
  `content` text NOT NULL,
  `source_text` text NOT NULL,
  `source_video` text NOT NULL,
  `source_image` text NOT NULL,
  `tags` varchar(200) NOT NULL,
  `timecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastedit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastupdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('edit','deleted','public') DEFAULT 'edit',
  PRIMARY KEY (`news_id`),
  FULLTEXT KEY `tags` (`tags`)
) ENGINE=MyISAM AUTO_INCREMENT=196 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (196,1,127073,'','iCom ist Open Source','Juhuuuu :)','','','','','','2012-06-04 22:28:09','0000-00-00 00:00:00','2012-06-04 22:28:25','public');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `radio`
--

DROP TABLE IF EXISTS `radio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radio` (
  `channel` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `host` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `admins` varchar(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `guests` varchar(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `current_dj` int(11) unsigned NOT NULL DEFAULT '0',
  `infos` varchar(750) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `chat_id` bigint(20) NOT NULL DEFAULT '0',
  `online` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastonline` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `listeners` int(11) unsigned NOT NULL DEFAULT '0',
  `peaklisteners` int(11) unsigned NOT NULL DEFAULT '0',
  `maxlisteners` int(11) unsigned NOT NULL DEFAULT '0',
  `bitrate` int(11) unsigned NOT NULL DEFAULT '0',
  `currentsong` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radio`
--

LOCK TABLES `radio` WRITE;
/*!40000 ALTER TABLE `radio` DISABLE KEYS */;
INSERT INTO `radio` VALUES
	('iC1','radio.icom.to',8001,'1','',0,'',0,0,0,0,0,0,0,''),
	('iC2','radio.icom.to',8003,'1','',0,'',0,0,0,0,0,0,0,''),
	('iC3','radio.icom.to',8005,'1','',0,'',0,0,0,0,0,0,0,''),
	('iC4','radio.icom.to',8007,'1','',0,'',0,0,0,0,0,0,0,''),
	('iC5','radio.icom.to',8009,'1','',0,'',0,0,0,0,0,0,0,'');
/*!40000 ALTER TABLE `radio` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `report_page`
--

DROP TABLE IF EXISTS `report_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_page` (
  `report_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `t` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ip` binary(16) DEFAULT NULL,
  `password` varchar(10) DEFAULT NULL,
  `url` varchar(500) NOT NULL,
  `class` varchar(10) NOT NULL,
  `message` text NOT NULL,
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `edit_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` text NOT NULL,
  `status` enum('open','accepted','rejected') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searches`
--

DROP TABLE IF EXISTS `searches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searches` (
  `id` int(10) unsigned NOT NULL,
  `str` varchar(100) NOT NULL,
  `num` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session_replay`
--

DROP TABLE IF EXISTS `session_replay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_replay` (
  `replay_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data` blob,
  PRIMARY KEY (`replay_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shoutbox_de`
--

DROP TABLE IF EXISTS `shoutbox_de`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shoutbox_de` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) unsigned NOT NULL,
  `message` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2862898 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shoutbox_de`
--

LOCK TABLES `shoutbox_de` WRITE;
/*!40000 ALTER TABLE `shoutbox_de` DISABLE KEYS */;
INSERT INTO `shoutbox_de` VALUES (1,'2012-06-04 22:16:57',1,'test');
/*!40000 ALTER TABLE `shoutbox_de` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `shoutbox_de_archive`
--

DROP TABLE IF EXISTS `shoutbox_de_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shoutbox_de_archive` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) unsigned NOT NULL,
  `message` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2834969 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shoutbox_en`
--

DROP TABLE IF EXISTS `shoutbox_en`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shoutbox_en` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1625 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shoutbox_en`
--

LOCK TABLES `shoutbox_en` WRITE;
/*!40000 ALTER TABLE `shoutbox_en` DISABLE KEYS */;
INSERT INTO `shoutbox_en` VALUES (1,'2012-06-04 22:16:57',1,'test');
/*!40000 ALTER TABLE `shoutbox_en` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `shoutbox_en_archive`
--

DROP TABLE IF EXISTS `shoutbox_en_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shoutbox_en_archive` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) unsigned NOT NULL,
  `message` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1197 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sph_counter`
--

DROP TABLE IF EXISTS `sph_counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sph_counter` (
  `n` varchar(20) NOT NULL,
  `v` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`n`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_bookmarks`
--

DROP TABLE IF EXISTS `user_bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_bookmarks` (
  `user_id` int(10) unsigned NOT NULL,
  `thing` enum('title','category','thread','wiki','news') NOT NULL,
  `thing_id` int(10) unsigned NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`thing`,`thing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_chat_categorys`
--

DROP TABLE IF EXISTS `user_chat_categorys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_chat_categorys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_de` varchar(60) DEFAULT NULL,
  `name_en` varchar(60) DEFAULT NULL,
  `place` int(10) unsigned NOT NULL DEFAULT '12345',
  `has_sub_categorys` tinyint(1) DEFAULT '0',
  `allow_ubb` tinyint(1) DEFAULT '0',
  `allow_html` tinyint(1) DEFAULT '0',
  `groups` varchar(4000) NOT NULL,
  `order_by` enum('name','time') DEFAULT 'time',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `groups` (`groups`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_chat_categorys`
--

LOCK TABLES `user_chat_categorys` WRITE;
/*!40000 ALTER TABLE `user_chat_categorys` DISABLE KEYS */;
INSERT INTO `user_chat_categorys` VALUES (1,'Main','Main',1,0,1,0,'1,2','time'),(3,'Radio','Radio',3,0,1,0,'187','name'),(4,'Chats','Chats',4,1,1,0,'196','time'),(5,'Bugs','Bugs',5,1,1,0,'196','time');
/*!40000 ALTER TABLE `user_chat_categorys` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `user_chat_content`
--

DROP TABLE IF EXISTS `user_chat_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_chat_content` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subid` bigint(20) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` varchar(8000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subid` (`subid`),
  KEY `uid` (`user_id`),
  KEY `subid_id` (`subid`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=586584 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_chat_content`
--

LOCK TABLES `user_chat_content` WRITE;
/*!40000 ALTER TABLE `user_chat_content` DISABLE KEYS */;
INSERT INTO `user_chat_content` VALUES (1,137,1,'2012-06-04 22:16:57','test');
/*!40000 ALTER TABLE `user_chat_content` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `user_chat_online_guests`
--

DROP TABLE IF EXISTS `user_chat_online_guests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_chat_online_guests` (
  `chat_id` bigint(20) unsigned NOT NULL,
  `guest_id` char(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `lasttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chat_id`,`guest_id`),
  KEY `chat_id` (`chat_id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_chat_online_users`
--

DROP TABLE IF EXISTS `user_chat_online_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_chat_online_users` (
  `chat_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `lasttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chat_id`,`user_id`),
  KEY `chat_id` (`chat_id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_chat_sub_categorys`
--

DROP TABLE IF EXISTS `user_chat_sub_categorys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_chat_sub_categorys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `name_de` varchar(60) DEFAULT NULL,
  `name_en` varchar(60) DEFAULT NULL,
  `place` int(10) unsigned NOT NULL DEFAULT '12345',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_chat_sub_categorys`
--

LOCK TABLES `user_chat_sub_categorys` WRITE;
/*!40000 ALTER TABLE `user_chat_sub_categorys` DISABLE KEYS */;
INSERT INTO `user_chat_sub_categorys` VALUES (6,4,'Support',NULL,100),(7,4,'Spam',NULL,12345),(8,4,'Sport',NULL,12345),(9,4,'Musik',NULL,12345),(10,4,'Filme, Serien, Dokus',NULL,12345),(11,4,'Spiele, Konsolen',NULL,12345),(12,4,'Sonstiges',NULL,12345);
/*!40000 ALTER TABLE `user_chat_sub_categorys` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `user_chats`
--

DROP TABLE IF EXISTS `user_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_chats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `sub_category_id` int(10) unsigned NOT NULL,
  `lang` char(2) NOT NULL DEFAULT 'de',
  `name` varchar(1000) NOT NULL,
  `default_text` varchar(250) NOT NULL,
  `content_ubb` text NOT NULL,
  `content_html` text NOT NULL,
  `timecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creator` int(10) unsigned NOT NULL,
  `admins` varchar(4000) NOT NULL,
  `admin_groups` varchar(4000) NOT NULL,
  `users` varchar(4000) NOT NULL,
  `banned_users` varchar(4000) NOT NULL,
  `groups` varchar(4000) NOT NULL,
  `points_from` int(10) unsigned NOT NULL DEFAULT '0',
  `points_to` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('open','closed','deleted') DEFAULT 'open',
  `place` int(10) unsigned NOT NULL DEFAULT '12345',
  `input_box` enum('textarea','input') DEFAULT 'textarea',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `category_id` (`category_id`),
  KEY `sub_category_id` (`sub_category_id`),
  FULLTEXT KEY `admins` (`admins`),
  FULLTEXT KEY `users` (`users`),
  FULLTEXT KEY `groups` (`groups`),
  FULLTEXT KEY `banned_users` (`banned_users`),
  FULLTEXT KEY `admin_groups` (`admin_groups`)
) ENGINE=MyISAM AUTO_INCREMENT=1274 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_chats`
--

LOCK TABLES `user_chats` WRITE;
/*!40000 ALTER TABLE `user_chats` DISABLE KEYS */;
INSERT INTO `user_chats` VALUES (137,1,0,'de','Gangsterbox','Foo','','','2010-12-31 02:39:56',1,'1','2','','','1,2,3,175',0,0,'open',200,'textarea');
/*!40000 ALTER TABLE `user_chats` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `user_denie_entrance`
--

DROP TABLE IF EXISTS `user_denie_entrance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_denie_entrance` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `mod_id` int(11) unsigned NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timeending` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `place` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `denie` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `reason` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`),
  KEY `mod_id` (`mod_id`),
  KEY `place` (`place`),
  KEY `denie` (`denie`),
  KEY `denie_2` (`denie`)
) ENGINE=InnoDB AUTO_INCREMENT=1269 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_friends`
--

DROP TABLE IF EXISTS `user_friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_friends` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `friend_id` int(11) unsigned NOT NULL,
  `status` varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`),
  KEY `friend` (`friend_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=54297 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_guestbook`
--

DROP TABLE IF EXISTS `user_guestbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_guestbook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `writer` int(11) unsigned NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`),
  KEY `writer` (`writer`)
) ENGINE=InnoDB AUTO_INCREMENT=12162 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_notes`
--

DROP TABLE IF EXISTS `user_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `writer_id` int(10) unsigned NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  `status` enum('ok','deleted') DEFAULT 'ok',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `writer_id` (`writer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=916 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_pns3`
--

DROP TABLE IF EXISTS `user_pns3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_pns3` (
  `pn_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL,
  `timecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creator` int(10) unsigned NOT NULL,
  `users` varchar(4000) NOT NULL,
  `involved_users` varchar(4000) NOT NULL,
  PRIMARY KEY (`pn_id`),
  KEY `id__lastmessage_id` (`pn_id`),
  FULLTEXT KEY `users` (`users`)
) ENGINE=MyISAM AUTO_INCREMENT=146902 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_pns3_content`
--

DROP TABLE IF EXISTS `user_pns3_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_pns3_content` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subid` bigint(20) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` varchar(8000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subid` (`subid`),
  KEY `uid` (`user_id`),
  KEY `subid_id` (`subid`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1072730 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_pns3_links`
--

DROP TABLE IF EXISTS `user_pns3_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_pns3_links` (
  `user_id` int(10) unsigned NOT NULL,
  `pn_id` bigint(20) unsigned NOT NULL,
  `has_new_message` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`pn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_pns3_new`
--

DROP TABLE IF EXISTS `user_pns3_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_pns3_new` (
  `user_id` int(10) unsigned NOT NULL,
  `pn_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`pn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_pns3_online_users`
--

DROP TABLE IF EXISTS `user_pns3_online_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_pns3_online_users` (
  `pn_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `lasttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pn_id`,`user_id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_pns3_polls`
--

DROP TABLE IF EXISTS `user_pns3_polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_pns3_polls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pn_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `reason` enum('invite','kick') DEFAULT 'invite',
  `votes_yes` varchar(4000) NOT NULL,
  `votes_no` varchar(4000) NOT NULL,
  `status` enum('open','yes','no','closed') DEFAULT 'open',
  PRIMARY KEY (`id`),
  KEY `pn_id` (`pn_id`),
  FULLTEXT KEY `votes` (`votes_yes`,`votes_no`)
) ENGINE=MyISAM AUTO_INCREMENT=408 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_poll_answers`
--

DROP TABLE IF EXISTS `user_poll_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_poll_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` bigint(20) unsigned NOT NULL,
  `answer` varchar(4000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3340 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_poll_votes`
--

DROP TABLE IF EXISTS `user_poll_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_poll_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `poll_id` bigint(20) unsigned NOT NULL,
  `answer_id` bigint(20) unsigned NOT NULL,
  `timevoted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `answer_id` (`answer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39126 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_polls`
--

DROP TABLE IF EXISTS `user_polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_polls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creator` int(10) unsigned NOT NULL,
  `groups` varchar(4000) NOT NULL,
  `status` enum('open','closed','deleted') DEFAULT 'closed',
  `question` varchar(4000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  FULLTEXT KEY `groups` (`groups`)
) ENGINE=MyISAM AUTO_INCREMENT=818 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sessions` (
  `user_id` int(11) unsigned NOT NULL,
  `ip` binary(16) NOT NULL,
  `data` blob NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_warnings`
--

DROP TABLE IF EXISTS `user_warnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_warnings` (
  `warning_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warner_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timeending` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `points` int(11) unsigned NOT NULL,
  `reason` varchar(15000) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`warning_id`),
  KEY `uid` (`user_id`),
  KEY `warner` (`warner_id`),
  KEY `uid__timeending` (`user_id`,`timeending`)
) ENGINE=InnoDB AUTO_INCREMENT=19803 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin','foo@bar.com','20427bc8e3f9ca222f630eec7dc9b869','71a6e17170b2d214b7fb9b8a1582f0a2','2008-06-10 10:17:34','2012-05-11 04:50:14','2012-05-11 05:16:14','2012-05-11 05:16:14','0000-00-00 00:00:00',44434953,1,'1,196','de,en','','','',1,'','','','private','private','friends','users',12316,1,'',579,632.325,1,1,0,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `wiki_aliases`
--

DROP TABLE IF EXISTS `wiki_aliases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_aliases` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page` int(11) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page` (`page`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=695 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_categorys`
--

DROP TABLE IF EXISTS `wiki_categorys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_categorys` (
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `lang` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'de',
  PRIMARY KEY (`name`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_changes`
--

DROP TABLE IF EXISTS `wiki_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_changes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` int(11) unsigned NOT NULL DEFAULT '0',
  `page` int(11) unsigned NOT NULL,
  `history` int(11) unsigned NOT NULL DEFAULT '0',
  `action` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `reason` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `page` (`page`),
  KEY `history` (`history`)
) ENGINE=InnoDB AUTO_INCREMENT=11069 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wiki_changes`
--

LOCK TABLES `wiki_changes` WRITE;
/*!40000 ALTER TABLE `wiki_changes` DISABLE KEYS */;
INSERT INTO `wiki_changes` VALUES (11069,'2012-06-04 22:01:53',1,1764,0,'article_created',''),(11070,'2012-06-04 22:01:53',1,1764,4837,'content_changed',''),(11071,'2012-06-04 22:02:29',1,1765,0,'article_created',''),(11072,'2012-06-04 22:02:29',1,1765,4838,'content_changed',''),(11073,'2012-06-04 22:04:09',1,1764,4837,'history_activated',''),(11074,'2012-06-04 22:05:54',1,1765,4838,'history_activated','');
/*!40000 ALTER TABLE `wiki_changes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `wiki_history`
--

DROP TABLE IF EXISTS `wiki_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page` int(11) unsigned NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page` (`page`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM AUTO_INCREMENT=4837 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wiki_history`
--

LOCK TABLES `wiki_history` WRITE;
/*!40000 ALTER TABLE `wiki_history` DISABLE KEYS */;
INSERT INTO `wiki_history` VALUES (4837,'2012-06-04 22:01:53',1764,'Neues Wiki'),(4838,'2012-06-04 22:02:29',1765,'Neu Wiki');
/*!40000 ALTER TABLE `wiki_history` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `wiki_pages`
--

DROP TABLE IF EXISTS `wiki_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang` char(2) CHARACTER SET ascii NOT NULL DEFAULT 'de',
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `timeadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastchange` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `history` int(11) unsigned NOT NULL DEFAULT '0',
  `hits` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang_name` (`lang`,`name`),
  KEY `deleted` (`deleted`),
  FULLTEXT KEY `name_fulltext` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1764 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wiki_pages`
--

LOCK TABLES `wiki_pages` WRITE;
/*!40000 ALTER TABLE `wiki_pages` DISABLE KEYS */;
INSERT INTO `wiki_pages` VALUES (1764,'de','Hauptseite','2012-06-04 22:01:53','2012-06-04 22:04:09',0,0,4837,2),(1765,'de','Main Page','2012-06-04 22:02:29','2012-06-04 22:05:54',0,0,4838,0);
/*!40000 ALTER TABLE `wiki_pages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `wiki_tickets`
--

DROP TABLE IF EXISTS `wiki_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page` int(11) unsigned NOT NULL,
  `opener` int(11) unsigned NOT NULL,
  `closer` int(11) unsigned NOT NULL DEFAULT '0',
  `timecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timeclosed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `message` varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page` (`page`),
  KEY `opener` (`opener`),
  KEY `closer` (`closer`)
) ENGINE=InnoDB AUTO_INCREMENT=348 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-06-02 23:58:36
