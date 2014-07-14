delimiter $$

CREATE DATABASE `recruit` /*!40100 DEFAULT CHARACTER SET utf8 */$$

delimiter $$

CREATE TABLE `alliance_history` (
  `allianceId` int(11) NOT NULL,
  `corporationId` int(11) NOT NULL,
  `joinedAt` int(11) NOT NULL,
  PRIMARY KEY (`allianceId`,`corporationId`,`joinedAt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `alliance_lookup` (
  `allianceId` int(11) NOT NULL,
  `allianceName` text NOT NULL,
  `allianceTicker` text NOT NULL,
  `foundedAt` int(11) NOT NULL,
  PRIMARY KEY (`allianceId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `application_history` (
  `notificationId` int(11) NOT NULL,
  `charId` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `issuedAt` int(11) NOT NULL,
  `text` text,
  PRIMARY KEY (`notificationId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `character_lookup` (
  `charId` int(11) NOT NULL,
  `charName` varchar(45) NOT NULL,
  PRIMARY KEY (`charId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `corporation_lookup` (
  `corporationId` int(11) NOT NULL,
  `corporationName` text NOT NULL,
  `corporationTicker` text NOT NULL,
  PRIMARY KEY (`corporationId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `employment_history` (
  `recordId` int(11) NOT NULL,
  `charId` int(11) NOT NULL,
  `corporationId` int(11) NOT NULL,
  `since` int(11) NOT NULL,
  PRIMARY KEY (`recordId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `charId` int(11) NOT NULL,
  `sessionId` text NOT NULL,
  `createdAt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8$$

