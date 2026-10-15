-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Jún 01. 23:51
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `higherorlowergame`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` int(11) NOT NULL,
  `user_id` char(36) NOT NULL,
  `score` int(11) NOT NULL,
  `mode` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `leaderboard`
--

INSERT INTO `leaderboard` (`id`, `user_id`, `score`, `mode`) VALUES
(1, '86a934f8-315d-4145-834e-8088e03bc894', 15, 'spotify'),
(2, '86a934f8-315d-4145-834e-8088e03bc894', 11, 'spotify'),
(3, '86a934f8-315d-4145-834e-8088e03bc894', 23, 'spotify'),
(4, '86a934f8-315d-4145-834e-8088e03bc894', 21, 'spotify'),
(5, '86a934f8-315d-4145-834e-8088e03bc894', 34, 'spotify'),
(6, '86a934f8-315d-4145-834e-8088e03bc894', 6, 'spotify'),
(7, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 0, 'spotify'),
(8, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 4, 'spotify'),
(9, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 0, 'spotify'),
(10, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 6, 'spotify'),
(11, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 5, 'spotify'),
(12, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 12, 'spotify'),
(13, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 24, 'spotify'),
(14, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 1, 'spotify'),
(15, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 10, 'spotify'),
(16, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 7, 'spotify'),
(17, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 39, 'spotify'),
(18, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 21, 'spotify'),
(19, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 3, 'spotify'),
(20, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 9, 'spotify'),
(21, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 37, 'spotify'),
(22, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 17, 'spotify'),
(23, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 37, 'spotify'),
(24, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 3, 'spotify'),
(25, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 80, 'spotify'),
(26, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 2, 'spotify'),
(27, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 1, 'spotify'),
(28, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 1, 'spotify'),
(29, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 6, 'spotify'),
(30, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 2, 'spotify'),
(31, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 16, 'countries'),
(32, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 0, 'spotify'),
(33, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 7, 'spotify'),
(34, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 25, 'spotify'),
(35, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 7, 'countries'),
(36, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 1, 'countries');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` char(36) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `country` varchar(2) NOT NULL,
  `password` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `user_id`, `username`, `email`, `country`, `password`, `date`) VALUES
(1, '86a934f8-315d-4145-834e-8088e03bc894', 'tesztelek', 'tesztelek@teszt.com', 'DE', 'teszt', '2025-05-25 20:29:23'),
(12, 'ecdb027d-fdc0-464d-a1c7-5ea7b32965f3', 'kabbekocsog', 'kabbekocsog@futyi.com', 'FR', 'kabbe', '2025-05-31 13:14:16'),
(13, '718a3a6b-f3fb-4217-8e05-feaefc7174b8', 'apadvagyok', 'apadvok@vagyok.com', 'MX', 'apad', '2025-05-31 13:02:56');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `date` (`date`),
  ADD KEY `user_name` (`username`),
  ADD KEY `email` (`email`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
