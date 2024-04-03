<?php
//  Старт сессии
session_start();
require('config.php');

//Когда пользователь войдет в систему, перейдите на страницу пользователя
if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == TRUE) {
    die(header('Location: user.php'));
}


// Укажите имя пользователя вашего бота здесь
//define('BOT_USERNAME', 'LoginWithTelegramOnSitebot');
$bot_username = file_get_contents('config.php');

?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nanum+Gothic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <h1>Зарегистрируйтесь и войдите в систему с помощью Telegram</h1>
    <div class="middle-center">
        <h1>Привет, Аноним!</h1>
        <script async src="https://telegram.org/js/telegram-widget.js" data-telegram-login="<?= BOT_USERNAME ?>" data-size="large" data-auth-url="auth.php"></script>
    </div>
</body>

</html>