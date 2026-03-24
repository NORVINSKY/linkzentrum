<?
$optStatus = '';
// - Начало блока управления связями
if($rmPOST && isset($_POST['rulesJoin'])):
$rCatID = $_POST['rCatID']; //ID обрабатываемой категории

$rulesQuery = "UPDATE donors_domains SET status=0;"; //Подготавливаем таблицу для записи связей категория => Домены_доноров
if(isset($_POST['domainsDonors'])){//Обрабатываем связь категория => Домены_доноров

	foreach($_POST['domainsDonors'] as $updDStr){
		$rulesQuery .= "UPDATE donors_domains SET status=1 WHERE id={$updDStr}; ";
	}
}

$rulesQuery .= "UPDATE urls_domains SET status=0; "; //Подготавливаем таблицу для записи связей категория => Ссылки
if(isset($_POST['domainsLinks'])){//Обрабатываем связь категория => Ссылки

	foreach($_POST['domainsLinks'] as $updDStr){
		$rulesQuery .= "UPDATE urls_domains SET status=1 WHERE id={$updDStr}; ";
	}
}

//..Отправляем запросы базе
if(!empty($rulesQuery)){
	if(!$mysqli->multi_query($rulesQuery)){
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		exit($mysqli->close()); 
	}while(@$mysqli->next_result()){$result = $mysqli->store_result(); } //Strict
}
endif;// - Конец блока управления связями

//Выбираем данные о связях
$result = $mysqli->query("SELECT id, status, domain FROM urls_domains");
while($joinTmp = $result->fetch_array(MYSQLI_ASSOC)){
	$urlDomainsData[] = $joinTmp;
}$result->close();

//Выбираем данные о категориях
//$categData[1] = $optionsData;


//Выбираем данные о доменах
$result = $mysqli->query("SELECT id, domain, status FROM donors_domains");
while($joinTmp = $result->fetch_array(MYSQLI_ASSOC)){
	$donorDomainJoin[] = $joinTmp;
}$result->close();