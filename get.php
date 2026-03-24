<?
$start = microtime(true);
include_once 'conf.php';
include_once _LIB_ . 'functions.php';
error_reporting(0);

#Принимаем и подготавливаем данные
$USER_AGENT = cleanStr($_GET['useragent']);
$PAGE = cleanStr($_GET['pageurl']);
$CHARSET = cleanStr($_GET['ch']);
$IP = cleanStr($_GET['ip']);
$parsedURL = mb_strtolower(parse_url($PAGE, PHP_URL_HOST));
$pageHost = (strpos($parsedURL, 'www.') !== false)? str_ireplace('www.', '', $parsedURL) : $parsedURL ; //Домен донора

#Новые настройки
$pageHostSHA1 = sha1($pageHost); //Домен в SHA1
$pageSHA1 = sha1($PAGE); //Страница в SHA1

$OPTIONS = unserialize(file_get_contents(_DATA_.'.options')); //Данные о текущей категории

#Фильтруем расширение запроса
extFilter($PAGE);

#Проверка на бота
(isBotUA($USER_AGENT))? $BOT_UA = TRUE : $BOT_UA = FALSE ; //USER AGENT check
(isBotIP($IP))? $BOT_IP = TRUE : $BOT_IP = FALSE ; //USER AGENT check

$BotIsHere = FALSE; //По умолчанию бота нет
switch($OPTIONS['cloak']){
	case 0: $BotIsHere = TRUE; break;
	case 1: if($BOT_UA){$BotIsHere = TRUE;}else{$BotIsHere = FALSE;}; break;
	case 2: if($BOT_IP){$BotIsHere = TRUE;}else{$BotIsHere = FALSE;}; break;
	case 3: if($BOT_UA && $BOT_IP){$BotIsHere = TRUE;}else{$BotIsHere = FALSE;}; break;
}


if($USER_AGENT == _HOSTUA_ || $BotIsHere): //Бот пришел - РАБОТАЕМ

if(file_exists(_XML_ . $pageHostSHA1.'/'.$pageSHA1.'.xml')){

	if(is_putLinks()){
		$pageData = file_get_contents(_XML_ . $pageHostSHA1.'/'.$pageSHA1.'.xml');
		preg_match('~<update>(.*?)</update>~is', $pageData, $m);
		
		$DB = DBConnect();
		$dStat = $DB->select("donors_domains", ["status"], ["domain" => $pageHost]);
		if($dStat[0]['status'] != '1'){ exit(); } //Проверяем статус донора
		if(empty($pageData) || (time()-$m[1]) > $OPTIONS['ch_time']){ //обновляем ссылки
			 file_put_contents(_XML_ . $pageHostSHA1.'/.update', time());

			$LINKS = getLinks();
			if($LINKS['count'] > 0){
				preg_match('~<count>(.*?)</count>~is', $pageData, $m);
				file_put_contents(_XML_ . $pageHostSHA1.'/'.$pageSHA1.'.xml', fileTpl($LINKS['html'], $LINKS['count']));
				$recCL = $LINKS['count']-$m[1];
				$DB->update("counters", ["counter_links[+]" => $recCL], ["id" => 1]);				
				$strLinks = $LINKS['html']; unset($DB);
			}

		}else{ //загружаем имеющиеся ссылки
			preg_match('~<links>(.*?)</links>~is', $pageData, $m);
			$strLinks = $m[1];
		}	printLnks($strLinks);
	}
}else{ //Бот первый раз на странице
	$DB = DBConnect(); $newDomain = false;
	if(!file_exists(_XML_ . $pageHostSHA1)){ //создаем папку домена
		if(mkdir(_XML_ . $pageHostSHA1, 0777)) { $newDomain = true;
			$DB->insert("donors_domains", ["domain" => $pageHost,	"status" => 1]);
		}else{ derlog('Cannot create directory: '. _XML_ . $pageHostSHA1); unset($DB); die('<!--- ERROR --->');}
	} file_put_contents(_XML_ . $pageHostSHA1.'/.update', time());

	if(is_putLinks()){
		if(!$newDomain){ //Проверяем статус донора
			$dStat = $DB->select("donors_domains", ["status"], ["domain" => $pageHost]);
			if($dStat[0]['status'] != '1'){ exit(); } 
		} $LINKS = getLinks();
		
		if($LINKS['count'] > 0){
			file_put_contents(_XML_ . $pageHostSHA1.'/'.$pageSHA1.'.xml', fileTpl($LINKS['html'], $LINKS['count']));
			$DB->update("counters", ["counter_links[+]" => $LINKS['count']], ["id" => 1]);
			$DB->update("counters", ["counter_pages[+]" => 1], ["id" => 1]);
			printLnks($LINKS['html']);
		}
	} unset($DB);
}
endif;

