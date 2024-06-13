<?php
require_once("helpers.php");
$conn = mysqli_connect('localhost', 'root', '', 'Yeticave');
if (!$conn) {
  die('Ошибка: невозможно подключиться к базе данных: ' . mysqli_error());
}

session_start();

//получаем все категории
$sql = "SELECT * FROM category";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_all($result, MYSQLI_ASSOC);

$emailCheck = false;
$form_invalid = "";
$errors_sign = [];

//получаем данные формы
if (isset($_POST['submit'])) {

  if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email = $_POST['email'];
    $errors_sign['email'] = '';
    $sql = "SELECT email FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $emailUser = mysqli_fetch_all($result, MYSQLI_ASSOC)[0]['email'];
    if ($email === $emailUser){
      $emailCheck = true;
    }
  } else {
    $errors_sign['email'] = 'form__item--invalid';
  }
  
  if (strlen($_POST['password']) > 3) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $errors_sign['password'] = '';
  } else {
    $errors_sign['password'] = 'form__item--invalid';
  }

  if (strlen($_POST['name']) > 3) {
    $name = filterXSS($_POST['name']);
    $errors_sign['name'] = '';
  } else {
    $errors_sign['name'] = 'form__item--invalid';
  }

  if (strlen($_POST['message']) > 3) {
    $message = filterXSS($_POST['message']);
    $errors_sign['message'] = '';
  } else {
    $errors_sign['message'] = 'form__item--invalid';
  }

  if ($emailCheck === true || in_array('form__item--invalid', $errors_sign, true)) {
    $form_invalid = 'form--invalid';
    $errors_sign['email'] = 'form__item--invalid';
  } else {
    $sql = "INSERT INTO users (`id`, `name`, `password_hash`, `email`, `contact`) VALUES 
    (NULL, '$name', '$password', '$email', '$message')";
    if (mysqli_query($conn, $sql) == true) {
      header("Location: login.php");
    }
  }
}


if (isset($_SESSION['user_id'])) {
  $user_name = get_user_name($_SESSION['user_id'], $conn);
  $layout = include_template('403.php', ['user_name'=>$user_name, 'is_auth' => 1, 'title' => 'ДОСТУП ЗАПРЕЩЕН', 'category'=>$category]);
} else {
  $layout = include_template('sign-up.php', ['form_invalid'=>$form_invalid, 'errors'=>$errors_sign, 'category'=>$category]);
}

mysqli_close($conn);
print $layout;

?>

