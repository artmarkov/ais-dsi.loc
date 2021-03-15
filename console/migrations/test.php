-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 15 2021 г., 17:26
-- Версия сервера: 8.0.15
-- Версия PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- База данных: `yii2artschool`
--

-- --------------------------------------------------------

--
-- Структура таблицы `address`
--

CREATE TABLE `address` (
`id` int(11) NOT NULL,
`customer_id` int(11) NOT NULL,
`full_name` varchar(128) NOT NULL,
`address_line1` varchar(256) NOT NULL,
`address_line2` varchar(256) NOT NULL,
`city` varchar(64) NOT NULL,
`state` varchar(64) NOT NULL,
`postal_code` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `address`
--

INSERT INTO `address` (`id`, `customer_id`, `full_name`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`) VALUES
(1, 1, 'Марков Артур Владимирович', 'Красногорск', 'Москва', 'Город', 'Россия', '125452'),
(9, 3, 'Марков Артур Владимирович', 'Красногорск', 'Москва', 'Город', 'Россия', '125452'),
(11, 4, 'Марков Артур Владимирович', 'Красногорск', 'Москва', 'Город', 'Россия', '125452');

-- --------------------------------------------------------

--
-- Структура таблицы `customer`
--

CREATE TABLE `customer` (
`id` int(11) NOT NULL,
`first_name` varchar(64) NOT NULL,
`last_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `customer`
--

INSERT INTO `customer` (`id`, `first_name`, `last_name`) VALUES
(1, 'Марков', 'Владимирович'),
(3, 'Марков', 'Владимирович'),
(4, 'Марков', 'Владимирович');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `address`
--
ALTER TABLE `address`
ADD PRIMARY KEY (`id`),
ADD KEY `address_ibfk_1` (`customer_id`);

--
-- Индексы таблицы `customer`
--
ALTER TABLE `customer`
ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `address`
--
ALTER TABLE `address`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `customer`
--
ALTER TABLE `customer`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `address`
--
ALTER TABLE `address`
ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
