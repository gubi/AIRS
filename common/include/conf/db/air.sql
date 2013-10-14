-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 14 ott, 2013 at 08:01 PM
-- Versione MySQL: 5.1.70
-- Versione PHP: 5.4.17-1~lucid+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `air`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `air_research`
--

CREATE TABLE IF NOT EXISTS `air_research` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `tags` varchar(500) NOT NULL,
  `search_engines` text NOT NULL,
  `query` varchar(500) NOT NULL,
  `languages` text NOT NULL,
  `filter_domain` text NOT NULL,
  `filter_filetype` text NOT NULL,
  `filter_region` text NOT NULL,
  `filter_date` date NOT NULL,
  `research_uris` varchar(500) NOT NULL,
  `last_insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user` varchar(125) NOT NULL,
  `group` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `air_research_results`
--

CREATE TABLE IF NOT EXISTS `air_research_results` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT 'ID::32',
  `research_id` mediumint(9) NOT NULL,
  `search_engine_id` mediumint(9) NOT NULL,
  `search_uri` varchar(500) NOT NULL,
  `search_total_results` varchar(500) NOT NULL COMMENT 'Risultati totali::50',
  `result_uri` varchar(500) NOT NULL COMMENT 'Indirizzo del risultato::150',
  `result_id` mediumint(9) NOT NULL,
  `result_page` mediumint(9) NOT NULL,
  `result_link_text` varchar(500) NOT NULL COMMENT 'Testo del collegamento::200',
  `result_description` text NOT NULL COMMENT 'Descrizione del collegamento::350',
  `result_content` text NOT NULL COMMENT 'Contenuto acquisito::400',
  `result_entire_html_content` text NOT NULL,
  `result_cache_uri` varchar(500) NOT NULL COMMENT 'Indirizzo della copia cache::150',
  `result_cache_html_content` text NOT NULL,
  `keywords` text NOT NULL,
  `tags` varchar(1000) NOT NULL,
  `words_count` int(11) NOT NULL,
  `user` varchar(16) NOT NULL COMMENT 'Utente::150',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data::130',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111628 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `air_search_engines`
--

CREATE TABLE IF NOT EXISTS `air_search_engines` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `search_page` varchar(125) DEFAULT NULL,
  `search_var` varchar(50) DEFAULT NULL COMMENT 'Variabile per la ricerca',
  `language_var` varchar(50) NOT NULL COMMENT 'Variabile per la lingua',
  `site_var` varchar(50) NOT NULL COMMENT 'Variabile per il filtro sul dominio',
  `filetype_var` varchar(50) NOT NULL COMMENT 'Variabile per il filtro su un file',
  `last_date_var` varchar(50) NOT NULL COMMENT 'Variabile per il filtro temporale',
  `last_date_val` varchar(100) NOT NULL COMMENT 'Valori per la variabile temporale',
  `country_var` varchar(50) NOT NULL COMMENT 'Variabile per il filtro geografico',
  `quoting_var` varchar(50) NOT NULL COMMENT 'Variabile per le citazioni',
  `or_var` varchar(50) NOT NULL COMMENT 'Variabile per l''OR',
  `exclusion_var` varchar(50) NOT NULL COMMENT 'Variabile per l''esclusione',
  `is_global` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Dump dei dati per la tabella `air_search_engines`
--

INSERT INTO `air_search_engines` (`id`, `name`, `description`, `search_page`, `search_var`, `language_var`, `site_var`, `filetype_var`, `last_date_var`, `last_date_val`, `country_var`, `quoting_var`, `or_var`, `exclusion_var`, `is_global`) VALUES
(1, 'Google', 'Il Motore di ricerca più diffuso al mondo', 'http://www.google.com/search', 'q=', 'hl=', 'site:', 'filetype:', 'qdr:', 'd,w,m,y', 'country:', '\\"{1}\\"', ' OR ', ' - ', '1'),
(2, 'Yahoo! Search - Ricerca nel Web | Motore di Ricerca', 'Il motore di ricerca ti aiuta a trovare esattamente ciò che stai cercando. Trova informazioni, video, immagini e risposte pertinenti in tutto il Web.', 'http://it.search.yahoo.com/search', 'p=', 'vl=', 'vst=', 'vf=', '', '', 'vc=', '\\"{1}\\"', '"', '"', '1');

-- --------------------------------------------------------


--
-- Struttura della tabella `air_users_search_engines`
--

CREATE TABLE IF NOT EXISTS `air_users_search_engines` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `se_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;


--
-- Dump dei dati per la tabella `__export_data_schema`
--

INSERT INTO `__export_data_schema` (`id`, `size`, `title`, `column`, `table`, `reference`, `has_more_results`, `order`) VALUES
(1, 0, 'ID', 'the_id', 'air_research_results', 'air_research_results', '0', 1),
(2, 0, 'RICERCA', 'trim($dato_test[''title'']) . '' ('' . trim($dato_test[''query'']) . '')''', 'air_research', 'air_research_results', '1', 2),
(3, 0, 'RISULTATI TOTALI', 'search_total_results', 'air_research_results', 'air_research_results', '0', 3),
(4, 0, 'POSIZIONE', '''Pagina '' . trim($dato_test[''result_page'']) . '', '' . trim($dato_test[''result_id'']) . '' risultato''', 'air_research_results', 'air_research_results', '1', 4),
(5, 0, 'URI', 'result_uri', 'air_research_results', 'air_research_results', '0', 5),
(6, 0, 'TITOLO', 'result_link_text', 'air_research_results', 'air_research_results', '0', 6),
(7, 0, 'DESCRIZIONE', 'result_description', 'air_research_results', 'air_research_results', '0', 7),
(8, 0, 'CONTENUTO', 'result_content', 'air_research_results', 'air_research_results', '0', 8),
(9, 0, 'TAGS', 'the_tags', 'air_research_results', 'air_research_results', '0', 9),
(10, 0, 'KEYWORDS', 'keywords', 'air_research_results', 'air_research_results', '0', 10),
(11, 0, 'PAROLE', 'words_count', 'air_research_results', 'air_research_results', '0', 11),
(12, 0, 'UTENTE', 'user', 'air_research_results', 'air_research_results', '0', 12),
(13, 0, 'DATA', 'date', 'air_research_results', 'air_research_results', '0', 13);

