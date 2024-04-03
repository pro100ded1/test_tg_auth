<?php
// Старт сессии
session_start();

// Если пользователь не вошел в систему, перейдите на страницу входа в систему
if (!isset($_SESSION['logged-in'])) {
    die(header('Location: login.php'));
}

// Импорт подключения к базе данных
require('db-config.php');

// Получить текущие данные пользователя, вошедшего в систему, с помощью сеанса
$user_data = $db->Select(
    "SELECT *
        FROM `tg_users`
            WHERE `telegram_id` = :id",
    [
        'id' => $_SESSION['telegram_id']
    ]
);


// Определяем чистые переменные с пользовательскими данными
$firstName        = $user_data[0]['first_name'];
$lastName         = $user_data[0]['last_name'];
$profilePicture   = $user_data[0]['profile_picture'];
$telegramID       = $user_data[0]['telegram_id'];
$telegramUsername = $user_data[0]['telegram_username'];
$userID           = $user_data[0]['id'];


/*
в приложении Telegram
фамилия | фотография профиля | имя пользователя Telegram
необязательны, поэтому эти данные отображаются с условием,
Используется значение NULL для этих необязательных данных в DB.
*/

/* ------------------------- */
/* ОТОБРАЖЕНИЕ ПОЛЬЗОВАТЕЛЬСКИХ ДАННЫХ В ФОРМАТЕ HTML */
/* ------------------------- */
if (!is_null($lastName)) {
    // Dотобразить имя и фамилию
    $HTML = "<h1>Hello, {$firstName} {$lastName}!</h1>";
} else {
    // Отображать имя
    $HTML = "<h1>Hello, {$firstName}!</h1>";
}

if (!is_null($profilePicture)) {
    // Отображать изображение профиля без кэширования "image.jpg?v=time()"
    $HTML .= '
    <a href="' . $profilePicture . '" target="_blank">
        <img class="profile-picture" src="' . $profilePicture . '?v=' . time() . '">
    </a>
    ';
}

if (!is_null($lastName)) {
    // Отобразить имя и фамилию
    $HTML .= '
    <h2 class="user-data">First Name: ' . $firstName . '</h2>
    <h2 class="user-data">Last Name: ' . $lastName . '</h2>
    ';
} else {
    // Отображать имя
    $HTML .= '<h2 class="user-data">First Name: ' . $firstName . '</h2>';
}

if (!is_null($telegramUsername)) {
    // Отображать имя пользователя Telegram
    $HTML .= '
    <h2 class="user-data">
        Username:
        <a href="https://t.me/' . $telegramUsername . '" target="_blank">
            @' . $telegramUsername . '
        </a>
    </h2>
    ';
}

// Отобразить идентификатор Telegram | Идентификатор пользователя | Кнопку выхода из системы
$HTML .= '
<h2 class="user-data">Telegram ID: ' . $telegramID . '</h2>
<h2 class="user-data">User ID: ' . $userID . '</h2>
<a href="logout.php"><h2 class="logout">Logout</h2></a>
';


// Отобразить все выбранные пользовательские данные
# echo '<style>body { background-color: #000 !important; } .middle-center { display: none !important; }</style>';
# echo '<pre>', print_r($user_data, TRUE), '</pre>';
# echo '<pre>', print_r($_SESSION, TRUE), '</pre>';
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Logged In User</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nanum+Gothic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="middle-center">
        <?= $HTML ?>
    </div>
</body>

</html>