<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**Функция форматирования цен 
* @param $price принимаем число для формативарония
*/
function priceFormatter($price)
{
    $cell_price = ceil($price);
    $format_price = number_format($cell_price, 0, '',' ');
    return $format_price;
}

/**
 * Функция для защиты от XSS перед записью в базу SQL
 * @param string $str принимаем строку для форматирования
 */
function filterXSS($str){
    $str = htmlspecialchars($str); //убираем HTML символы
    return $str;
}

/**
 * Функция вычисляет остаток часов и минут 
 * для определения окончания аукциона по ставке
 * @param string $date принимаем дату окончания аукциона лота
 * @param array $timeArray[чч, мм] выводим массив с часами и минутами
 */
function get_dt_range($date)
{
    if (is_date_valid($date)) {
        $now = date('Y-m-d H:i:s');
        $date = $date.'23:59:59';
        $diff = strtotime($date) - strtotime($now);
        $hours = floor($diff / 3600);
        $minutes = floor(($diff - $hours * 3600) / 60);
        $hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);
        $timeArray = ['hours'=>$hours, 'minutes'=>$minutes];
        return $timeArray;
    }
    return false;
}

/**
 * Функция проверяем авторизован пользователь или нет
 * @param int $user_id id пользователя для проверки
 * @param array $conn массив с параметрами подключения к базе данных
 */
function get_auth_user($user_id, $conn)
{
    if ($user_id <> null) {
        $sql = "SELECT * FROM users WHERE id = $user_id";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if($user['id'] * 1 > 0){
            return 1; //возвращаем 1 если $user_id присутствует в таблице users
        }
    }
    return false;
}

/**
 * получаем имя пользователя по его id 
 * @param int $user_id id пользователя для проверки
 * @param array $conn массив с параметрами подключения к базе данных
 */
function get_user_name($user_id, $conn)
{
        $sql = "SELECT * FROM users WHERE id=".$user_id;
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
        return $user['name']; //возвращаем имя пользователя 
}