<?
setlocale(LC_ALL, 'ru_RU.UTF-8');
header('Content-Type: text/html; charset=utf-8');
### Настройки ###
define('_DOM_', 'lzentrum.my'); //Домен (без слеша на конце)
define('_HOSTUA_', 'LINKZENTRUM'); //Наш User Agent
define('_WORD_', md5('LINKZENTRUM')); //Метка для чекера


//БАЗА ДАННЫХ
define('DB_HOST', 'localhost'); //Хост БД
define('DB_NAME', 'Linkzentrum'); //Имя БД
define('DB_USER', 'root'); //Пользователь БД
define('DB_PASS', ''); //Пароль к БД


#Пути
define('_ROOT_', __DIR__ . '/'); //Корень
define('_DATA_', _ROOT_ . 'data/');	//Данные
define('_LIB_', _ROOT_ . 'inc/'); //Подключаемые файлы
define('_TPL_', _ROOT_ . 'gui/'); //Шаблон
define('_XML_', _ROOT_ . 'XML/'); //Папка с XML файлами
define('_WRDS_', _DATA_ . 'words/'); //Папка с файлами добавочных слов

define('_VER_', '0.1.9a');
$HOST = 'http://'. _DOM_;