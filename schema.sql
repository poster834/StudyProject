-- создаем базу данных Yeticave
CREATE DATABASE Yeticave;
USE Yeticave;

-- создаем таблицу `lots` для хранения лотов
CREATE TABLE `lots` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` TEXT NOT NULL,
  `category_id` int NOT NULL,
  `start_price` int NOT NULL,
  `step` int NOT NULL,
  `price` int NOT NULL,
  `date_add` date NOT NULL,
  `date_finish` date NOT NULL,
  `image` varchar(50) NOT NULL,
  `user_id` int NOT NULL,
  `winner_id` int NULL
);


-- создаем таблицу `category` для хранения категорий лотов
CREATE TABLE `category` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `description` TEXT NOT NULL
);

-- создаем таблицу `users` для хранения пользователей
CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` varchar(255) NOT NULL
);

-- создаем таблицу `bets` для хранения ставок пользователей
CREATE TABLE `bets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `lot_id` int NOT NULL,
  `cost_bet` int NOT NULL,
  `date_set` timestamp NOT NULL
);

-- для полнотекстового поиска
ALTER TABLE `lots` ENGINE = 'MYISAM';
ALTER TABLE `category` ENGINE = 'MYISAM';
ALTER TABLE `users` ENGINE = 'MYISAM';
ALTER TABLE `bets` ENGINE = 'MYISAM';

-- добавляем первичные ключи для таблиц
ALTER TABLE `lots`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bets`
  ADD PRIMARY KEY (`id`);


-- добавляем AUTO_INCREMENT первичным ключам
ALTER TABLE `lots`
  MODIFY `id` int NOT NULL UNIQUE AUTO_INCREMENT;

ALTER TABLE `category`
  MODIFY `id` int NOT NULL UNIQUE AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int NOT NULL UNIQUE AUTO_INCREMENT;

ALTER TABLE `bets`
  MODIFY `id` int NOT NULL UNIQUE AUTO_INCREMENT;

  --  создание полнотекстового индекса для полей 
ALTER TABLE `lots` ADD FULLTEXT(`name`);
ALTER TABLE `lots` ADD FULLTEXT(`description`);

-- добавляем внешние ключи для таблиц
ALTER TABLE lots ADD FOREIGN KEY (category_id) REFERENCES category(id);
ALTER TABLE lots ADD FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE bets ADD FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE bets ADD FOREIGN KEY (lot_id) REFERENCES lots(id);