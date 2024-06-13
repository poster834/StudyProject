<?php
error_reporting(E_ALL);
require_once("helpers.php");
$conn = mysqli_connect('localhost', 'root', '', 'Yeticave');
if (!$conn) {
  die('Ошибка: невозможно подключиться к базе данных: ' . mysqli_error());
}
$title = 'Мои ставки';
session_start();
$lot = [];
$user_id = intval($_SESSION['user_id']);

$cost = 0;
$errors = [];
$now = date("Y-m-d H:i:s");
$yesterday = date('Y-m-d', strtotime('yesterday'));

//получаем все категории
$sql = "SELECT * FROM category";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_all($result, MYSQLI_ASSOC);

//проверяем авторизован ли пользователь
$is_auth = get_auth_user($_SESSION['user_id'], $conn);

if ($is_auth === 1) {
    $user_name = get_user_name($_SESSION['user_id'], $conn);
}

//получаем мои ставки
$sql = "SELECT bets.date_set as date_set,
bets.cost_bet as cost_bet,
lots.image as lot_image,
lots.name as lot_name,
lots.id as lot_id,
lots.winner_id as winner_id,
lots.date_finish as date_finish,
category.name as category_name, 
users.name as user_name,
users.contact as user_contact
FROM bets 
LEFT JOIN lots ON lots.id = lot_id 
LEFT JOIN category ON category.id = lots.category_id 
LEFT JOIN users ON users.id = lots.user_id
WHERE bets.user_id = $user_id ORDER BY bets.date_set DESC";
$result = mysqli_query($conn, $sql);
$bets = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($bets as $key => $bet) {
  $bets[$key]['user_id'] = $user_id;
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
  if ($bets[$key]['winner_id'] == NULL) {
    $bets[$key]['rates'] = 'active';
  } else {
    if ($user_id == $bets[$key]['winner_id']){
      $bets[$key]['rates'] = 'winner';
    } else {
      $bets[$key]['rates'] = 'closed';
    }
  }
  

}

  $layout = include_template('my-bets.php', [
    'lot'=>$lot, 
    'user_name'=>$user_name, 
    'is_auth'=>$is_auth, 
    'category'=>$category,
    'bets' => $bets,
    'title' => $title,
  ]);

print $layout;


?>