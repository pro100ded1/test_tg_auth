Для настройки бота-авторизации:

1. Зарегистрировать бота (информации куча в интернете как это сделать через @BotFather)
2. Послать BotFather оманду /setdomain (для отладки на локальном компьютере это будет 127.0.0.1)
3. В файле config.php внести полученный при регистрации бота API ключ
4. В файле db-config внести данные для подключения к базе данных (находятся в самом низу файла)
5. В папке database находится файлик для экспорта таблицы в базу данных.
После создания БД (telegram_login) нужно выполнить экспорт table-structure.sql, который создаст
все необходимые таблицы.