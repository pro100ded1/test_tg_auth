<?php
// Завершите все сеансы и перейдите на страницу входа в систему
session_start();
session_unset();
session_destroy();
die(header('Location: login.php'));