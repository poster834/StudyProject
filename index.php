<?php
require_once("helpers.php");
require_once("winners.php");
$conn = mysqli_connect('localhost', 'root', '', 'Yeticave');
if (!$conn) {
  die('Ошибка: невозможно подключиться к базе данных: ' . mysqli_error());
}
$user_name = null;
$title = 'Главная страница';

// session_start();

//проверяем авторизован ли пользователь
$is_auth = get_auth_user($_SESSION['user_id'], $conn);

if ($is_auth === 1) {
    $user_name = get_user_name($_SESSION['user_id'], $conn);
}

//получаем все активные на текущую дату лоты
$sql = "SELECT *, lots.name as name, lots.id as lot_id, lots.image as lot_img, category.name as category 
FROM lots 
LEFT JOIN category ON category.id = lots.category_id
WHERE DATE(date_finish) >= CURRENT_DATE() && lots.winner_id IS NULL ORDER BY lots.date_add DESC";
$result = mysqli_query($conn, $sql);
$lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

//получаем все категории
$sql = "SELECT * FROM category";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_close($conn);

$main = include_template('main.php', ['category'=>$category, 'lots'=>$lots]);
$layout = include_template('layout.php', ['main'=>$main, 'user_name'=>$user_name, 'title'=>$title, 'is_auth'=>$is_auth, 'category'=>$category]);
print $layout;
?>

