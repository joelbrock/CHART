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
CREATE TABLE IF NOT EXISTS `attendance` (
  `year` year(4) NOT NULL,
  `event` varchar(20) NOT NULL,
  `eventID` int(11) NOT NULL,
  `att` varchar(1) NOT NULL,
  `coop` varchar(255) NOT NULL,
  `clientID` int(8) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `title` varchar(20) NOT NULL,
  `qtr` tinyint(1) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `coop` (`coop`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=590 ;
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
CREATE TABLE IF NOT EXISTS `events` (
  `eventid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `region` varchar(20) NOT NULL,
  PRIMARY KEY (`eventid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;
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
-- Table structure for table `ncga_coops`
--

CREATE TABLE IF NOT EXISTS `ncga_coops` (
  `in_ncga` varchar(10) NOT NULL,
  `in_cbld` varchar(10) NOT NULL,
  `staffID` int(6) NOT NULL,
  `clientID` varchar(20) NOT NULL,
  `coop_name` varchar(100) NOT NULL,
  `corridor` varchar(10) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=151 ;

--
-- Dumping data for table `ncga_coops`
--

INSERT INTO `ncga_coops` (`in_ncga`, `in_cbld`, `staffID`, `clientID`, `coop_name`, `corridor`, `id`) VALUES
('NCGA', 'CBLD', 0, '220', '3 Rivers Natural Grocery', 'c', 1),
('NCGA', 'CBLD', 0, '3', 'Abundance Cooperative Market', 'e', 2),
('NCGA', '', 0, '5', 'Alberta Cooperative Grocery', 'w', 3),
('NCGA', 'CBLD', 0, '7', 'Amazing Grains Natural Foods Market', 'c', 4),
('NCGA', '', 0, '8', 'Ashland Food Co-op', 'w', 5),
('NCGA', '', 0, '9', 'Astoria Co-op', 'w', 6),
('NCGA', '', 0, '10', 'Basics Cooperative', 'c', 7),
('NCGA', 'CBLD', 0, '12', 'Belfast Co-op', 'e', 8),
('NCGA', 'CBLD', 0, '14', 'Berkshire Co-op Market', 'e', 9),
('NCGA', 'CBLD', 0, '18', 'Bloomingfoods Market and Deli', 'c', 10),
('NCGA', '', 0, '19', 'Blue Hill Co-op Community Market & Cafe', 'e', 11),
('NCGA', 'CBLD', 0, '20', 'Bluff Country Co-op', 'c', 12),
('NCGA', '', 0, '21', 'Boise Co-op', 'w', 13),
('NCGA', 'CBLD', 0, '23', 'Brattleboro Food Co-op', 'e', 14),
('NCGA', 'CBLD', 0, '24', 'BriarPatch Co-op Market', 'w', 15),
('NCGA', 'CBLD', 0, '118', 'Central Co-op', 'w', 16),
('NCGA', 'CBLD', 0, '26', 'Chatham Marketplace', 'e', 17),
('NCGA', 'CBLD', 0, '27', 'Chequamegon Food Co-op', 'c', 18),
('NCGA', 'CBLD', 0, '28', 'Chico Natural Foods Cooperative', 'w', 19),
('NCGA', 'CBLD', 0, '29', 'City Center Market', 'c', 20),
('NCGA', 'CBLD', 0, '30', 'City Market/Onion River Co-op', 'e', 21),
('NCGA', 'CBLD', 0, '33', 'Common Ground Food Co-op', 'c', 22),
('NCGA', '', 0, '35', 'Community Food Co-op (Bellingham)', 'w', 23),
('NCGA', '', 0, '36', 'Community Food Co-op (Bozeman)', 'w', 24),
('NCGA', 'CBLD', 0, '38', 'Company Shops Market', 'e', 25),
('NCGA', 'CBLD', 0, '39', 'Concord Food Co-op', 'e', 26),
('NCGA', 'CBLD', 0, '40', 'Cook County Whole Foods Co-op', 'c', 27),
('NCGA', 'CBLD', 0, '42', 'Co-opportunity Consumers Co-op', 'w', 28),
('NCGA', 'CBLD', 0, '48', 'Davis Food Co-op', 'w', 29),
('NCGA', 'CBLD', 0, '47', 'Daily Groceries Co-op, Athens GA', 'e', 30),
('NCGA', 'CBLD', 0, '49', 'Deep Roots Market', 'e', 31),
('NCGA', 'CBLD', 0, '50', 'Durango Natural Foods', 'w', 32),
('NCGA', 'CBLD', 0, '53', 'East End Food Co-op', 'e', 33),
('NCGA', 'CBLD', 0, '54', 'East Lansing Food Co-op', 'c', 34),
('NCGA', 'CBLD', 0, '55', 'Eastside Food Cooperative', 'c', 35),
('NCGA', 'CBLD', 0, '272', 'Elm City Market', 'e', 36),
('NCGA', 'CBLD', 0, '57', 'Ever''man Natural Foods Co-op, Inc.', 'e', 37),
('NCGA', 'CBLD', 0, '264', 'Fairbanks Community Coopertive Market', 'w', 38),
('NCGA', '', 0, '62', 'First Alternative Co-op', 'w', 39),
('NCGA', 'CBLD', 0, '63', 'Flatbush Food Cooperative', 'e', 40),
('NCGA', 'CBLD', 0, '65', 'Food Conspiracy Co-op', 'w', 41),
('NCGA', 'CBLD', 0, '66', 'Food Front Cooperative Grocery', 'w', 42),
('NCGA', 'CBLD', 0, '68', 'Franklin Community Co-op', 'e', 43),
('NCGA', 'CBLD', 0, '69', 'French Broad Food Co-op', 'e', 44),
('NCGA', 'CBLD', 0, '70', 'Friendly City Food Co-op', 'e', 45),
('NCGA', '', 0, '74', 'Good Earth Market', 'w', 46),
('NCGA', 'CBLD', 0, '', 'Good Foods Market & Cafe', 'e', 47),
('NCGA', 'CBLD', 0, '78', 'Grain Train Natural Foods Market', 'c', 48),
('NCGA', '', 0, '', 'Great Basin Community Co-op', 'w', 49),
('NCGA', 'CBLD', 0, '82', 'GreenStar Cooperative Market', 'e', 50),
('NCGA', 'CBLD', 0, '80', 'GreenTree Cooperative Grocery', 'c', 51),
('NCGA', '', 0, '84', 'Hampden Park', 'c', 52),
('NCGA', 'CBLD', 0, '85', 'Hanover Co-op Food Store', 'e', 53),
('NCGA', '', 0, '86', 'Harmony Natural Foods Co-op', 'c', 54),
('NCGA', 'CBLD', 0, '87', 'Harvest Co-op Markets', 'e', 55),
('NCGA', 'CBLD', 0, '87', 'Harvest Moon Natural Foods ', 'c', 56),
('NCGA', 'CBLD', 0, '89', 'Hendersonville Community Co-op', 'e', 57),
('NCGA', '', 0, '93', 'Honest Weight Food Co-op, Inc.', 'e', 58),
('NCGA', 'CBLD', 0, '95', 'Hunger Mountain Co-op', 'e', 59),
('NCGA', '', 0, '96', 'Hungry Hollow Co-op', 'e', 60),
('NCGA', '', 0, '98', 'Isla Vista Food Co-op', 'w', 61),
('NCGA', 'CBLD', 0, '100', 'Just Food Co-op', 'c', 62),
('NCGA', '', 0, '101', 'Just Local Food', 'c', 63),
('NCGA', '', 0, '104', 'Keeweenaw Cooperative', 'c', 64),
('NCGA', '', 0, '109', 'La Monta?ita Co-op', 'w', 65),
('NCGA', 'CBLD', 0, '110', 'Lakewinds Natural Foods', 'c', 66),
('NCGA', 'CBLD', 0, '112', 'Lexington Cooperative Market', 'e', 67),
('NCGA', '', 0, '113', 'Life Grocery & Cafe', 'e', 68),
('NCGA', 'CBLD', 0, '114', 'Linden Hills Co-op', 'c', 69),
('NCGA', '', 0, '115', 'Littleton Food Co-op', 'e', 70),
('NCGA', 'CBLD', 0, '116', 'Los Alamos Cooperative Market', 'w', 71),
('NCGA', 'CBLD', 0, '121', 'Maple City Market', 'c', 72),
('NCGA', '', 0, '122', 'Mariposa Food Co-op', 'e', 73),
('NCGA', '', 0, '123', 'Marquette Food Co-op', 'c', 74),
('NCGA', '', 0, '124', 'Medford Food Co-op', 'w', 75),
('NCGA', '', 0, '125', 'Menomonie Market Food Co-op', 'c', 76),
('NCGA', 'CBLD', 0, '126', 'Middlebury Natural Foods Co-op', 'e', 77),
('NCGA', '', 0, '128', 'Mississippi Market', 'c', 78),
('NCGA', 'CBLD', 0, '271', 'Monadnock Food Co-op', 'e', 79),
('NCGA', 'CBLD', 0, '131', 'Moscow Food Co-op', 'w', 80),
('NCGA', 'CBLD', 0, '134', 'Mountain View Market', 'w', 81),
('NCGA', '', 0, '138', 'Natural Harvest Food Co-op', 'c', 82),
('NCGA', 'CBLD', 0, '144', 'Neighborhood Co-op Grocery', 'c', 83),
('NCGA', 'CBLD', 0, '145', 'New Leaf Market', 'e', 84),
('NCGA', 'CBLD', 0, '147', 'New Pioneer Food Co-op', 'c', 85),
('NCGA', '', 0, '149', 'North Coast Co-op', 'w', 86),
('NCGA', '', 0, '155', 'Ocean Beach People''s Organic Food Co-op', 'w', 87),
('NCGA', '', 0, '158', 'Olympia Food Co-op', 'w', 88),
('NCGA', '', 0, '159', 'Oneota Community Co-op', 'c', 89),
('NCGA', 'CBLD', 0, '160', 'Open Harvest Cooperative Grocery', 'c', 90),
('NCGA', 'CBLD', 0, '161', 'Oryana Natural Foods Market', 'c', 91),
('NCGA', 'CBLD', 0, '164', 'Outpost Natural Foods Cooperative', 'c', 92),
('NCGA', 'CBLD', 0, '165', 'Ozark Natural Foods', 'c', 93),
('NCGA', 'CBLD', 0, '167', 'PCC Natural Markets', 'pcc', 94),
('NCGA', 'CBLD', 0, '169', 'People''s Food Co-op - Ann Arbor', 'c', 95),
('NCGA', 'CBLD', 0, '171', 'People''s Food Co-op - Kalamazoo', 'c', 96),
('NCGA', '', 0, '168', 'People''s Food Co-op - LaCrosse/Rochester', 'c', 97),
('NCGA', 'CBLD', 0, '173', 'People''s Food Co-op - Portland', 'w', 98),
('NCGA', '', 0, '174', 'Phoenix Earth Food Co-op', 'c', 99),
('NCGA', 'CBLD', 0, '181', 'Putney Food Co-op', 'e', 100),
('NCGA', '', 0, '183', 'Quincy Natural Foods Co-op', 'w', 101),
('NCGA', 'CBLD', 0, '187', 'Rising Tide Community Market', 'e', 102),
('NCGA', 'CBLD', 0, '188', 'River Market Community Co-op', 'c', 103),
('NCGA', 'CBLD', 0, '189', 'River Valley Market', 'e', 104),
('NCGA', 'CBLD', 0, '190', 'Roanoke Natural Foods Co-op', 'e', 105),
('NCGA', 'CBLD', 0, '194', 'Sacramento Natural Foods Co-op', 'w', 106),
('NCGA', 'CBLD', 0, '198', 'Sevananda Natural Foods Market', 'e', 107),
('NCGA', 'CBLD', 0, '199', 'Seward Co-op Grocery & Deli', 'c', 108),
('NCGA', '', 0, '201', 'Silver City Food Co-op', 'w', 109),
('NCGA', '', 0, '202', 'Skagit Valley Food Co-op', 'w', 110),
('NCGA', 'CBLD', 0, '203', 'Sno-Isle Natural Foods Co-op', 'w', 111),
('NCGA', 'CBLD', 0, '206', 'Springfield Food Co-op', 'e', 112),
('NCGA', 'CBLD', 0, '207', 'St. Peter Food Co-op & Deli', 'c', 113),
('NCGA', 'CBLD', 0, '215', 'Syracuse Real Food Co-op', 'e', 114),
('NCGA', 'CBLD', 0, '34', 'The Common Market', 'e', 115),
('NCGA', 'CBLD', 0, '217', 'The Co-op', 'c', 116),
('NCGA', '', 0, '218', 'The Food Co-op', 'w', 117),
('NCGA', 'CBLD', 0, '37', 'The Merc', 'c', 118),
('NCGA', '', 0, '239', 'The Wedge Co-op', 'c', 119),
('NCGA', 'CBLD', 0, '221', 'Three Rivers Market', 'e', 120),
('NCGA', 'CBLD', 0, '222', 'Tidal Creek Cooperative Food Market', 'e', 121),
('NCGA', 'CBLD', 0, '216', 'TPSS Food Co-op', 'e', 122),
('NCGA', '', 0, '226', 'Ukiah Natural Foods Co-op', 'w', 123),
('NCGA', 'CBLD', 0, '229', 'Upper Valley Food Co-op', 'e', 124),
('NCGA', '', 0, '232', 'Valley Natural Foods', 'c', 125),
('NCGA', 'CBLD', 0, '236', 'Viroqua Food Co-op', 'c', 126),
('NCGA', 'CBLD', 0, '237', 'Weaver Street Market', 'e', 127),
('NCGA', 'CBLD', 0, '238', 'Weavers Way Co-op', 'e', 128),
('NCGA', '', 0, '241', 'Wheatsfield Cooperative', 'c', 129),
('NCGA', 'CBLD', 0, '243', 'Wheatsville Co-op', 'c', 130),
('NCGA', 'CBLD', 0, '247', 'Whole Foods Co-op - Duluth', 'c', 131),
('NCGA', '', 0, '246', 'Whole Foods Cooperative Erie', 'e', 132),
('NCGA', 'CBLD', 0, '248', 'Wild Oats Market', 'e', 133),
('NCGA', 'CBLD', 0, '249', 'Willimantic', 'e', 134),
('NCGA', 'CBLD', 0, '250', 'Willy Street Co-op', 'c', 135),
('NCGA', 'CBLD', 0, '258', 'Ypsilanti Food Co-op', 'c', 136),
('NCGA', 'CBLD', 0, '297', 'Creekside, Philly area', 'e', 137),
('NCGA', 'CBLD', 0, '261', 'Community Owned Grocery, Willmar MN', 'c', 138),
('NCGA', 'CBLD', 0, '269', 'East Aurora', 'e', 139),
('NCGA', 'CBLD', 0, '276', 'Fiddleheads', 'e', 140),
('NCGA', 'CBLD', 0, '281', 'Fuquay-Varina Community Market, NC', 'e', 141),
('NCGA', 'CBLD', 0, '73', 'Good Earth - St Cloud', 'c', 142),
('NCGA', 'CBLD', 0, '260', 'Hub City', 'e', 143),
('NCGA', 'CBLD', 0, '280', 'Kensington Co-op', 'e', 144),
('NCGA', 'CBLD', 0, '142', 'National Cooperative Grocers Association (NCGA)', '', 145),
('NCGA', 'CBLD', 0, '51', 'Durham', '', 146),
('NCGA', 'CBLD', 0, '146', 'New Orleans Food Co-op', 'c', 147),
('NCGA', 'CBLD', 0, '265', 'Placerville', 'w', 148),
('NCGA', 'CBLD', 0, '279', 'South Philly', 'e', 149),
('NCGA', 'CBLD', 0, '267', 'Tacoma', 'w', 150);

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
