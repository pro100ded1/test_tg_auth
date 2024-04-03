<?php
// Старт сессии
session_start();

// Когда пользователь войдет в систему, перейдите на страницу пользователя
if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == TRUE) {
    die(header('Location: user.php'));
}

// Импорт подключения к базе данных и получение API Телеграм
require('db-config.php');
require('config.php');
// Разместите токен вашего бота здесь
//define('BOT_TOKEN', 'ххххххххххххххххххххххххххххххххххххххххххххххх');
//вместо этого будем использовать АПИ размещенный во внешнем файле config.php
$bot_token = file_get_contents('config.php');

// Хэш Telegram необходим для авторизации
if (!isset($_GET['hash'])) {
    die('Telegram hash not found');
}

// Официальная функция авторизации в Telegram
function checkTelegramAuthorization($auth_data)
{
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);
    $secret_key = hash('sha256', BOT_TOKEN, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    if (strcmp($hash, $check_hash) !== 0) {
        throw new Exception('Data is NOT from Telegram');
    }
    if ((time() - $auth_data['auth_date']) > 86400) {
        throw new Exception('Data is outdated');
    }
    return $auth_data;
}

// Функция аутентификации пользователя
function userAuthentication($db, $auth_data)
{
    // Функция создания пользователя
    function createNewUser($db, $auth_data)
    {
        // Пользователь не найден, поэтому создаем
        $id = $db->Insert(
            "INSERT INTO `tg_users`
                (`first_name`, `last_name`, `telegram_id`, `telegram_username`, `profile_picture`, `auth_date`)
                    values (:first_name, :last_name, :telegram_id, :telegram_username, :profile_picture, :auth_date)",
            [
                'first_name'        => isset ($auth_data['first_name']) ? $auth_data['first_name'] : '',
                'last_name'         => isset ($auth_data['last_name']) ? $auth_data['last_name'] : '',
                'telegram_id'       => $auth_data['id'],
                'telegram_username' => $auth_data['username'],
                'profile_picture'   => isset ($auth_data['photo_url']) ? $auth_data['photo_url'] : '',
                'auth_date'         => $auth_data['auth_date']
            ]
        );
    }

    // Функция обновления пользователя
    function updateExistedUser($db, $auth_data)
    {
        // Пользователь найден, обновим информацию
        $db->Update(
            "UPDATE `tg_users`
                SET `first_name`        = :first_name,
                    `last_name`         = :last_name,
                    `telegram_username` = :telegram_username,
                    `profile_picture`   = :profile_picture,
                    `auth_date`         = :auth_date
                        WHERE `telegram_id` = :telegram_id",
            [
                'first_name'        => $auth_data['first_name'],
                'last_name'         => isset($auth_data['last_name']) ? $auth_data['last_name'] : '',
                'telegram_username' => $auth_data['username'],
                'profile_picture'   => $auth_data['photo_url'],
                'auth_date'         => $auth_data['auth_date'],
                'telegram_id'       => $auth_data['id']
            ]
        );
    }

    // Функция проверки пользователя
    function checkUserExists($db, $auth_data)
    {
        // Получение ID пользователя Телеграм
        $target_id = $auth_data['id'];

        // Проверьте, существует ли пользователь в базе данных или нет
        $isUser = $db->Select(
            "SELECT `telegram_id`
                FROM tg_users
                    WHERE `telegram_id` = :id",
            [
                'id' => $target_id
            ]
        );

        // Возвращает true, если пользователь существует в базе данных
        if (!empty($isUser) && $isUser[0]['telegram_id'] === $target_id) {
            return TRUE;
        }
    }

    // Проверка пользователя
    if (checkUserExists($db, $auth_data) == TRUE) {
        // Пользователь найден, обновим информацию
        updateExistedUser($db, $auth_data);
    } else {
        // Пользователь не найден, создадим его
        createNewUser($db, $auth_data);
    }

    // Создать сеанс пользователя, вошедшего в систему
    $_SESSION = [
        'logged-in' => TRUE,
        'telegram_id' => $auth_data['id']
    ];
}

// Запустите процесс
try {
    // Получите данные авторизованного пользователя из виджета Telegram
    $auth_data = checkTelegramAuthorization($_GET);

    // Аутентифицировать пользователя
    userAuthentication($db, $auth_data);
} catch (Exception $e) {
    // Показать ошибки
    die($e->getMessage());
}

// Перейти на страницу пользователя
die(header('Location: user.php'));