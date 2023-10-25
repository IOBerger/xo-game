-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 25 2023 г., 13:53
-- Версия сервера: 10.5.11-MariaDB
-- Версия PHP: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `valhalla`
--

-- --------------------------------------------------------

--
-- Структура таблицы `game`
--

CREATE TABLE `game` (
  `id` int(11) NOT NULL,
  `username1` varchar(27) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username2` varchar(27) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell11` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell12` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell13` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell21` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell22` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell23` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell31` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell32` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell33` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `win` int(1) DEFAULT NULL,
  `last_update` int(11) NOT NULL,
  `next_step_user` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `game`
--

INSERT INTO `game` (`id`, `username1`, `username2`, `cell11`, `cell12`, `cell13`, `cell21`, `cell22`, `cell23`, `cell31`, `cell32`, `cell33`, `win`, `last_update`, `next_step_user`) VALUES
(1, '111', '222', 'x', 'x', 'o', 'x', 'o', NULL, 'o', NULL, NULL, 2, 1698221985, 2),
(2, '111', '222', 'x', NULL, NULL, 'x', 'o', 'o', 'x', NULL, NULL, 1, 1698223036, 1),
(3, '222', '111', 'x', 'x', 'x', 'o', NULL, NULL, NULL, 'o', NULL, 1, 1698223460, 1),
(4, '333', '222', NULL, NULL, NULL, 'x', 'x', 'x', 'o', 'o', NULL, 1, 1698225369, 1),
(5, '444', '555', NULL, 'x', 'o', NULL, 'x', 'o', NULL, 'x', NULL, 1, 1698225913, 1),
(6, '555', '444', 'x', 'o', 'o', NULL, 'x', NULL, NULL, NULL, 'x', 1, 1698225981, 1),
(104, 'lll', 'eee', 'o', 'o', 'x', NULL, NULL, 'x', NULL, NULL, 'x', 1, 1698230943, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `game`
--
ALTER TABLE `game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
