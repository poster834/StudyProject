<?php
require_once("helpers.php");
$conn = mysqli_connect('localhost', 'root', '', 'Yeticave');
if (!$conn) {
  die('Ошибка: невозможно подключиться к базе данных: ' . mysqli_error());
}

session_start();
$lot = [];
$cost = 0;
$errors = [];
$now = date("Y-m-d H:i:s");
$user_id = null;
$lot_id = null;

//получаем все категории
$sql = "SELECT * FROM category";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_all($result, MYSQLI_ASSOC);

//проверяем авторизован ли пользователь
$is_auth = get_auth_user($_SESSION['user_id'], $conn);

if ($is_auth === 1) {
    $user_name = get_user_name($_SESSION['user_id'], $conn);
    $user_id = $_SESSION['user_id'];
}

if ( isset($_POST['submit']) ) {
  $lot_id = intval( filterXSS( $_POST['lot-id'] ) );

    //получаем лот по id
    $sql = "SELECT * FROM lots WHERE id =". $lot_id;
    $result = mysqli_query($conn, $sql);
    $lot = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
    $price = $lot['price']; // Текущая цена лота с учетом ставок
    $step = $lot['step']; // шаг ставки

  $cost = intval(filterXSS($_POST['cost']));

  if ( $cost > 0 && $cost >= $price + $step ){
    $errors['cost'] = '';
    $sql = "UPDATE `lots` SET price = $cost WHERE id = $lot_id";
    $result = mysqli_query($conn, $sql);
    $sql = "INSERT INTO bets (`id`,`user_id`, `lot_id`, `cost_bet`, `date_set`) 
        VALUES (NULL, $user_id, $lot_id, $cost, '$now')";
    $result = mysqli_query($conn, $sql);
    header("Location: lot.php?id=$lot_id");
  } else {
    header("Location: lot.php?id=$lot_id");
    $errors['cost'] = 'form__item--invalid';
  }

}


?>