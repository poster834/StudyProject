-- добавляем пользователей в БД
INSERT INTO users (`id`, `name`, `password_hash`, `email`, `contact`) VALUES 
(NULL, 'Администратор', '$2y$10$NgLYy8vutBji/ol.jlxPle0jnlYrTyWQicZmbUcHzHCs.WXMIZkjy', 'admin@mail.com', '9999999999'), -- пароль 123123
(NULL, 'Иванов И.И.', '$2y$10$NgLYy8vutBji/ol.jlxPle0jnlYrTyWQicZmbUcHzHCs.WXMIZkjy', 'user@mail.com', 'Москва, Арбат д.3'); -- пароль 123123

-- Добавляем категории в базу данных
INSERT INTO category (`id`,`name`, `type`, `image`, `description`) VALUES 
    (NULL, 'Доски и лыжи', 'boards', 'category-1.jpg', 'Описание категории Доски и лыжи'),
    (NULL, 'Крепления', 'attachment', 'category-2.jpg', 'Описание категории Крепления'),
    (NULL, 'Ботинки', 'boots', 'category-3.jpg', 'Описание категории Ботинки'),
    (NULL, 'Одежда', 'clothing', 'category-4.jpg', 'Описание категории Одежда'),
    (NULL, 'Инструменты', 'tools', 'category-5.jpg', 'Описание категории Инструменты'),
    (NULL, 'Разное', 'other', 'category-6.jpg', 'Описание категории Разное');


-- Добавляем лоты в базу данных
INSERT INTO lots (`id`,`name`, `description`, `category_id`, `start_price`, `step`, `price`, `date_add`, `date_finish`, `image`, `user_id`, `winner_id`) VALUES 
    (NULL, '2014 Rossignol District Snowboard', '2014 Rossignol District Snowboard', 1, 10999, 100, 10999, '2024-06-09', '2024-07-09', 'lot-1.jpg', 1, NULL),
    (NULL, 'DC Ply Mens 2016/2017 Snowboard', 'DC Ply Mens 2016/2017 Snowboard', 1, 159999, 50, 159999,'2024-06-09', '2024-07-09', 'lot-2.jpg', 1, NULL),
    (NULL, 'Крепления Union Contact Pro 2015 года размер L/XL', 'Крепления Union Contact Pro 2015 года размер L/XL', 2, 8000, 20, 8000, '2024-06-09', '2024-07-09', 'lot-3.jpg', 2, NULL),
    (NULL, 'Ботинки для сноуборда DC Mutiny Charocal', 'Ботинки для сноуборда DC Mutiny Charocal', 3, 10999, 70, 10999, '2024-06-09', '2024-07-09', 'lot-4.jpg', 2, NULL),
    (NULL, 'Куртка для сноуборда DC Mutiny Charocal', 'Куртка для сноуборда DC Mutiny Charocal', 4, 7500, 30, 7500, '2024-06-09', '2024-07-09', 'lot-5.jpg', 2, NULL),
    (NULL, 'Маска Oakley Canopy', 'Маска Oakley Canopy', 6, 5400, 50, 5400, '2024-06-09', '2024-07-09', 'lot-6.jpg', 1, NULL);


-- Добавляем ставки в базу данных
INSERT INTO bets (`id`,`user_id`, `lot_id`, `cost_bet`, `date_set`) VALUES 
    (NULL, 1, 3, 8000, '2024-06-09 12:00:00'),
    (NULL, 2, 3, 8020, '2024-06-09 13:00:00'),
    (NULL, 2, 1, 10999, '2024-06-09 12:00:00');


    -- ЗАПРОСЫ

-- получить все категории
SELECT * FROM `category`;

--  получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;
SELECT lots.name, lots.start_price, lots.image, category.name as catName FROM `lots` 
LEFT JOIN `category` ON lots.category_id = category.id WHERE DATE(lots.date_finish) > CURRENT_DATE();

-- показать лот по его ID. Получите также название категории, к которой принадлежит лот;
SELECT *, category.name as catName FROM `lots`
LEFT JOIN `category` ON lots.category_id = category.id WHERE lots.id = 1;

--  обновить название лота по его идентификатору;
UPDATE `lots` SET name='Rossignol District Snowboard' WHERE id = 1;

--  получить список ставок для лота по его идентификатору с сортировкой по дате.
SELECT * FROM `bets` WHERE lot_id = 1 ORDER BY date_set ASC;
