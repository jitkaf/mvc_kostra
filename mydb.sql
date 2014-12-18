-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Čtv 20. lis 2014, 23:03
-- Verze serveru: 5.6.20
-- Verze PHP: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `mydb`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `akvariste`
--

CREATE TABLE IF NOT EXISTS `akvariste` (
`id_akvariste` int(11) NOT NULL,
  `nick` varchar(45) NOT NULL,
  `datum_registrace` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `popis` text,
  `heslo` varchar(300) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Vypisuji data pro tabulku `akvariste`
--

INSERT INTO `akvariste` (`id_akvariste`, `nick`, `datum_registrace`, `popis`, `heslo`) VALUES
(1, 'progres', '2014-11-20 05:12:19', 'super nejlepsi kamarad', 'abc123'),
(2, 'sheep', '2014-11-20 05:12:55', 'super dfslepsi kamarad', 'bfd231'),
(3, 'blb', '2014-05-20 05:12:55', 'hablpafd', '95648'),
(4, 'idiot', '2014-11-10 00:00:00', 'fsadf dsf adsf ', 'fadsfds'),
(5, 'abdsf ', '0000-00-00 00:00:00', '123456', 'd93a5def7511da3d0f2d171d9c344e91'),
(6, 'petr', '0000-00-00 00:00:00', 'fsdafdsfq', '6116afedcb0bc31083935c1c262ff4c9'),
(9, 'progresek', '0000-00-00 00:00:00', '123456', 'd93a5def7511da3d0f2d171d9c344e91'),
(10, 'anezka', '0000-00-00 00:00:00', 'fsdgdfsg', 'd93a5def7511da3d0f2d171d9c344e91'),
(11, '', '0000-00-00 00:00:00', '', '0144712dd81be0c3d9724f5e56ce6685');

-- --------------------------------------------------------

--
-- Struktura tabulky `chova`
--

CREATE TABLE IF NOT EXISTS `chova` (
  `akvariste_id_akvariste` int(11) NOT NULL,
  `ryba_id_ryba` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `komentar`
--

CREATE TABLE IF NOT EXISTS `komentar` (
  `id_komentar` int(11) NOT NULL,
  `text` text NOT NULL,
  `prispevek_id_prispevek` int(11) NOT NULL,
  `akvariste_id_akvariste` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `prispevek`
--

CREATE TABLE IF NOT EXISTS `prispevek` (
  `id_prispevek` int(11) NOT NULL,
  `text` text NOT NULL,
  `akvariste_id_akvariste` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `ryba`
--

CREATE TABLE IF NOT EXISTS `ryba` (
  `id_ryba` int(11) NOT NULL,
  `jmeno` varchar(45) NOT NULL,
  `kmen` varchar(45) NOT NULL,
  `trida` varchar(45) NOT NULL,
  `rad` varchar(45) NOT NULL,
  `celed` varchar(45) NOT NULL,
  `podceled` varchar(45) NOT NULL,
  `rod` varchar(45) NOT NULL,
  `popis` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `akvariste`
--
ALTER TABLE `akvariste`
 ADD PRIMARY KEY (`id_akvariste`), ADD UNIQUE KEY `nick_UNIQUE` (`nick`);

--
-- Klíče pro tabulku `chova`
--
ALTER TABLE `chova`
 ADD PRIMARY KEY (`akvariste_id_akvariste`,`ryba_id_ryba`), ADD KEY `fk_akvariste_has_ryba_ryba1_idx` (`ryba_id_ryba`), ADD KEY `fk_akvariste_has_ryba_akvariste_idx` (`akvariste_id_akvariste`);

--
-- Klíče pro tabulku `komentar`
--
ALTER TABLE `komentar`
 ADD PRIMARY KEY (`id_komentar`), ADD KEY `fk_komentar_prispevek1_idx` (`prispevek_id_prispevek`), ADD KEY `fk_komentar_akvariste1_idx` (`akvariste_id_akvariste`);

--
-- Klíče pro tabulku `prispevek`
--
ALTER TABLE `prispevek`
 ADD PRIMARY KEY (`id_prispevek`), ADD KEY `fk_prispevek_akvariste1_idx` (`akvariste_id_akvariste`);

--
-- Klíče pro tabulku `ryba`
--
ALTER TABLE `ryba`
 ADD PRIMARY KEY (`id_ryba`), ADD UNIQUE KEY `jmeno_UNIQUE` (`jmeno`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `akvariste`
--
ALTER TABLE `akvariste`
MODIFY `id_akvariste` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `chova`
--
ALTER TABLE `chova`
ADD CONSTRAINT `fk_akvariste_has_ryba_akvariste` FOREIGN KEY (`akvariste_id_akvariste`) REFERENCES `akvariste` (`id_akvariste`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_akvariste_has_ryba_ryba1` FOREIGN KEY (`ryba_id_ryba`) REFERENCES `ryba` (`id_ryba`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `komentar`
--
ALTER TABLE `komentar`
ADD CONSTRAINT `fk_komentar_akvariste1` FOREIGN KEY (`akvariste_id_akvariste`) REFERENCES `akvariste` (`id_akvariste`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_komentar_prispevek1` FOREIGN KEY (`prispevek_id_prispevek`) REFERENCES `prispevek` (`id_prispevek`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `prispevek`
--
ALTER TABLE `prispevek`
ADD CONSTRAINT `fk_prispevek_akvariste1` FOREIGN KEY (`akvariste_id_akvariste`) REFERENCES `akvariste` (`id_akvariste`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
