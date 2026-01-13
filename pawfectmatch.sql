-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 09. Jan 2026 um 17:42
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `pawfectmatch`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adoption_history`
--

CREATE TABLE `adoption_history` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `decided_at` datetime NOT NULL,
  `note` longtext DEFAULT NULL,
  `adoption_request_id` int(11) NOT NULL,
  `decided_by_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adoption_request`
--

CREATE TABLE `adoption_request` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `username` varchar(50) NOT NULL,
  `survey_answer` longtext DEFAULT NULL,
  `pet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260109155844', '2026-01-09 15:58:55', 167);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `species` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `adoption_requirements` longtext DEFAULT NULL,
  `shelter_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `adoption_history`
--
ALTER TABLE `adoption_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5D25693AECFD9D75` (`adoption_request_id`),
  ADD KEY `IDX_5D25693AE26B496B` (`decided_by_id`);

--
-- Indizes für die Tabelle `adoption_request`
--
ALTER TABLE `adoption_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_410896EE966F7FB6` (`pet_id`);

--
-- Indizes für die Tabelle `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indizes für die Tabelle `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8638EA3F54053EC0` (`shelter_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_USERNAME` (`username`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `adoption_history`
--
ALTER TABLE `adoption_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adoption_request`
--
ALTER TABLE `adoption_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `adoption_history`
--
ALTER TABLE `adoption_history`
  ADD CONSTRAINT `FK_5D25693AE26B496B` FOREIGN KEY (`decided_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_5D25693AECFD9D75` FOREIGN KEY (`adoption_request_id`) REFERENCES `adoption_request` (`id`);

--
-- Constraints der Tabelle `adoption_request`
--
ALTER TABLE `adoption_request`
  ADD CONSTRAINT `FK_410896EE966F7FB6` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`);

--
-- Constraints der Tabelle `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `FK_8638EA3F54053EC0` FOREIGN KEY (`shelter_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
