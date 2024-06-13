<?php
require_once("helpers.php");
$conn = mysqli_connect('localhost', 'root', '', 'Yeticave');
if (!$conn) {
  die('Ошибка: невозможно подключиться к базе данных: ' . mysqli_error());
}

$user_name = null;
$title = 'Добавление нового лота';

session_start();

//проверяем авторизован ли пользователь
$is_auth = get_auth_user($_SESSION['user_id'], $conn);

if ($is_auth === 1) {
    $user_name = get_user_name($_SESSION['user_id'], $conn);
}
//получаем все категории
$sql = "SELECT * FROM category";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_all($result, MYSQLI_ASSOC);

$form_invalid = "";
$errors = [];
$today = date('Y-m-d');

//получаем данные формы
if (isset($_POST['submit'])) {

  if (strlen($_POST['lot-name']) > 3) {
    $lot_name = filterXSS($_POST['lot-name']);
    $errors['lot-name'] = '';
  } else {
    $errors['lot-name'] = 'form__item--invalid';
  }

  if ($_POST['category'] <> '0') {
    $lot_category = intval(filterXSS($_POST['category']));
    $errors['category'] = '';
  } else {
    $errors['category'] = 'form__item--invalid';
  }
  
  if (strlen($_POST['message']) > 3) {
    $lot_description = filterXSS($_POST['message']);
    $errors['message'] = '';
  } else {
    $errors['message'] = 'form__item--invalid';
  }

  if (intval($_POST['lot-rate']) > 0) {
    $lot_price = intval(filterXSS($_POST['lot-rate']));
    $errors['lot-rate'] = '';
  } else {
    $errors['lot-rate'] = 'form__item--invalid';
  }

  if (intval($_POST['lot-step']) > 0) {
    $lot_step = intval(filterXSS($_POST['lot-step']));
    $errors['lot-step'] = '';
  } else {
    $errors['lot-step'] = 'form__item--invalid';
  }

  if ( is_date_valid($_POST['lot-date']) && $today < date('Y-m-d', strtotime($_POST['lot-date'])) ) {
    $date_finish = filterXSS($_POST['lot-date']);
    $errors['lot-date'] = '';
  } else {
    $errors['lot-date'] = 'form__item--invalid';
  }
  
  //разрешенные типы файлов для загрузки
  $allowType = [
    'image/jpeg',
    'image/png',
    'image/jpg'
  ];
// проверяка mime type загруженного файла
  if (isset($_FILES['lot-img']['name']) && strlen($_FILES['lot-img']['name']) > 0) {
    if (in_array(mime_content_type($_FILES['lot-img']["tmp_name"]), $allowType)) {
      $tmp_name = $_FILES['lot-img']['tmp_name'];
      $file_name = $_FILES['lot-img']['name'];
      $lot_image = $file_name;
    }
  } else {
    $errors['lot-img'] = 'form__item--invalid';
    $errors['lot-img'] = '';
  }

  if ($err <> null) { //если есть ошибки при заполнения формы
    $form_invalid = 'form--invalid';
  } else {
    
    $sql = "INSERT INTO lots (`id`,`name`, `category_id`, `description`, `start_price`, `step`, `price`, `date_add`, `date_finish`, `image`, `user_id`) VALUES 
    (NULL, '$lot_name', $lot_category, '$lot_description', $lot_price, $lot_step, $lot_price, '$today', '$date_finish', '$lot_image', 1)";
    $result = mysqli_query($conn, $sql);
    $id = mysqli_insert_id($conn);

    if ($id > 0) {
      header("Location: lot.php?id=".$id);
    }
 
  }
}



mysqli_close($conn);

if ($is_auth > 0) {
  $layout = include_template('add.php', ['form_invalid'=>$form_invalid, 'errors'=>$errors, 'user_name'=>$user_name, 'is_auth'=>$is_auth, 'category'=>$category, 'title'=>$title]);
} else {
  $layout = include_template('403.php', ['user_name'=>$user_name, 'is_auth' => $is_auth, 'title' => 'ДОСТУП ЗАПРЕЩЕН', 'category'=>$category]);
}

print $layout;
?>

