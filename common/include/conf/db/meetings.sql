-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 14 ott, 2013 at 08:32 PM
-- Versione MySQL: 5.1.70
-- Versione PHP: 5.4.17-1~lucid+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `meetings`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `current_meetings`
--

CREATE TABLE IF NOT EXISTS `current_meetings` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `attendeePW` varchar(32) NOT NULL,
  `moderatorPW` varchar(32) NOT NULL,
  `welcome_message` text NOT NULL,
  `dialNumber` varchar(15) NOT NULL,
  `voiceBridge` varchar(5) NOT NULL,
  `webVoice` varchar(100) NOT NULL,
  `logoutURL` varchar(255) NOT NULL,
  `maxParticipants` int(11) NOT NULL DEFAULT '20',
  `record` enum('true','false') NOT NULL DEFAULT 'false',
  `duration` int(11) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` datetime NOT NULL,
  `user` varchar(150) NOT NULL,
  `restrict_to_level` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `restrict_to_group` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `recorded_meetings`
--

CREATE TABLE IF NOT EXISTS `recorded_meetings` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
