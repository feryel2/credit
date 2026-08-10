<?php

date_default_timezone_set('Europe/Paris');

define('APP_ROOT', dirname(__DIR__));
define('PROJECT_ROOT', dirname(APP_ROOT));
define('BASE_URL', '/');
define('DB_PATH', PROJECT_ROOT . '/storage/database.sqlite');
define('STORAGE_PATH', PROJECT_ROOT . '/storage');
define('ASSET_PATH', BASE_URL . 'assets');
