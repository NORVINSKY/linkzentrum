<div class="row">
	<div class="twelve columns field">
	
	<div class="six columns">
	<p>Файл должен быть в UTF-8</p>
	
	
	</p>
		<form  enctype="multipart/form-data" action="<?=$HOST; ?>/?links"  method="post">
			<input  type="file" name="uploadFile"/><br>

		<div style="margin-top: 15px;"></div>
		<strong>Ссылок без ключа:</strong> 
	    <input style="width: 60px; margin-bottom: 15px;" class="input" type="text" name="anch_perc" value="<?=$optionsData['anch_perc'];?>" /> %  <br>
	
		<input style="margin-top:5px; color: white; width: 140px;" class="medium primary btn"  type="submit" name="upload" value="Загрузить"/>
		</form>
		
	</div>
	
	<div class="six columns">
		<p>Поддерживаемые форматы ссылок:
	<br>
	<p class="default alert">&lt;a href="http://domain.tld/page/key.html"&gt;keyword&lt;a&gt;</p>
	</div>
		<?=$fileStatus;?><br>
		<?=$fileStatus2;?>

		<hr>
<?if(isset($LINKSdomain)):
$cnPages = ceil3($resCount / ($resOnPage ));
if($cnPages>1):?>
	<div class="pagin"> 
		<a href="<?=$HOST; ?>/?links">1</a>
		<? for($pgin = 2; $pgin<=$cnPages; $pgin++): ?>
			 / <a href="<?=$HOST; ?>/?links=<?=$pgin; ?>"><?=$pgin; ?></a>
		<? endfor; ?>
		 стр.
	</div>
<?endif; ?>	
	

<ul class="acc" id="acc">

	<? foreach($LINKSdomain as $LNdom): ?>
	<li>
		<h3><span><?=$LNdom['domain'];?> [<?=date('d/m H:i',$LNdom['data_add']);?>]</span> <span style="font-size:20px;float: right;"><a onclick="return confirm ('Удалить ссылки относящиеся к этому домену?')" href="<?=$HOST; ?>/?links=delete&dom=<?=$LNdom['domain'];?>" title="Удалить"><i class="icon-trash"></i></a></span></h3>
	</li>
	<?endforeach;?>
</ul>		
	<?if($cnPages>1):?>
		<div class="pagin">
			<a href="<?=$HOST; ?>/?links">1</a>
			<? for($pgin = 2; $pgin<=$cnPages; $pgin++): ?>
				 / <a href="<?=$HOST; ?>/?links=<?=$pgin; ?>"><?=$pgin; ?></a>
			<? endfor; ?> стр.
		</div>
	<?endif; ?>	
<?endif; ?>	
<div class="medium default btn"><a href="<?=$HOST; ?>/?links=download">Выгрузить ссылки</a></div>
<div class="medium warning btn"><a onclick="return confirm ('Удалить все ссылки из базы?')" href="<?=$HOST; ?>/?links=delete&dom=all">Удалить все ссылки</a></div>

		<ul>
<?/*
$cndr = 1;
foreach($LINKSdomain as $LNdom){
	echo '<li><p class="btn medium primary">
		<a href="#"  class="switch" gumby-trigger="#drawer'.$cndr.'">'.$LNdom.'</a>
		</p></li><div class="drawer" id="drawer'.$cndr.'">';
	foreach($LINKSinfo as $LNinfo){

		if($LNinfo['domain'] == $LNdom){
		
		echo 'LNK<br>';
		//print_r($LNinfo);
		
		}
	echo '</div>';
	}
$cndr++;
}
		*/
		?></ul>
		
		
	</div>
</div>

