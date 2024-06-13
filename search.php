<?php
require_once("helpers.php");
$conn = mysqli_connect('localhost', 'root', '', 'Yeticave');
if (!$conn) {
  die('Ошибка: невозможно подключиться к базе данных: ' . mysqli_error());
}
session_start();
$user_name = '';
$no_result = '';
$lots = [];
//проверяем авторизован ли пользователь
$is_auth = get_auth_user($_SESSION['user_id'], $conn);

if ($is_auth === 1) {
    //получаем пользователя по id
    $user_name = get_user_name($_SESSION['user_id'], $conn);
}

//получаем все категории
$sql = "SELECT * FROM category";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (isset($_GET['find'])) {
        
    //переводим данные из строки поиска в строку и убираем лишние пробелы
    $search = trim(strval(filterXSS($_GET['search']))); 

    if ($search == '') {
        $no_result = "Ничего не найдено по вашему запросу";
    } else {
    // ищем лоты в таблице по строке
        $sql = "SELECT * FROM lots WHERE name LIKE '%$search%' || description LIKE '%$search%'";
        // $sql = "SELECT * FROM lots WHERE MATCH (name,description) AGAINST ('%$search%')";
        $result_search = mysqli_query($conn, $sql);
        $lots = mysqli_fetch_all($result_search, MYSQLI_ASSOC);

        //считаем найденные лоты
        if (count($lots) == 0) {
            $no_result = "Ничего не найдено по вашему запросу";
        }
    }
    
    $layout = include_template('search.php', [
        'is_auth' => $is_auth, 
        'user_name' => $user_name, 
        'lots' => $lots, 
        'no_result'=>$no_result,
        'category'=>$category,
        'search' => $search,
    ]);
}
print $layout;
?>