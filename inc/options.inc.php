<?
// - Начало блока смены пароля
$passStatus = (isset($_SESSION["pswdChanged"]) && (time() - $_SESSION["pswdChanged"])< 5 )? '<p class="success alert">Пароль успешно изменен!</p>' : '' ;
if($rmPOST && isset($_POST['passwd'])):

if(!empty($_POST['passwd'])){
$prePass = cleanStr($_POST['passwd']);
if(!strpos($prePass, ' ')){
if(strlen($prePass) >= 6 ){
	file_put_contents(_DATA_.'.pwd', saltPass($prePass));	
	$_SESSION["pswdChanged"] = time();
	header("Location: {$HOST}/?options");
	exit();

}else{
	$passStatus = ' <p class="danger alert">Минимальная длина пароля 6 символов!</p> ';
}

}else{
	$passStatus = ' <p class="danger alert">Пароль не может содержать пробелы!</p> ';
}

}else{
	$passStatus = ' <p class="danger alert">Пароль не может быть пустым!</p> ';
}
endif; // - Конец блока смены пароля

// - Начало блока обработки настроек
$optStatus = '';
if($rmPOST && isset($_POST['options'])):
//Проверяем данные и формируем запрос
$updQuery = '';
$rawOptions = [];
	$kolvos = cleanStr($_POST['kolvos']);
	if(!empty($kolvos) && is_numeric($kolvos) && ($kolvos*1) >= 0){
		$rawOptions['count'] = $kolvos; //Кол-во ссылок на стринице
		$kolvoStat = true;
	}else{
		$kolvoStat = false;
		$optStatus .= ' <p class="danger alert">Кол-во ссылок на страницу не может быть пустым, равным нулю или содержать символы отличные от цифр.</p> ';
	}

	$upd_time = str_replace(',', '.', cleanStr($_POST['upd_time']));
	$upd_time = str_replace(' ', '', $upd_time);
	if(!empty($upd_time) && is_numeric($upd_time) && ($upd_time*1) >= 0){
		$rawOptions['ch_time'] = $upd_time*3600; //Интервал обновления ссылок
		$updStat = true;
	}else{
		$updStat = false;
		$optStatus .= ' <p class="danger alert">Интервал обновления не может быть пустым, равным нулю или содержать символы отличные от цифр.</p> ';
	}	
	
	#Процент безанкорных ссылок
	$anch_perc = str_replace(',', '.', cleanStr($_POST['anch_perc']));
	$anch_perc = str_replace(' ', '', $anch_perc);
	if(!empty($anch_perc) && is_numeric($anch_perc) || $anch_perc == 0){
		$anch_perc = round((int)$anch_perc);
		if($anch_perc > 100){$anch_perc = 100;}
		$rawOptions['anch_perc'] = $anch_perc;
		$anchStat = true;
	}else{
		$anchStat = false;
		$optStatus .= ' <p class="danger alert">Процент не может быть пустым или содержать символы отличные от цифр.</p> ';
	}	
	
	#Процент разбавленных анкоров
	$anch_mix = str_replace(',', '.', cleanStr($_POST['anch_mix']));
	$anch_mix = str_replace(' ', '', $anch_mix);
	if(!empty($anch_mix) && is_numeric($anch_mix) || $anch_mix == 0){
		$anch_mix = round((int)$anch_mix);
		if($anch_mix > 100){$anch_mix = 100;}
		$rawOptions['anch_mix'] = $anch_mix;
		$anchMix = true;
	}else{
		$anchMix = false;
		$optStatus .= ' <p class="danger alert">Процент не может быть пустым или содержать символы отличные от цифр.</p> ';
	}

	//Принимаем фиксированные данные
	$rawOptions['cloak'] = $_POST['cloak'];
	$rawOptions['post_regime'] = $_POST['post_regime'];
	$rawOptions['where_put'] = $_POST['where_put'];
	$rawOptions['mixw_file'] = $_POST['mixw_file'];
	
	$rawOptions['def_cat'] = (isset($_POST['def_cat']))? 1 : 0 ;// Опция: новых доноров связывать с основной группой

	if($kolvoStat && $updStat && $anchStat && $anchMix){
		if(file_put_contents(_DATA_.'.options', serialize($rawOptions))){$optStatus = '<p class="success alert">Настройки сохранены</p>';}
	}
endif; // - Конец блока обработки настроек

// - начало загрузки файла с добавочными ключами
if(isset($_POST['upload'])){
  $uploadedFile = _WRDS_ . basename($_FILES['uploadFile']['name']);
  
  if(is_uploaded_file($_FILES['uploadFile']['tmp_name'])){

  $uploadedFile = chkDoubleFile($uploadedFile, _WRDS_);
  
  if(move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadedFile)){
     $optStatus =  '<span class="success alert">Файл загружен</span>';
  }else{
     $optStatus =  '<span class="danger alert">Во время загрузки файла произошла ошибка</span>';
  }
  }else{
   $optStatus =  '<span class="danger alert">Файл не  загружен</span>';
  }
}
// - конец загрузки файла с добавочными ключами

//Загрузка настроек из файла
$optionsData = unserialize(file_get_contents(_DATA_.'.options'));

// - удаление файла с доп. словами
if($_GET['options'] == 'del' && !empty($_GET['file'])){
unlink(_WRDS_ . $_GET['file']);

if($optionsData['mixw_file'] == $_GET['file']){
 $optionsData['mixw_file'] = '';
 file_put_contents(_DATA_.'.options', serialize($optionsData));
}

exit(header("Location: {$HOST}/?options"));
}
// - конец удаления файла с доп. словами
	
// - сканируем дирректорию с файлами добавочных ключей
$scandir = scandir(_DATA_ . 'words/');

$wFiles = array();
foreach($scandir as $fl){
	if ($fl != '.' && $fl != '..') {
		$wFiles[] = $fl;
	}
}

