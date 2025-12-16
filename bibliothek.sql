-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 12. Dez 2025 um 09:16
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `bibliothek`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ausleihen`
--

CREATE TABLE `ausleihen` (
  `ausleihen_id` int(11) NOT NULL,
  `nutzer_id` int(11) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `bibliothekar_id` int(11) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `rueckgabedatum` date DEFAULT NULL,
  `leihstatus` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `ausleihen`
--

INSERT INTO `ausleihen` (`ausleihen_id`, `nutzer_id`, `isbn`, `bibliothekar_id`, `datum`, `rueckgabedatum`, `leihstatus`) VALUES
(1, 1, '978-3-551-35400-5', 1, '2025-12-01', '2025-12-15', 'ausgeliehen'),
(2, 2, '978-3-518-42902-7', 1, '2025-11-20', '2025-12-05', 'zurückgegeben');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bibliothekar`
--

CREATE TABLE `bibliothekar` (
  `bibliothekar_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `vname` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `passwort` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `bibliothekar`
--

INSERT INTO `bibliothekar` (`bibliothekar_id`, `name`, `vname`, `email`, `passwort`) VALUES
(1, 'Daill', 'Samuel', 'samuel.daill@bibliothek.at', 'afd6e1eedc71825036375435958e58168ea67805eab3175276b7d54c7e43a12d');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buch`
--

CREATE TABLE `buch` (
  `isbn` varchar(20) NOT NULL,
  `titel` varchar(255) DEFAULT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `verlag` varchar(255) DEFAULT NULL,
  `kategorien` varchar(255) DEFAULT NULL,
  `preis` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `buch`
--

INSERT INTO `buch` (`isbn`, `titel`, `autor`, `verlag`, `kategorien`, `preis`, `status`) VALUES
('978-3-16-148410-0', 'Der Schatten des Windes', 'Carlos Ruiz Zafón', 'Fischer Verlag', 'Roman, Thriller', 14.99, 'verfügbar'),
('978-3-423-21645-2', 'Die Physiker', 'Friedrich Dürrenmatt', 'Diogenes Verlag', 'Drama, Klassiker', 9.95, 'verfügbar'),
('978-3-442-47172-3', 'Harry Potter und der Stein der Weisen', 'J.K. Rowling', 'Carlsen Verlag', 'Fantasy, Jugendbuch', 16.99, 'reserviert'),
('978-3-499-26744-0', 'Der Alchimist', 'Paulo Coelho', 'Ullstein Verlag', 'Roman, Philosophie', 12.50, 'ausgeliehen'),
('978-3-518-42902-7', 'Die Verwandlung', 'Franz Kafka', 'Suhrkamp Verlag', 'Novelle, Klassiker', 9.50, 'ausgeliehen'),
('978-3-518-45623-1', 'Eine kurze Geschichte der Menschheit', 'Yuval Noah Harari', 'C.H. Beck', 'Sachbuch, Geschichte', 19.90, 'verfügbar'),
('978-3-551-35400-5', 'Harry Potter und der Stein der Weisen', 'J.K. Rowling', 'Carlsen Verlag', 'Fantasy, Jugendbuch', 19.99, 'verfügbar');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nutzer`
--

CREATE TABLE `nutzer` (
  `nutzer_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `vname` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `nutzer`
--

INSERT INTO `nutzer` (`nutzer_id`, `name`, `vname`, `email`) VALUES
(1, 'Fiala', 'Valentin', 'valentin.fiala@tfs-haslach.at'),
(2, 'Kronenberger', 'Jonas', 'jonas.kronenberger@tfs-haslach.at');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `ausleihen`
--
ALTER TABLE `ausleihen`
  ADD PRIMARY KEY (`ausleihen_id`),
  ADD KEY `nutzer_id` (`nutzer_id`),
  ADD KEY `isbn` (`isbn`),
  ADD KEY `bibliothekar_id` (`bibliothekar_id`);

--
-- Indizes für die Tabelle `bibliothekar`
--
ALTER TABLE `bibliothekar`
  ADD PRIMARY KEY (`bibliothekar_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `buch`
--
ALTER TABLE `buch`
  ADD PRIMARY KEY (`isbn`);

--
-- Indizes für die Tabelle `nutzer`
--
ALTER TABLE `nutzer`
  ADD PRIMARY KEY (`nutzer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `ausleihen`
--
ALTER TABLE `ausleihen`
  ADD CONSTRAINT `ausleihen_ibfk_1` FOREIGN KEY (`nutzer_id`) REFERENCES `nutzer` (`nutzer_id`),
  ADD CONSTRAINT `ausleihen_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `buch` (`isbn`),
  ADD CONSTRAINT `ausleihen_ibfk_3` FOREIGN KEY (`bibliothekar_id`) REFERENCES `bibliothekar` (`bibliothekar_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
