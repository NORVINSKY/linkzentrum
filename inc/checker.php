<pre><?php
//header('Location: '.$HOST);
include_once _LIB_ . 'RollingCurl.php';
$chDomains = array(); //Тут храним выгруженные из базы домены
$deadQuery = ''; //Начало запроса, для обновления статуса (по умолчанию пустой)
$STREAMS = 18; //Кол-во потоков для чекинга доноров


if($_GET['check'] == 'donors'): //Проверяем доноров

#Вытягиваем домены доноров
if(!$result = $mysqli->query("SELECT page FROM donors GROUP BY host_id")){
	//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
	echo mysqli_error($mysqli);
exit('EXIT');}

while($tmpDomains = $result->fetch_array(MYSQLI_ASSOC)){
	$chDomains[] = cleanStr($tmpDomains['page']);
}$result->close();



#Загружаем страницы, ищем метку
$rc = new RollingCurl("request_callback");
$rc->window_size = $STREAMS;
foreach ($chDomains as $url) {
    // добавляем запросы в очередь
    $request = new RollingCurlRequest($url);
	$request->options = array(
	CURLOPT_HEADER => false,
	CURLOPT_CONNECTTIMEOUT => 30,
	CURLOPT_FOLLOWLOCATION => 1,
	CURLOPT_USERAGENT => _HOSTUA_
	);
    $rc->add($request);
}
$rc->execute();

endif;


if($_GET['check'] == 'delete'): //Удаляем не отвечающих доноров
if(!$mysqli->query("DELETE donors_domains, donors FROM donors_domains, donors WHERE donors_domains.status=0 AND donors_domains.domain=donors.host_id")){
	//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
$mysqli->close();exit();}

	
endif;




//////////////////////////////// Перенеси куда следует при рефакторинге
function request_callback($response, $info){// функция для обработки ответа
global $mysqli;
$chLnk = str_ireplace('www.', '', parse_url($info['url'], PHP_URL_HOST));;

echo 'GOOD <br>';
  $pos = strpos($response, _WORD_);
  if ($pos !== false) {
	$deadQuery = "UPDATE donors_domains SET status=1 WHERE domain LIKE '{$chLnk}'; ";
  }else{
    
	$deadQuery = "UPDATE donors_domains SET status=0 WHERE domain LIKE '{$chLnk}'; ";
  }
 
if(!$mysqli->query($deadQuery)){
//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
$mysqli->close();exit();}
}

 
//Заканчиваем работу
$mysqli->close(); 
ob_end_flush();
exit;