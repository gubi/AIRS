-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 14 ott, 2013 at 08:30 PM
-- Versione MySQL: 5.1.70
-- Versione PHP: 5.4.17-1~lucid+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `system_logs`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `airs_browser_scraping`
--

CREATE TABLE IF NOT EXISTS `airs_browser_scraping` (
  `uri` varchar(500) NOT NULL COMMENT 'URI::250',
  `user` varchar(16) NOT NULL COMMENT 'Utente di afferenza::150',
  `data` date NOT NULL COMMENT 'Data::100',
  `ora` time NOT NULL COMMENT 'Ora::50'
) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 COMMENT='Pagine web scansionate';

-- --------------------------------------------------------

--
-- Struttura della tabella `airs_login`
--

CREATE TABLE IF NOT EXISTS `airs_login` (
  `user` varchar(16) NOT NULL COMMENT 'Utente::75',
  `ip` varchar(16) NOT NULL COMMENT 'Indirizzo IP::80',
  `data` date NOT NULL COMMENT 'Data::100',
  `ora` time NOT NULL COMMENT 'Ora::50',
  `referer` varchar(125) NOT NULL COMMENT 'Provenienza::250',
  `action` enum('login','logout') NOT NULL COMMENT 'Operazione::80'
) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 COMMENT='Accessi effettuati';

-- --------------------------------------------------------

--
-- Struttura della tabella `airs_mail`
--

CREATE TABLE IF NOT EXISTS `airs_mail` (
  `id` varchar(100) NOT NULL,
  `to` varchar(1000) NOT NULL COMMENT 'Destinatario::200',
  `subject` varchar(500) NOT NULL COMMENT 'Oggetto::250',
  `body` text NOT NULL COMMENT 'Corpo::400',
  `data` date NOT NULL COMMENT 'Data::100',
  `ora` time NOT NULL COMMENT 'Ora::50'
) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 COMMENT='E-mail inviate';

-- --------------------------------------------------------

--
-- Struttura della tabella `airs_searches`
--

CREATE TABLE IF NOT EXISTS `airs_searches` (
  `user` varchar(16) NOT NULL COMMENT 'Utente::75',
  `ip` varchar(16) NOT NULL COMMENT 'Indirizzo IP::80',
  `word` text NOT NULL COMMENT 'Chiavi di ricerca::200',
  `data` date NOT NULL COMMENT 'Data::100',
  `ora` time NOT NULL COMMENT 'Ora::50',
  `referer` varchar(125) NOT NULL COMMENT 'Provenienza::250',
  `redirect` enum('0','1') NOT NULL COMMENT 'Redirezionamento::120'
) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 COMMENT='Ricerche nel Sistema';

-- --------------------------------------------------------

--
-- Struttura della tabella `airs_users_feedback`
--

CREATE TABLE IF NOT EXISTS `airs_users_feedback` (
  `page` varchar(500) NOT NULL COMMENT 'Pagina::250',
  `user` varchar(16) NOT NULL COMMENT 'Utente::100',
  `comment` text NOT NULL COMMENT 'Messaggio::400',
  `data` date NOT NULL COMMENT 'Data::100',
  `ora` time NOT NULL COMMENT 'Ora::50'
) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 COMMENT='Feedback degli utenti';

-- --------------------------------------------------------

--
-- Struttura della tabella `__export_data_schema`
--

CREATE TABLE IF NOT EXISTS `__export_data_schema` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `column` varchar(500) NOT NULL,
  `table` varchar(100) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `has_more_results` enum('0','1') NOT NULL DEFAULT '0',
  `order` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Dump dei dati per la tabella `__export_data_schema`
--

INSERT INTO `__export_data_schema` (`id`, `title`, `column`, `table`, `reference`, `has_more_results`, `order`) VALUES
(1, 'URI', 'uri', 'airs_browser_scraping', 'airs_browser_scraping', '0', 1),
(2, 'UTENTE', 'user', 'airs_browser_scraping', 'airs_browser_scraping', '0', 2),
(3, 'DATA', 'data', 'airs_browser_scraping', 'airs_browser_scraping', '0', 3),
(4, 'ORA', 'ora', 'airs_browser_scraping', 'airs_browser_scraping', '0', 4),
(5, 'UTENTE', 'user', 'airs_login', 'airs_login', '0', 1),
(6, 'PROVENIENZA', 'referer', 'airs_login', 'airs_login', '0', 2),
(7, 'OPERAZIONE', 'action', 'airs_login', 'airs_login', '0', 3),
(8, 'INDIRIZZO IP(v4)', 'ip', 'airs_login', 'airs_login', '0', 4),
(9, 'DATA', 'data', 'airs_login', 'airs_login', '0', 5),
(10, 'ORA', 'ora', 'airs_login', 'airs_login', '0', 6),
(11, 'DESTINATARIO', 'to', 'airs_mail', 'airs_mail', '0', 1),
(12, 'OGGETTO', 'subject', 'airs_mail', 'airs_mail', '0', 2),
(13, 'CORPO', 'body', 'airs_mail', 'airs_mail', '0', 3),
(14, 'DATA', 'data', 'airs_mail', 'airs_mail', '0', 4),
(15, 'ORA', 'ora', 'airs_mail', 'airs_mail', '0', 5),
(16, 'UTENTE', 'user', 'airs_searches', 'airs_searches', '0', 1),
(17, 'CHIAVI DI RICERCA', 'word', 'airs_searches', 'airs_searches', '0', 2),
(18, 'DATA', 'data', 'airs_searches', 'airs_searches', '0', 4),
(19, 'ORA', 'ora', 'airs_searches', 'airs_searches', '0', 0),
(20, 'PROVENIENZA', 'referer', 'airs_searches', 'airs_searches', '0', 5),
(21, 'INDIRIZZO IP(v4)', 'ip', 'airs_searches', 'airs_searches', '0', 6),
(22, 'REDIREZIONAMENTO', 'redirect', 'airs_searches', 'airs_searches', '0', 7),
(23, 'UTENTE', 'user', 'airs_users_feedback', 'airs_users_feedback', '0', 1),
(24, 'PAGINA', 'page', 'airs_users_feedback', 'airs_users_feedback', '0', 2),
(25, 'COMMENTO', 'comment', 'airs_users_feedback', 'airs_users_feedback', '0', 3),
(26, 'DATA', 'data', 'airs_users_feedback', 'airs_users_feedback', '0', 4),
(27, 'ORA', 'ora', 'airs_users_feedback', 'airs_users_feedback', '0', 5);


