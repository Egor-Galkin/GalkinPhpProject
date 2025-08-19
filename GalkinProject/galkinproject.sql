-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Авг 19 2025 г., 13:21
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `galkinproject`
--

-- --------------------------------------------------------

--
-- Структура таблицы `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `contacts`
--

INSERT INTO `contacts` (`id`, `first_name`, `last_name`) VALUES
(1, 'Василий', 'Иванов'),
(2, 'Иван', 'Петров'),
(3, 'Наталья', 'Сидорова'),
(9, 'Мария', 'Смирнова'),
(10, 'Алексей', 'Иванов'),
(11, 'Ольга', 'Кузнецова'),
(12, 'Дмитрий', 'Попов'),
(13, 'Елена', 'Васильева'),
(14, 'Сергей', 'Новиков'),
(15, 'Наталья', 'Морозова'),
(16, 'Михаил', 'Волков'),
(17, 'Татьяна', 'Лебедева');

-- --------------------------------------------------------

--
-- Структура таблицы `deals`
--

CREATE TABLE `deals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `deals`
--

INSERT INTO `deals` (`id`, `name`, `amount`) VALUES
(1, 'Продажа настольной лампы', 2500.00),
(14, 'Заказ офисной мебели', 15000.00),
(15, 'Купля-продажа светильников', 8000.00),
(16, 'Поставка компьютерной техники', 67000.00),
(17, 'Обслуживание оборудования', 12000.00),
(18, 'Продажа кондиционеров', 23000.00),
(19, 'Аренда конференц-зала', 5000.00),
(20, 'Ремонт офисной техники', 4500.00),
(21, 'Закупка канцелярии', 3000.00),
(22, 'Поставка серверного оборудования', 45000.00);

-- --------------------------------------------------------

--
-- Структура таблицы `deal_contact`
--

CREATE TABLE `deal_contact` (
  `deal_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `deal_contact`
--

INSERT INTO `deal_contact` (`deal_id`, `contact_id`) VALUES
(1, 1),
(1, 11),
(14, 2),
(14, 12),
(15, 2),
(16, 16),
(17, 9),
(18, 3),
(18, 14),
(19, 1),
(19, 10),
(20, 17),
(21, 1),
(21, 13),
(22, 15);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `deals`
--
ALTER TABLE `deals`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `deal_contact`
--
ALTER TABLE `deal_contact`
  ADD PRIMARY KEY (`deal_id`,`contact_id`),
  ADD KEY `contact_id` (`contact_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `deals`
--
ALTER TABLE `deals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `deal_contact`
--
ALTER TABLE `deal_contact`
  ADD CONSTRAINT `deal_contact_ibfk_1` FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deal_contact_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
