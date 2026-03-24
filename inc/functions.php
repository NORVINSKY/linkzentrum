<?
include_once _LIB_ . 'medoo.php';

#Чистим строку от переноса
function cleanStr($str, $st=0){
	if($st==1){$str = strip_tags($str);}
    $str = str_replace("\n", "", $str);
    $str = str_replace("\r", "", $str);
    $str = preg_replace("/\t/"," ","$str");
    $str = preg_replace('/[ ]+/', ' ', $str);
		$str = trim($str);
 return $str;   
}

#Чистим массив от всякого
function cleanArray($a, $m=['',' ']){ return array_diff($a, $m); }

#Mysqli_real_escape_string
function cleanDBstr($str){
global $mysqli;
	return $mysqli->real_escape_string(trim($str));
}

#Солим пароль
function saltPass($str){
	$l = strlen($str);
	for($i=1;$i<=$l;$i++){
		$str = sha1($str).$i.sha1($str).$l;
	} return $str;
}

#Проверка на ЮзерАгент бота
function isBotUA($UA){ 
	foreach (file(_DATA_ . 'USER-AGENTS.txt') as $strUA) {
		if (stripos($UA, cleanStr($strUA)) !== false){
			return true;
		}
	} return false;
}

#Проверка на IP бота
function isBotIP($IP){
	$listIP = file(_DATA_ . 'IP-RANGES.txt');
	
	foreach($listIP as $strIP){
		if(ip_range($IP, $strIP)){
			return true;
		}
	} return false;
}

#Проверка на вхождение в диапазон IP
function ip_range($ip, $range) {
	if(strpos($range, '-') == false && strpos($range, '/') == false) {
		if($ip == $range) return true;
		return false;
	}
	if(strpos($range, '/')) {
		list($subnet, $bits) = explode('/', $range);
		$ip      = ip2long($ip);
		$subnet  = ip2long($subnet);
		$mask    = -1 << (32 - $bits);
		$subnet &= $mask;
		return ($ip & $mask) == $subnet;
	}else {
		$range       = explode('-',trim($range));
		$range_start = ip2long($range[0]);
		$range_end   = ip2long($range[1]);
		$ip          = ip2long($ip);
		if($ip >= $range_start && $ip <= $range_end) return true;
	}	return false;
}


#Округление в большую сторону
function ceil3($number, $precision = 0) {
    return ceil($number * pow(10, $precision)) / pow(10, $precision);
}

#выводим ссылки
function printLnks($lnks){
global $CHARSET; 

$str = '<ul>';
$str .= $lnks;
$str .= '</ul><!--Render in '.scWT().' sec -->';

//Кодировка
if($CHARSET == 'win1251')
	{$str = iconv("utf-8", "windows-1251//IGNORE", $str);}

die($str);
}

#Разбавление анкоров
function mixAnch($wrd, $mixWords){
if(isset($mixWords) && is_array($mixWords)){
	$WORDO = cleanStr($mixWords[array_rand($mixWords)]);
	$rnd = mt_rand(0,2);
	if($rnd == 1){ //в начале анкора
		$wrd = mb_ucfirst($WORDO).' '.mb_strtolower($wrd, 'UTF-8');
	}elseif($rnd == 2 && substr_count($wrd, ' ')){ //в середине анкора
		preg_match_all('/\x20/s', $wrd, $m, PREG_OFFSET_CAPTURE);
		$i = mt_rand(0, count($m[0]) - 1);
		$wrd = substr($wrd, 0, $m[0][$i][1]).' '.$WORDO.' '.substr($wrd, $m[0][$i][1] + 1);
	}else{ //в конце анкора
		$wrd = $wrd.' '.$WORDO;
	}
} return $wrd;
}

#Переводим первую букву в верхний регистр
function mb_ucfirst($string, $enc = 'UTF-8'){
  return mb_strtoupper(mb_substr($string, 0, 1, $enc), $enc) . 
         mb_substr($string, 1, mb_strlen($string, $enc), $enc);
}

#Выводим время исполнения скрипта
function scWT(){
global $start;
	if(isset($start)){
		$time = microtime(true) - $start;
		return sprintf('%.4F', $time);
	} return false;
}

#Логирование
function derlog($error){



}

//Отдаем файл на скачивание
function file_force_download($file) {
  if (file_exists($file)) {
    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
    // если этого не сделать файл будет читаться в память полностью!
    if (ob_get_level()) {
      ob_end_clean();
    }
    // заставляем браузер показать окно сохранения файла
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    // читаем файл и отправляем его пользователю
    if ($fd = fopen($file, 'rb')) {
      while (!feof($fd)) {
        print fread($fd, 1024);
      } fclose($fd);
    } exit;
  }
}

#Проверка дублей при загрузке файла
function chkDoubleFile($path, $folder, $i=1){
$i++;
	if(file_exists($path)){
		$path = $folder.$i.'_'.basename($path);
		$path = chkDoubleFile($path, $folder, $i);
	} return $path;
}

#Модный var_dump
function xx($v){ echo '<pre>'; die(var_dump($v)); }

#Шаблон файла
function fileTpl($l, $c){
$t = time();
$STRUCT = <<<STRUCT
<update>{$t}</update>
<count>{$c}</count>
<links>
{$l}
</links>

STRUCT;

return $STRUCT;
}

#Конект к БД
function DBConnect(){
return new medoo([
	'database_type' => 'mysql',
	'database_name' => DB_NAME,
	'server' => DB_HOST,
	'username' => DB_USER,
	'password' => DB_PASS,
	'charset' => 'utf8',
]);
}

#тянем линки из базы
function getLinks(){
	global $DB, $OPTIONS; $str = ''; $wh = '';
	
	if($OPTIONS['post_regime'] == '0'){ // 1 стр. = 1 рецепиент
			$datas = $DB->query("SELECT id FROM `urls_domains` WHERE `status` = 1 ORDER BY RAND() LIMIT 1")->fetchAll();
			$wh = "AND `urls`.`domain` LIKE '{$datas[0]['id']}' ";	}
	
	$datas = $DB->query("SELECT `urls`.`id`, `urls`.`url`, `urls`.`keyword` FROM `urls` LEFT JOIN (`urls_domains`) ON (`urls_domains`.`id`=`urls`.`domain` ) WHERE `urls_domains`.`status`=1 {$wh}ORDER BY `urls`.`posted_cn` ASC, `urls`.`id` DESC LIMIT {$OPTIONS['count']}")->fetchAll();

	if(mt_rand(1, 100) <= $OPTIONS['anch_mix']){
		if(!empty($OPTIONS['mixw_file']) && file_exists(_WRDS_ . $OPTIONS['mixw_file'])){
			$mixWords = file(_WRDS_ . $OPTIONS['mixw_file']);	
		}
	}

	foreach($datas as $line){
		$str .= '<li><a href="'.$line['url'].'">'.mixAnch($line['keyword'], $mixWords).'</a></li>'."\r\n";
		$DB->update("urls", ["posted_cn[+]" => 1], ["id" => $line['id']]);
	} return ['html'=>$str, 'count'=>count($datas)];
}

#Проверка на главную страницу
function checkMP(){
global $PAGE;

	$parsedCurrPage = parse_url($PAGE);
	$mainPageMarks = array('/', '/index.php', '/index.html', 'main.php', 'main.html', 'home.php', '/index', '/home', '/index.htm', 'main.htm'); //Метки главной страницы

	if(!isset($parsedCurrPage['query'])){$parsedCurrPage['query'] = '';} //Прибиваем лишние "Notice"
	foreach($mainPageMarks as $MPMark){
		if(preg_match('~^'.$MPMark.'$~i', $parsedCurrPage['path'].$parsedCurrPage['query'], $mtchMP)){
			return TRUE; //Мы на главной
		}
	}	return FALSE;
}

#Проверка на возможность поставить ссылку
function is_putLinks(){
global $OPTIONS, $DB;
	if($OPTIONS['where_put'] == '0'){ //Ставим ссылки на всех страницах
		return TRUE;
	}else{ //Ставим только на главной
		if(checkMP()){ return TRUE; }else{ unset($DB); exit(); }
	} return FALSE;
}

#Перемешиваем массив с сохранением ключей
function shuffle_assoc($list) { 
  if (!is_array($list)) die('IS NOT ARRAY!'); 
  $keys = array_keys($list); 
  shuffle($keys); 
  $random = array(); 
  foreach ($keys as $key) { $random[$key] = $list[$key]; }
  return $random; 
} 

#фильтр расширений
function extFilter($e){
	$exts = ['.css', '.jpg', '.png', '.jpeg', '.js', '.svg', '.gif', '.ico', '.woff', '.bmp'];
	foreach($exts as $ex){ if(preg_match('~\\'.$ex.'$~is', $e, $m)){die();}}
}