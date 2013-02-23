-- DIESE DATEI NICHT ÄNDERN! NACH DER INSTALLATION >KANN< DIESE DATEI ENTFERNT WERDEN...
--
-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 08. Mai 2011 um 14:15
-- Server Version: 5.0.38
-- PHP-Version: 5.2.1
-- 
-- Datenbank: `obst`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `xdb_reports`
-- 

ALTER TABLE `xdb_reports` 
ADD `dot` varchar(10) NOT NULL default 'grey',
ADD `no_information` tinyint(1) NOT NULL default '0'


