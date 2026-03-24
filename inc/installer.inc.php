<?

if(isset($_SESSION["install"]) && $_SESSION["install"] === true){
	
	if(!$result = $mysqli->query("CREATE TABLE IF NOT EXISTS `donors_domains` ( `id` INT NOT NULL AUTO_INCREMENT, `domain` VARCHAR(75) NOT NULL, `status` TINYINT(1) NOT NULL DEFAULT 1, PRIMARY KEY (`id`), UNIQUE INDEX `domain_UNIQUE` (`domain` ASC)) ENGINE = InnoDB;")){
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit($mysqli->close()); 
	}	
	if(!$result = $mysqli->query("CREATE TABLE IF NOT EXISTS `urls` ( `id` INT NOT NULL AUTO_INCREMENT, `url` VARCHAR(150) NOT NULL, `keyword` VARCHAR(150) NOT NULL, `posted_cn` INT NOT NULL DEFAULT 0, `domain` INT(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB;")){
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit($mysqli->close()); 
	}	
	if(!$result = $mysqli->query("CREATE TABLE IF NOT EXISTS `urls_domains` ( `id` INT NOT NULL AUTO_INCREMENT, `domain` VARCHAR(75) NOT NULL, `data_add` INT(11) NOT NULL, `status` TINYINT(1) NOT NULL DEFAULT 0, PRIMARY KEY (`id`), UNIQUE INDEX `domain_UNIQUE` (`domain` ASC)) ENGINE = InnoDB;")){
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit($mysqli->close()); 
	}	
	if(!$result = $mysqli->query("CREATE TABLE IF NOT EXISTS `counters` ( `id` tinyint(1) NOT NULL DEFAULT '1', `counter_links` int(11) NOT NULL DEFAULT '0', `counter_pages` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB;")){
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit($mysqli->close()); 
	}	
	if(!$result = $mysqli->query("INSERT INTO `counters` (`counter_links`, `counter_pages`) VALUES (0, 0);")){
		//>>>!!!!!!!!!!<<< Тут вызвать функцию логирования ошибок [сделать потом] >>>!!!!!!!!!!<<<
		echo( mysqli_error($mysqli));
		exit($mysqli->close()); 
	}
	
}
$_SESSION["install"] = false;
header('Location: '.$HOST);


