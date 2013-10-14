-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 14 ott, 2013 at 08:12 PM
-- Versione MySQL: 5.1.70
-- Versione PHP: 5.4.17-1~lucid+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `editorss`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `editorss_feeds`
--

CREATE TABLE IF NOT EXISTS `editorss_feeds` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `group` varchar(500) NOT NULL COMMENT 'GRUPPO::120',
  `title` varchar(250) NOT NULL COMMENT 'TITOLO::150',
  `description` text NOT NULL COMMENT 'DESCRIZIONE::250',
  `uri` varchar(255) NOT NULL COMMENT 'URI::120',
  `valid_resources` mediumint(9) NOT NULL COMMENT 'RISORSE VALIDE::32',
  `tags` varchar(500) NOT NULL COMMENT 'TAGS::120',
  `user` varchar(125) NOT NULL COMMENT 'UTENTE::75',
  `user_group` varchar(150) NOT NULL COMMENT 'GRUPPO UTENTI::150',
  `origin` enum('single_feed','page_feed','file_feed') NOT NULL DEFAULT 'single_feed' COMMENT 'ORIGINE::50',
  `last_insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'DATA DI INSERIMENTO::150',
  `automation_status` enum('play','stop','pause') NOT NULL DEFAULT 'play' COMMENT 'STATO::50',
  `is_active` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'ATTIVO::32',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `Ricerca` (`title`,`description`,`group`,`tags`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `editorss_feeds_news`
--

CREATE TABLE IF NOT EXISTS `editorss_feeds_news` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL COMMENT 'TITOLO::150',
  `description` text NOT NULL COMMENT 'DESCRIZIONE::250',
  `date` varchar(125) NOT NULL COMMENT 'DATA::150',
  `link` varchar(255) NOT NULL COMMENT 'URI::150',
  `parent_id` mediumint(9) NOT NULL,
  `tags` varchar(125) NOT NULL COMMENT 'TAGS::120',
  `user` varchar(125) NOT NULL COMMENT 'UTENTE::75',
  `last_insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'DATA DI INSERIMENTO::150',
  `automation_status` enum('play','stop','pause') NOT NULL DEFAULT 'play',
  `is_active` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `Ricerca` (`title`,`description`,`tags`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1726 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `editorss_origins_type`
--

CREATE TABLE IF NOT EXISTS `editorss_origins_type` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `elem_id` varchar(50) NOT NULL,
  `img` varchar(125) NOT NULL,
  `origin` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Menu della pagina "Aggiungi feed"' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Dump dei dati per la tabella `editorss_origins_type`
--

INSERT INTO `editorss_origins_type` (`id`, `name`, `elem_id`, `img`, `origin`) VALUES
(1, 'SINGOLO FEED', 'single', 'common/media/img/document_feed_128_ccc.png', 'single_feed'),
(2, 'DA FILE<br /><acronym title="eXtensible Markup Language">XML</acronym>, <acronym title="Comma-Separated Values">CSV</acronym> O <acronym title="Google Reader - Outline Processor Markup Language">OPML</acronym>', 'file', 'common/media/img/xml_document_128_ccc.png', 'file_feed'),
(3, 'PAGINA CON ELENCO DI FEEDS', 'feeds_page', 'common/media/img/website_128_ccc.png', 'page_feed');

-- --------------------------------------------------------

--
-- Struttura della tabella `__export_data_schema`
--

CREATE TABLE IF NOT EXISTS `__export_data_schema` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `size` mediumint(9) NOT NULL,
  `title` varchar(100) NOT NULL,
  `column` varchar(500) NOT NULL,
  `table` varchar(100) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `has_more_results` enum('0','1') NOT NULL DEFAULT '0',
  `order` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dump dei dati per la tabella `__export_data_schema`
--

INSERT INTO `__export_data_schema` (`id`, `size`, `title`, `column`, `table`, `reference`, `has_more_results`, `order`) VALUES
(1, 5, 'ID', 'id', 'editorss_feeds_news', 'editorss_feeds_news', '0', 1),
(2, 60, 'URI', 'link', 'editorss_feeds_news', 'editorss_feeds_news', '0', 5),
(3, 5, 'FEED', 'parent_id', 'editorss_feeds_news', 'editorss_feeds_news', '0', 2),
(4, 35, 'TITOLO', 'title', 'editorss_feeds_news', 'editorss_feeds_news', '0', 3),
(5, 50, 'DESCRIZIONE', 'description', 'editorss_feeds_news', 'editorss_feeds_news', '0', 4),
(6, 20, 'TAGS', 'tags', 'editorss_feeds_news', 'editorss_feeds_news', '0', 6),
(7, 10, 'UTENTE', 'user', 'editorss_feeds_news', 'editorss_feeds_news', '0', 7),
(8, 16, 'DATA', 'date', 'editorss_feeds_news', 'editorss_feeds_news', '0', 8),
(9, 0, 'ID', 'id', 'editorss_feeds', 'editorss_feeds', '0', 1),
(10, 0, 'TITOLO', 'title', 'editorss_feeds', 'editorss_feeds', '0', 2),
(11, 0, 'DESCRIZIONE', 'description', 'editorss_feeds', 'editorss_feeds', '0', 3),
(12, 0, 'URI', 'uri', 'editorss_feeds', 'editorss_feeds', '0', 4),
(13, 0, 'RISORSE VALIDE', 'valid_resources', 'editorss_feeds', 'editorss_feeds', '0', 5),
(14, 0, 'GRUPPI', 'group', 'editorss_feeds', 'editorss_feeds', '0', 6),
(15, 0, 'TAG', 'tags', 'editorss_feeds', 'editorss_feeds', '0', 7),
(16, 0, 'ORIGINE', 'origin', 'editorss_feeds', 'editorss_feeds', '0', 8),
(17, 0, 'STATO', 'automation_status', 'editorss_feeds', 'editorss_feeds', '0', 9),
(18, 0, 'ATTIVO', 'is_active', 'editorss_feeds', 'editorss_feeds', '0', 10),
(19, 0, 'DATA', 'last_insert_date', 'editorss_feeds', 'editorss_feeds', '0', 12),
(20, 0, 'UTENTE', 'user', 'editorss_feeds', 'editorss_feeds', '0', 11),
(21, 0, 'GRUPPO UTENTI', 'user_group', 'editorss_feeds', 'editorss_feeds', '0', 12);

