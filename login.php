<?php
require_once("helpers.php");
$conn = mysqli_connect('localhost', 'root', '', 'Yeticave');
if (!$conn) {
  die('Ошибка: невозможно подключиться к базе данных: ' . mysqli_error());
}

$user_name = null;
$title = 'Главная страница';

//проверяем авторизован ли пользователь
$is_auth = get_auth_user($_SESSION['user_id'], $conn);

if ($is_auth === 1) {
    $user_name = get_user_name($_SESSION['user_id'], $conn); //получаем имя пользователя по id
}

//получаем все категории
$sql = "SELECT * FROM category";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_all($result, MYSQLI_ASSOC);

$form_invalid = "";
$errors = [];
$user = null;

//получаем данные формы
if (isset($_POST['submit'])) {

  if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
    $email = $_POST['email'];
  } else {
    $errors['email']['class'] = 'form__item--invalid';
    $errors['email']['err_txt'] = 'Введите корректный email';
  }


  if (!isset($errors['email'])) {
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
    if (strlen($_POST['password']) < 3){
      $errors['password']['class'] = 'form__item--invalid';
      $errors['password']['err_txt'] = 'Введите пароль';
    }else if (!password_verify($_POST['password'], $user['password_hash'])) {
      $errors['password']['class'] = 'form__item--invalid';
      $errors['password']['err_txt'] = 'Вы ввели неверный пароль';
    }
    if (!$user) {
      $errors['email']['class'] = 'form__item--invalid';
      $errors['email']['err_txt'] = 'Пользователь с таким email не зарегистрирован';
    }
  }

  //проверка на ошибки при вводе данных входа на сайт
  if (count($errors) > 0) {
    $form_invalid = 'form--invalid';
    // var_dump($errors);
  } else {
    session_start(); //создаем сессию
    $_SESSION['user_id'] = $user['id'];
    header("Location: index.php");
  }
}

session_start();
if (isset($_SESSION['user_id'])) {
  $user_name = get_user_name($_SESSION['user_id'], $conn);
  $layout = include_template('403.php', ['user_name'=>$user_name, 'is_auth' => 1, 'title' => 'ДОСТУП ЗАПРЕЩЕН', 'category'=>$category]);
}else {
  $layout = include_template('login.php', ['form_invalid'=>$form_invalid, 'errors'=>$errors, 'category'=>$category]);
}
mysqli_close($conn);
print $layout;
?>

