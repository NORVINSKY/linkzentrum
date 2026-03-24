<?
#Процент ссылок без ключа
$optionsData = unserialize(file_get_contents(_DATA_.'.options'));

#Выгружаем ссылки
if($_GET['links'] == 'download'){

	//Выбираем домены
	if(!$result = $mysqli->query("SELECT domain FROM urls_domains;")){
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit($mysqli->close()); 
	}
	
	$viz = 0;
	$DOMaz = array();
	while($domData = $result->fetch_array(MYSQLI_ASSOC)){
		$DOMaz[$viz]['domain'] = $domData['domain'];
		$viz++;
	} $result->close();

	//Пытаемся выгрузить ссылки
	$superString = '';
	foreach($DOMaz as $selDom){
		if(!$result = $mysqli->query("SELECT url, keyword FROM urls WHERE domain='{$selDom['domain']}';")){
			//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
			echo( mysqli_error($mysqli));
			exit($mysqli->close()); 
		}

		while($LNKData = $result->fetch_array(MYSQLI_ASSOC)){
		$superString .= "<a href=\"{$LNKData['url']}\">{$LNKData['keyword']}</a>\r\n";

		} $result->close();
	}

file_put_contents(_DATA_ . 'cache/LinksBackup.txt',$superString, LOCK_EX);
usleep(350000); //Немного подождем
file_force_download(_DATA_ . 'cache/LinksBackup.txt'); //Отдаем на скачивание

$mysqli->close();
exit();
}

#Удаляем ссылки
if($_GET['links'] == 'delete' && isset($_GET['dom']) && !empty($_GET['dom'])){

//По домену (группу)
$dmnD = cleanDBstr($_GET['dom']);


	if($_GET['dom'] == 'all'){
	
	if(!$mysqli->query("TRUNCATE urls;")){ 
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit(); }	
		
	if(!$mysqli->query("TRUNCATE urls_domains;")){ 
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit(); }
	
	}else{
		
	//Блокируем таблицы
	if(!$mysqli->query("LOCK TABLES urls WRITE, urls_domains WRITE;")){
				//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
				echo( mysqli_error($mysqli));
				exit(); 
	}


	if(!$mysqli->query("DELETE urls, urls_domains FROM urls, urls_domains WHERE urls.domain = urls_domains.id AND urls_domains.domain = '{$dmnD}';")){ 
				//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
				echo( mysqli_error($mysqli));
				exit(); 
	}
	
			//Разблокируем таблицы
			if(!$mysqli->query("UNLOCK TABLES;")){
						//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
						echo( mysqli_error($mysqli));
						exit(); 
			}	
	
	}
	
header("Location: {$HOST}/?links");
$mysqli->close();
exit();
}

#Добавляем ссылки в базу
//Загружаем файл
$fileStatus = ''; //Статус загрузки
$fileStatus2 = ''; //Кол-во операций
if(isset($_POST['upload'])){
  $folder = _DATA_ . 'cache/';
  $uploadedFile = $folder.basename($_FILES['uploadFile']['name']);
  
  if(is_uploaded_file($_FILES['uploadFile']['tmp_name'])){
  if(move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadedFile)){
     $fileStatus =  '<span class="success alert">Файл загружен</span>';
  }else{
     $fileStatus =  '<span class="danger alert">Во время загрузки файла произошла ошибка</span>';
  }
  }else{
   $fileStatus =  '<span class="danger alert">Файл не  загружен</span>';
  }
}

//Обрабатываем файл
libxml_use_internal_errors(true); //Прибиваем ошибки
if(isset($uploadedFile)){

$lnStrings = array_unique(file($uploadedFile));
$lnStrings = array_map('cleanStr', $lnStrings);
$lnStrings = cleanArray($lnStrings);

$strLnkCount = count($lnStrings); //Кол-во ссылок в файле

	//Принимаем и обрабатываем данные о проценте ссылок без ключей
	$anch_percL = str_replace(',', '.', cleanStr($_POST['anch_perc']));
	$anch_percL = str_replace(' ', '', $anch_percL);
	if(!empty($anch_percL) && is_numeric($anch_percL) || $anch_percL == 0){
		$anch_percL = round((int)$anch_percL);
		if($anch_percL > 100){$anch_percL = 99;}
	}else{
		$anch_percL = $optionsData['anch_perc'];
	}
	
	//Вычисляем процент
	$perc = round(($strLnkCount*$anch_percL)/100); //Кол-во линков без ключа

	$URLwKEY = array(); //Массив для добавляемых ссылок

$percCount = 1;
foreach($lnStrings as $lnkStr){

	$dom = new DOMDocument; 
	$dom->loadHTML(cleanStr($lnkStr)); 
	$partedH = $dom->getElementsByTagName('a'); 
	foreach ($partedH as $partH) {
		$hrfs = $partH->getAttribute('href'); //Линк
	}
	unset($dom);
	
	if($percCount <= $perc){
		$tmpURL = parse_url($hrfs);
		$URLwKEY[$hrfs] = $tmpURL['host']; //Домен взамест ключа
	}else{
		$URLwKEY[$hrfs] = strip_tags(cleanStr($lnkStr)); //Ключ
	}
	$percCount++;
}

preg_match_all('~//(.*?)[/\'">]~is', file_get_contents($uploadedFile), $mtchd);
$pureDomains = array_unique($mtchd[1]);

unlink($uploadedFile); //Удаляем загруженый файл

////////////////////////

$data_add = time(); //Время добавления

#Пихаем в базу
foreach($pureDomains as $uniqDomain){//Заливаем домены

	//Проверяем наличие домена
	if(!$result = $mysqli->query("SELECT id FROM urls_domains WHERE domain LIKE '{$uniqDomain}'")){
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit($mysqli->close()); 
	}
	$inBaseDom = $result->fetch_array(MYSQLI_ASSOC);
	$result->close();

	if($inBaseDom == NULL){
		if(!$mysqli->query("INSERT INTO urls_domains (domain, data_add) VALUES ('{$uniqDomain}', {$data_add} )")){
			//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
			echo( mysqli_error($mysqli));
			exit($mysqli->close()); 
		}
	}
}

$addedStr = 0;
$URLwKEY = shuffle_assoc($URLwKEY);
foreach($URLwKEY as $lnkP => $kwdP){
	$parsedURL = parse_url($lnkP);
	$urlHost = cleanDBstr($parsedURL['host']); //Домен донора
	$posted_cn = 0;//Счетчик запощеных ссылок
	
	$lnkP = cleanDBstr($lnkP);
	$kwdP = cleanDBstr($kwdP);

	#пихаем линк в базу
//	if(!$mysqli->query("INSERT INTO urls (url, keyword, domain, posted_cn) VALUES ('{$lnkP}', '{$kwdP}', '{$urlHost}', {$posted_cn} )")){
	if(!$mysqli->query("INSERT INTO urls (url, keyword, domain) SELECT '{$lnkP}', '{$kwdP}', id FROM urls_domains WHERE domain = '{$urlHost}'")){
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit($mysqli->close()); 
	}	$addedStr++;
}

$fileStatus2 = 'В базу добавлено '.$addedStr.' записей.';
}

//Кол-во результатов
if(!$result = $mysqli->query("SELECT COUNT(DISTINCT domain) FROM urls ")){
	//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
	echo( mysqli_error($mysqli));
	exit($mysqli->close()); 
}
$resTmp = $result->fetch_array(MYSQLI_ASSOC); $resCount = $resTmp['COUNT(DISTINCT domain)']; //Число
$result->close();

//пагинация
$pageStep = 0; //Шаг результатов на странице
$resOnPage = 30;
if(is_numeric($_GET['links'])){
	for($ins = 1; $ins<=$_GET['links']-1; $ins++){
		$pageStep = $pageStep + $resOnPage;
	}
}

#Выбираем инфу о ссылках
if(!$result = $mysqli->query("SELECT domain, data_add FROM urls_domains ORDER BY data_add DESC LIMIT {$pageStep}, {$resOnPage}")){
	//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
	echo( mysqli_error($mysqli));
	exit($mysqli->close()); 
}

$idcnt = 0;
while($DOMAINStmp = $result->fetch_array(MYSQLI_ASSOC)){
	$LINKSdomain[$idcnt]['domain'] = $DOMAINStmp['domain'];
	$LINKSdomain[$idcnt]['data_add'] = $DOMAINStmp['data_add'];
	$idcnt++;
}

$result->close();