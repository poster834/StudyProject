<?php
require_once("helpers.php");
$conn = mysqli_connect('localhost', 'root', '', 'Yeticave');
if (!$conn) {
  die('Ошибка: невозможно подключиться к базе данных: ' . mysqli_error());
}
session_start();
$user_name = '';

//проверяем авторизован ли пользователь
$is_auth = get_auth_user($_SESSION['user_id'], $conn);
$user_id = intval($_SESSION['user_id']);
$now = date('Y-m-d H:i:s');
$yesterday = date('Y-m-d', strtotime('yesterday'));

if ($is_auth === 1) {
    $user_name = get_user_name($_SESSION['user_id'], $conn); //получаем имя пользователя по id
}

$id = intval(filterXSS($_GET['id']));

//получаем лот по id
$sql = "SELECT *, lots.name as lotName, lots.id as lotID, lots.image as lotImage, category.name as catName 
FROM lots
LEFT JOIN category ON category.id = lots.category_id
WHERE lots.id =". $id;

$result = mysqli_query($conn, $sql);
$lot = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];

//получаем все категории
$sql = "SELECT * FROM category";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_all($result, MYSQLI_ASSOC);

//получаем все ставки по лоту
$sql = "SELECT *, users.name as user_name FROM bets 
LEFT JOIN users ON bets.user_id = users.id
WHERE lot_id = $id ORDER BY bets.date_set DESC";
$result = mysqli_query($conn, $sql);
$bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
$last_bet_user_id = $bets[0]['user_id'];

foreach ($bets as $key => $bet) {
  $date_set = date('Y-m-d', strtotime($bet['date_set']));
  $age = strtotime($now) - strtotime($bet['date_set']);
  if ($age < 60 * 60){
    $age_date = date('i', $age);
    $bets[$key]['age'] = $age_date." ".get_noun_plural_form ($age_date, 'минута', 'минуты', 'минут').' назад';
  }
  if ($date_set == date('Y-m-d') && $age >= 60 * 60) {
    $age_date = date('H', $age);
    $bets[$key]['age'] = $age_date." ".get_noun_plural_form ($age_date, 'час', 'часа', 'часов').' назад';
  }
  if ($date_set == $yesterday) {
    $bets[$key]['age'] = "вчера в ".date('H:i', strtotime($bet['date_set']));
  }
  if (date('Y-m-d', strtotime($bet['date_set'])) < $yesterday) {
    $bets[$key]['age'] = date('d.m.Y H:i', strtotime($bet['date_set']));
  }
}

mysqli_close($conn);

$title = "Лот: ".$lot['lotName'];
if (is_array($lot) && count($lot) > 0) {
  $layout = include_template('lot.php', [
    'lot'=>$lot, 
    'user_name'=>$user_name, 
    'is_auth'=>$is_auth, 
    'category'=>$category,
    'bets' => $bets,
    'title' => $title,
    'errors' => $errors,
    'user_id' => $user_id,
    'last_bet_user_id' => $last_bet_user_id,
    
  ]);
} else {
  $layout = include_template('404.php', ['user_name'=>$user_name, 'is_auth'=>$is_auth, 'category'=>$category]);
}



print $layout;
?>

