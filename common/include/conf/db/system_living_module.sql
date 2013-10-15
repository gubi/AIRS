-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 15 ott, 2013 at 02:48 AM
-- Versione MySQL: 5.1.70
-- Versione PHP: 5.4.17-1~lucid+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `system_living_module`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `current_scripts`
--

CREATE TABLE IF NOT EXISTS `current_scripts` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `path` varchar(500) NOT NULL,
  `contents` text NOT NULL,
  `short_description` varchar(250) NOT NULL,
  `long_description` text NOT NULL,
  `type` enum('dir','file') NOT NULL,
  `extension` varchar(9) NOT NULL,
  `mime_type` varchar(50) NOT NULL,
  `language_version` varchar(250) NOT NULL,
  `license` text NOT NULL,
  `category` varchar(150) NOT NULL,
  `package` varchar(150) NOT NULL,
  `author` varchar(250) NOT NULL,
  `link` varchar(255) NOT NULL,
  `size` varchar(50) NOT NULL,
  `permission` int(4) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_access_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creation_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_core` enum('0','1') NOT NULL,
  `status` enum('testing','wrong','ok') NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `contents` (`contents`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7007 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `diffs_backup`
--

CREATE TABLE IF NOT EXISTS `diffs_backup` (
  `parent_id` mediumint(9) NOT NULL,
  `path` varchar(500) NOT NULL,
  `diff` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
