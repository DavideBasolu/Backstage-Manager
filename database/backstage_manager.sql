-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 01, 2026 alle 18:38
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `backstage_manager`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `album`
--

CREATE TABLE `album` (
  `id_album` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL,
  `titolo_album` varchar(150) NOT NULL,
  `data_uscita` date DEFAULT NULL,
  `tipo_album` varchar(30) DEFAULT NULL,
  `genere` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `stato_album` varchar(30) NOT NULL DEFAULT 'in lavorazione'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `album`
--

INSERT INTO `album` (`id_album`, `id_artista`, `titolo_album`, `data_uscita`, `tipo_album`, `genere`, `note`, `stato_album`) VALUES
(1, 4, 'L\'erba cattiva', '2012-01-01', 'album', 'rap', NULL, 'pubblicato'),
(2, 4, 'Mercurio', '2013-01-01', 'album', 'rap', NULL, 'pubblicato'),
(3, 4, 'Terza Stagione', '2016-01-01', 'album', 'rap', NULL, 'pubblicato'),
(4, 4, 'Supereroe', '2018-01-01', 'album', 'rap', NULL, 'pubblicato'),
(5, 4, 'Bat Edition', '2019-01-01', 'deluxe', 'rap', NULL, 'pubblicato'),
(6, 4, '17', '2020-01-01', 'album', 'rap', NULL, 'pubblicato'),
(7, 4, 'Effetto Notte', '2023-01-01', 'album', 'rap', NULL, 'pubblicato'),
(8, 4, 'Musica Triste', '2025-01-01', 'album', 'rap', NULL, 'pubblicato');

-- --------------------------------------------------------

--
-- Struttura della tabella `album_brani`
--

CREATE TABLE `album_brani` (
  `id_album` int(11) NOT NULL,
  `id_brano` int(11) NOT NULL,
  `numero_traccia` int(11) DEFAULT NULL,
  `disco` int(11) DEFAULT 1,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `album_brani`
--

INSERT INTO `album_brani` (`id_album`, `id_brano`, `numero_traccia`, `disco`, `note`) VALUES
(1, 9, 1, 1, NULL),
(1, 10, 2, 1, NULL),
(1, 11, 3, 1, NULL),
(2, 12, 1, 1, NULL),
(2, 13, 2, 1, NULL),
(2, 14, 3, 1, NULL),
(3, 15, 1, 1, NULL),
(3, 16, 2, 1, NULL),
(4, 17, 1, 1, NULL),
(4, 18, 2, 1, NULL),
(5, 19, 1, 1, NULL),
(6, 20, 1, 1, NULL),
(6, 21, 2, 1, NULL),
(7, 22, 1, 1, NULL),
(7, 23, 2, 1, NULL),
(8, 24, 1, 1, NULL),
(8, 25, 2, 1, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `artisti`
--

CREATE TABLE `artisti` (
  `id_artista` int(11) NOT NULL,
  `nome_arte` varchar(100) NOT NULL,
  `genere_musicale` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `citta` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `id_etichetta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `artisti`
--

INSERT INTO `artisti` (`id_artista`, `nome_arte`, `genere_musicale`, `email`, `telefono`, `citta`, `note`, `nome`, `cognome`, `id_etichetta`) VALUES
(4, 'emis killa', 'rap', 'ek@gmail.com', '666 666 666', 'Vimercate', NULL, 'emilano rudolf', 'Giambelli', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `brani`
--

CREATE TABLE `brani` (
  `id_brano` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL,
  `titolo` varchar(150) NOT NULL,
  `durata` time NOT NULL,
  `bpm` smallint(6) DEFAULT NULL,
  `tonalita` varchar(20) DEFAULT NULL,
  `anno_pubblicazione` int(11) DEFAULT NULL,
  `genere_brano` varchar(50) DEFAULT NULL,
  `testo` text DEFAULT NULL,
  `stato_brano` varchar(30) NOT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `brani`
--

INSERT INTO `brani` (`id_brano`, `id_artista`, `titolo`, `durata`, `bpm`, `tonalita`, `anno_pubblicazione`, `genere_brano`, `testo`, `stato_brano`, `note`) VALUES
(9, 4, 'Parole di ghiaccio', '00:03:30', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(10, 4, 'Cashwoman', '00:03:00', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(11, 4, 'Cocktailz', '00:03:20', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(12, 4, 'Wow', '00:03:10', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(13, 4, 'MB45', '00:03:05', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(14, 4, 'Benzina', '00:03:15', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(15, 4, 'Dal basso', '00:03:20', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(16, 4, 'Parigi', '00:03:25', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(17, 4, 'Rollercoaster', '00:03:30', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(18, 4, 'Supereroe', '00:03:40', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(19, 4, 'Tijuana', '00:03:10', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(20, 4, 'No Insta', '00:03:15', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(21, 4, 'Malandrino', '00:03:20', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(22, 4, 'Pacino', '00:03:10', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(23, 4, 'Maserati', '00:03:20', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(24, 4, 'Demoni', '00:03:00', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL),
(25, 4, 'Egoista', '00:03:10', NULL, NULL, NULL, 'rap', NULL, 'pubblicato', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `etichette`
--

CREATE TABLE `etichette` (
  `id_etichetta` int(11) NOT NULL,
  `nome_etichetta` varchar(100) NOT NULL,
  `referente` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sede` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `etichette`
--

INSERT INTO `etichette` (`id_etichetta`, `nome_etichetta`, `referente`, `telefono`, `email`, `sede`, `note`) VALUES
(1, 'universal', 'biagio', '000111222', 'universal@gmail.com', 'milano', NULL),
(2, 'island', 'cristian', '000 555 0555', 'island@gmail.com', 'calvairate', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `eventi`
--

CREATE TABLE `eventi` (
  `id_evento` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL,
  `nome_evento` varchar(100) NOT NULL,
  `data_evento` date NOT NULL,
  `luogo` varchar(100) NOT NULL,
  `cachet` decimal(10,2) DEFAULT NULL,
  `id_scaletta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `eventi_artisti`
--

CREATE TABLE `eventi_artisti` (
  `id_evento` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL,
  `ruolo_evento` varchar(30) DEFAULT 'ospite'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `feat_brani`
--

CREATE TABLE `feat_brani` (
  `id_brano` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `scaletta_brani`
--

CREATE TABLE `scaletta_brani` (
  `id_scaletta` int(11) NOT NULL,
  `id_brano` int(11) NOT NULL,
  `posizione` int(11) NOT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `scaletta_brani`
--

INSERT INTO `scaletta_brani` (`id_scaletta`, `id_brano`, `posizione`, `note`) VALUES
(4, 14, 3, ''),
(4, 17, 2, NULL),
(4, 20, 6, ''),
(4, 21, 5, NULL),
(4, 23, 1, ''),
(4, 24, 4, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `scalette`
--

CREATE TABLE `scalette` (
  `id_scaletta` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL,
  `nome_scaletta` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `durata_totale` time DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `scalette`
--

INSERT INTO `scalette` (`id_scaletta`, `id_artista`, `nome_scaletta`, `descrizione`, `durata_totale`, `note`) VALUES
(4, 4, 'club tour', NULL, '01:30:00', NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`id_album`),
  ADD KEY `id_artista` (`id_artista`);

--
-- Indici per le tabelle `album_brani`
--
ALTER TABLE `album_brani`
  ADD PRIMARY KEY (`id_album`,`id_brano`),
  ADD UNIQUE KEY `unique_traccia_album` (`id_album`,`disco`,`numero_traccia`),
  ADD KEY `id_brano` (`id_brano`);

--
-- Indici per le tabelle `artisti`
--
ALTER TABLE `artisti`
  ADD PRIMARY KEY (`id_artista`),
  ADD KEY `fk_artisti_etichette` (`id_etichetta`);

--
-- Indici per le tabelle `brani`
--
ALTER TABLE `brani`
  ADD PRIMARY KEY (`id_brano`),
  ADD KEY `id_artista` (`id_artista`);

--
-- Indici per le tabelle `etichette`
--
ALTER TABLE `etichette`
  ADD PRIMARY KEY (`id_etichetta`);

--
-- Indici per le tabelle `eventi`
--
ALTER TABLE `eventi`
  ADD PRIMARY KEY (`id_evento`),
  ADD KEY `id_artista` (`id_artista`),
  ADD KEY `fk_eventi_scaletta` (`id_scaletta`);

--
-- Indici per le tabelle `eventi_artisti`
--
ALTER TABLE `eventi_artisti`
  ADD PRIMARY KEY (`id_evento`,`id_artista`),
  ADD KEY `id_artista` (`id_artista`);

--
-- Indici per le tabelle `feat_brani`
--
ALTER TABLE `feat_brani`
  ADD PRIMARY KEY (`id_brano`,`id_artista`),
  ADD KEY `id_artista` (`id_artista`);

--
-- Indici per le tabelle `scaletta_brani`
--
ALTER TABLE `scaletta_brani`
  ADD PRIMARY KEY (`id_scaletta`,`id_brano`),
  ADD UNIQUE KEY `unique_posizione_scaletta` (`id_scaletta`,`posizione`),
  ADD KEY `id_brano` (`id_brano`);

--
-- Indici per le tabelle `scalette`
--
ALTER TABLE `scalette`
  ADD PRIMARY KEY (`id_scaletta`),
  ADD KEY `id_artista` (`id_artista`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `album`
--
ALTER TABLE `album`
  MODIFY `id_album` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `artisti`
--
ALTER TABLE `artisti`
  MODIFY `id_artista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `brani`
--
ALTER TABLE `brani`
  MODIFY `id_brano` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT per la tabella `etichette`
--
ALTER TABLE `etichette`
  MODIFY `id_etichetta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `eventi`
--
ALTER TABLE `eventi`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `scalette`
--
ALTER TABLE `scalette`
  MODIFY `id_scaletta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `album`
--
ALTER TABLE `album`
  ADD CONSTRAINT `album_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artisti` (`id_artista`);

--
-- Limiti per la tabella `album_brani`
--
ALTER TABLE `album_brani`
  ADD CONSTRAINT `album_brani_ibfk_1` FOREIGN KEY (`id_album`) REFERENCES `album` (`id_album`),
  ADD CONSTRAINT `album_brani_ibfk_2` FOREIGN KEY (`id_brano`) REFERENCES `brani` (`id_brano`);

--
-- Limiti per la tabella `artisti`
--
ALTER TABLE `artisti`
  ADD CONSTRAINT `fk_artisti_etichette` FOREIGN KEY (`id_etichetta`) REFERENCES `etichette` (`id_etichetta`);

--
-- Limiti per la tabella `brani`
--
ALTER TABLE `brani`
  ADD CONSTRAINT `brani_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artisti` (`id_artista`);

--
-- Limiti per la tabella `eventi`
--
ALTER TABLE `eventi`
  ADD CONSTRAINT `eventi_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artisti` (`id_artista`),
  ADD CONSTRAINT `fk_eventi_scaletta` FOREIGN KEY (`id_scaletta`) REFERENCES `scalette` (`id_scaletta`);

--
-- Limiti per la tabella `eventi_artisti`
--
ALTER TABLE `eventi_artisti`
  ADD CONSTRAINT `eventi_artisti_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventi` (`id_evento`),
  ADD CONSTRAINT `eventi_artisti_ibfk_2` FOREIGN KEY (`id_artista`) REFERENCES `artisti` (`id_artista`);

--
-- Limiti per la tabella `feat_brani`
--
ALTER TABLE `feat_brani`
  ADD CONSTRAINT `feat_brani_ibfk_1` FOREIGN KEY (`id_brano`) REFERENCES `brani` (`id_brano`),
  ADD CONSTRAINT `feat_brani_ibfk_2` FOREIGN KEY (`id_artista`) REFERENCES `artisti` (`id_artista`);

--
-- Limiti per la tabella `scaletta_brani`
--
ALTER TABLE `scaletta_brani`
  ADD CONSTRAINT `scaletta_brani_ibfk_1` FOREIGN KEY (`id_scaletta`) REFERENCES `scalette` (`id_scaletta`),
  ADD CONSTRAINT `scaletta_brani_ibfk_2` FOREIGN KEY (`id_brano`) REFERENCES `brani` (`id_brano`);

--
-- Limiti per la tabella `scalette`
--
ALTER TABLE `scalette`
  ADD CONSTRAINT `scalette_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artisti` (`id_artista`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
