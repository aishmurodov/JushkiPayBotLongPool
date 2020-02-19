-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 17 2020 г., 12:35
-- Версия сервера: 5.5.63-MariaDB
-- Версия PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `junkipay`
--

-- --------------------------------------------------------

--
-- Структура таблицы `system`
--

CREATE TABLE `system` (
  `name` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `system`
--

INSERT INTO `system` (`name`, `val`) VALUES
('cny_to_uah', '3.32'),
('reserve', '200000'),
('usd_cny', '6.98');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `tg_id` int(11) NOT NULL,
  `root` int(11) NOT NULL,
  `on_menu` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requisites` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `tg_id`, `root`, `on_menu`, `balance`, `payment`, `requisites`, `created_at`) VALUES
(1, 354194826, 0, 'main', '0', '234', '23', '2020-02-17 08:35:32'),
(2, 192851541, 0, 'main', '0', '', '', '2020-02-17 09:07:25');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`name`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
