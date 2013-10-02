-- MySQL dump 10.13  Distrib 5.5.19, for osx10.6 (i386)
--
-- Host: YOUR.DB.HOST    Database: chart
-- ------------------------------------------------------
-- Server version	5.1.39-log

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
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `event` varchar(20) NOT NULL,
  `att` varchar(1) NOT NULL,
  `coop` varchar(255) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `qtr` tinyint(1) NOT NULL,
  FULLTEXT KEY `coop` (`coop`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `program` varchar(20) NOT NULL,
  `total_hours` decimal(11,2) NOT NULL COMMENT 'current total',
  `q_hours` decimal(11,1) NOT NULL COMMENT 'Hours alloted per Q',
  `address` longtext NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `url` varchar(255) NOT NULL,
  `contact_details` varchar(255) NOT NULL,
  `BalancedHrsUse` tinyint(4) NOT NULL,
  `UsingPG` tinyint(4) NOT NULL,
  `CBLDSince` date NOT NULL,
  `ExpireDate` date NOT NULL,
  `Expansion` tinyint(4) NOT NULL,
  `NewGM` tinyint(4) NOT NULL,
  `Retain` int(11) NOT NULL,
  `RetreatDate` date NOT NULL,
  `RetreatDesc` longtext NOT NULL,
  `gm_name` varchar(255) NOT NULL,
  `gm_contact` varchar(255) NOT NULL,
  `gm_email` varchar(255) NOT NULL,
  `chair_name` varchar(255) NOT NULL,
  `chair_contact` varchar(255) NOT NULL,
  `chair_email` varchar(255) NOT NULL,
  `board_name` varchar(255) NOT NULL,
  `board_contact` varchar(255) NOT NULL,
  `board_email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=285 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clients_retreat_repos`
--

DROP TABLE IF EXISTS `clients_retreat_repos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients_retreat_repos` (
  `crr_id` int(11) NOT NULL AUTO_INCREMENT,
  `clientID` int(11) NOT NULL,
  `retreatDate` date NOT NULL,
  `upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`crr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Repository for client retreat dates';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_attendance`
--

DROP TABLE IF EXISTS `event_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_attendance` (
  `coop` int(4) NOT NULL,
  `eventid` int(4) NOT NULL,
  `count` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `eventid` tinyint(4) NOT NULL,
  `event_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flags`
--

DROP TABLE IF EXISTS `flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flags` (
  `flag_id` int(11) NOT NULL AUTO_INCREMENT,
  `flag_title` varchar(255) NOT NULL,
  `flag_upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`flag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal`
--

DROP TABLE IF EXISTS `journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal` (
  `ClientName` varchar(255) DEFAULT NULL,
  `ClientID` varchar(3) DEFAULT NULL,
  `StaffID` tinyint(4) DEFAULT NULL,
  `UsingPG` tinyint(1) DEFAULT NULL,
  `CBLDSince` date DEFAULT NULL,
  `Expansion` tinyint(1) DEFAULT NULL,
  `NewGM` tinyint(1) DEFAULT NULL,
  `Flags` varchar(255) DEFAULT NULL,
  `RetreatDate` date DEFAULT NULL,
  `BalancedHrsUse` tinyint(1) DEFAULT NULL,
  `Hours` float DEFAULT NULL,
  `Billable` tinyint(1) NOT NULL,
  `TeamNote` text,
  `ClientNote` text,
  `RetreatNote` text NOT NULL,
  `RetreatDate1` date NOT NULL,
  `RetreatDate2` date NOT NULL,
  `QtrInc` int(11) NOT NULL,
  `Quarterly` text NOT NULL,
  `Intro` longtext NOT NULL,
  `Retain` tinyint(1) DEFAULT NULL,
  `Date` date NOT NULL,
  `Category` mediumtext NOT NULL COMMENT '(call,research,quarterly)',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1708 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal_flags`
--

DROP TABLE IF EXISTS `journal_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_flags` (
  `jf_id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_id` int(11) NOT NULL,
  `flag_id` int(11) NOT NULL,
  PRIMARY KEY (`jf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal_tmp`
--

DROP TABLE IF EXISTS `journal_tmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_tmp` (
  `ClientName` varchar(255) DEFAULT NULL,
  `ClientID` varchar(3) DEFAULT NULL,
  `StaffID` tinyint(4) DEFAULT NULL,
  `UsingPG` tinyint(1) DEFAULT NULL,
  `CBLDSince` date DEFAULT NULL,
  `Expansion` tinyint(1) DEFAULT NULL,
  `NewGM` tinyint(1) DEFAULT NULL,
  `Flags` varchar(255) DEFAULT NULL,
  `RetreatDate` date DEFAULT NULL,
  `BalancedHrsUse` tinyint(1) DEFAULT NULL,
  `Hours` float DEFAULT NULL,
  `Billable` tinyint(1) NOT NULL,
  `TeamNote` text,
  `ClientNote` text,
  `QtrInc` int(11) NOT NULL,
  `Quarterly` text NOT NULL,
  `Intro` longtext NOT NULL,
  `Retain` tinyint(1) DEFAULT NULL,
  `Date` date NOT NULL,
  `Category` mediumtext NOT NULL COMMENT '(call,research,quarterly)',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_content`
--

DROP TABLE IF EXISTS `report_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content-1` longtext NOT NULL,
  `content-2` longtext NOT NULL,
  `content-3` longtext NOT NULL,
  `intro_default` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff` (
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `photo` blob NOT NULL,
  `clients` varchar(512) NOT NULL,
  `admin` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `staff_clients`
--

DROP TABLE IF EXISTS `staff_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff_clients` (
  `sc_id` int(11) NOT NULL AUTO_INCREMENT,
  `clientID` int(11) NOT NULL,
  `staffID` int(11) NOT NULL,
  PRIMARY KEY (`sc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=191 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-10-01 23:17:40
