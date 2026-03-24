<?
$start = microtime(true);
set_time_limit(0);
ob_start();
session_start();
error_reporting(E_ALL);
include_once 'conf.php';
include_once _LIB_ . 'functions.php';

if(!file_exists(_XML_)){ mkdir(_XML_, 0777); }
if(!file_exists(_XML_.'.htaccess')){ file_put_contents(_XML_.'.htaccess','Deny from all'); }

#Конект к БД
$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if(mysqli_connect_errno()) {die( "Подключение невозможно: ".mysqli_connect_error());}

#Проверка доноров на работоспособность
if(isset($_GET['check'])){ include _LIB_ . 'checker.php'; exit;}
  
#Тип запроса
($_SERVER['REQUEST_METHOD'] == 'POST') ? $rmPOST = TRUE : $rmPOST = FALSE ;

#Тайтлы и определение страниц
$pagesUri = array(
'donor' => 'Правила - ',
'links' => 'Ссылки - ',
'options' => 'Настройки - ',
'install' => 'Install - '
);

$Pageu = 'main'; $TITLE = '';
foreach($pagesUri as $currUri => $currTitle){
	if(preg_match('~^/\?'.$currUri.'~i', $_SERVER['REQUEST_URI'], $mtchuri)){
		$Pageu = $currUri; $TITLE = $currTitle; 
		break;
	}
}

include_once _TPL_ . 'head.php'; //Шапка

#Авторизация [первичная проверка]  
(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] == 'true') ? $auth = TRUE : $auth = FALSE ; 

#Основной блок
if($auth){
	
//phpinfo
if(isset($_GET['info']) || isset($_GET['phpinfo'])){ phpinfo(); exit;}
	
//Выход
if(isset($_GET['logout'])){ $_SESSION["loggedIn"] = 'false'; header('Location: '.$HOST); die();}

//Готовим коды
$clientCode = file_get_contents(_DATA_ . 'client.txt');
$clientCode = str_replace('#DOMAIN#', $HOST, $clientCode);
$clientCodeWP = file_get_contents(_DATA_ . 'clientWP.txt');
$clientCodeWP = str_replace('#DOMAIN#', $HOST, $clientCodeWP);

include_once _TPL_ . 'menu.php'; //Меню

#Главная
if($Pageu == 'main'){

//Подсчитываем всякое
$countQuery = "SELECT COUNT(id) FROM urls; SELECT COUNT(id) FROM urls_domains; 
SELECT COUNT(id) FROM donors_domains; SELECT counter_links FROM counters; SELECT counter_pages FROM counters; ";
$vl=0; $installer = false;
if ($mysqli->multi_query($countQuery)){
do {
	if ($result = $mysqli->store_result()) {
		while ($row = $result->fetch_row()) {
			$massCount[$vl]= $row;
			$vl++;
		}
		$result->free();
	}
} while (@$mysqli->next_result());//STRICT
}else{ //Проверка перед установкой
$installer = true; $inst_str=''; $chc = 0;
	
if(version_compare(phpversion(), '5.4.1', '>')) { $mess = phpversion() . ' <font color="green">OK</font>'; $chc++; }
else {$mess = phpversion() . ' <font color="red">Error!</font> (Min PHP VERSION 5.4.1)';}
$inst_str .= '<b>PHP:</b> ' . $mess . '<br>';	
	
if(extension_loaded("mbstring")){ $mess = '<font color="green">OK</font>'; $chc++; }else{ $mess = '<font color="red">Error!</font>'; }
$inst_str .= '<b>MBString:</b> ' . $mess . '<br>';

if(stripos($_SERVER["SERVER_NAME"], _DOM_) !== false){ $mess = '<font color="green">OK</font>'; $chc++; }else{ $mess = '<font color="red">'._DOM_.'</font>'; }
$inst_str .= '<b>Domain:</b> ' . $mess . '<br>';

if(class_exists('DOMDocument')){ $mess = '<font color="green">OK</font>'; $chc++; }else{ $mess = '<font color="red">Error!</font>'; }
$inst_str .= '<b>DOMDocument:</b> ' . $mess . '<br>';

if($chc == '4'){ $_SESSION["install"] = true;}

}

include_once _TPL_ . 'main.php'; //Меню

}

#Установщик
if($Pageu == 'install'){ include_once _LIB_ . 'installer.inc.php'; }

#Доноры
if($Pageu == 'donor'){
	include_once _LIB_ . 'donors.inc.php'; //Логика доноров
	include_once _TPL_ . 'donors.php'; //Тело доноров
}

#Настройки
if($Pageu == 'options'){
	include_once _LIB_ . 'options.inc.php'; //Логика настроек
	include_once _TPL_ . 'options.php'; //Тело настроек
}

#Ссылки
if($Pageu == 'links'){
	include_once _LIB_ . 'links.inc.php'; //Логика ссылок
	include_once _TPL_ . 'links.php'; //Тело ссылок
}

}else{

#Авторизация
$authWarn = '';
if($rmPOST){

	if (!empty($_POST['passwd'])){
		
		if(file_exists(_DATA_.'.pwd')){
			//Сверяем пароли
			$hash = file_get_contents(_DATA_.'.pwd');
			if(saltPass(cleanStr($_POST['passwd'], 1)) === $hash){
				$_SESSION["loggedIn"] = 'true';
				header('Location: '.$HOST);
				die($mysqli->close());
			} 
		}else{ //По умолчанию устанавливается пароль A123456a
			file_put_contents(_DATA_.'.pwd', '5621bb1052a2ad4209da7d0d360656ce553fac6385621bb1052a2ad4209da7d0d360656ce553fac638'); 
			$authWarn = '<div class="danger alert">Установлен служебный пароль: <b>A123456a</b></div>';
		}

	}else{ $authWarn = '<div class="danger alert">Введи пароль</div>'; }
}
	include_once _TPL_ . 'auth.php'; //Страничка авторизации
}

include_once _TPL_ . 'footer.php'; //Подвал

#Отключаемся от БД
$mysqli->close();
$time = microtime(true) - $start;
printf('<center>Render time %.4F sec.</center>', $time);
ob_end_flush();