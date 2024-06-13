<?php
require_once("helpers.php");
$conn = mysqli_connect('localhost', 'root', '', 'Yeticave');
if (!$conn) {
  die('Ошибка: невозможно подключиться к базе данных: ' . mysqli_error());
}

session_start();

//проверяем авторизован ли пользователь
$is_auth = get_auth_user($_SESSION['user_id'], $conn);

if ($is_auth === 1) {
  $user_name = get_user_name($_SESSION['user_id'], $conn);
  $user_id = $_SESSION['user_id'];
}



//получаем лоты
$sql = "SELECT * FROM lots WHERE DATE(date_finish) <= CURRENT_DATE() && winner_id IS NULL";
$result = mysqli_query($conn, $sql);
$lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($lots as $lot)
{

  $lot_id = intval($lot['id']);
  $sql = "SELECT * FROM bets WHERE bets.lot_id = $lot_id ORDER BY date_set DESC LIMIT 1";
  $result = mysqli_query($conn, $sql);
  $bet = mysqli_fetch_all($result, MYSQLI_ASSOC);
  if (count($bet) > 0) { // если есть ставки по данному лоту
    $winner_id = intval($bet[0]['user_id']);

    $sql = "UPDATE `lots` SET winner_id = $winner_id WHERE id = $lot_id";
    $result = mysqli_query($conn, $sql);

    //отправляем письмо победителю
  }




}
mysqli_close($conn);

?>

