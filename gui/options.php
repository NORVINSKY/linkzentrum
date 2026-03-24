<div class="row">
	<div class="twelve columns">
	<h4 class="centered text-center">Настройки размещения ссылок</h4>
	<?=$optStatus;?>
	<br>
<form  enctype="multipart/form-data" action="<?=$HOST; ?>/?options"  method="post">
	<div class="six columns field">
	<strong>Ссылок на страницу:</strong> <br>	
    <input style="width: 170px;" class="input" type="text" name="kolvos" value="<?=$optionsData['count']; ?>" placeholder="" />
	<br>
	<br>
	<strong>Показывать ссылки:</strong> <br>
	<div style="margin-left: 0px;" class="picker">
    <select name="cloak">
      <option value="1" <?if($optionsData['cloak'] == 1){echo 'selected';}?>>По UA</option>
      <option value="2" <?if($optionsData['cloak'] == 2){echo 'selected';}?>>По IP</option>
      <option value="3" <?if($optionsData['cloak'] == 3){echo 'selected';}?>>По UA+IP</option>
      <option value="0" <?if($optionsData['cloak'] == 0){echo 'selected';}?>>Всем</option>
    </select>
  </div>
  
  <br>
  <br>
	<strong>Разбавленных анкоров:</strong> <br>
	    <input style="width: 170px;" class="input" type="text" name="anch_mix" value="<?=$optionsData['anch_mix']?>" /> процентов
		
	<br>
	<br>
	<strong>Обновлять через:</strong> <br>
	    <input style="width: 170px;" class="input" type="text" name="upd_time" placeholder="" value="<?=round($optionsData['ch_time'] / 3600); ?>" /> часов
	<br>
	<br>
	<strong>Ссылок без ключа:</strong> <br>
	    <input style="width: 170px; margin-bottom: 15px;" class="input" type="text" name="anch_perc" value="<?=$optionsData['anch_perc']?>" /> процентов
		
	</div>
	
	<div class="six columns field">
	
	<strong>Распределение ссылок:</strong> <br>
	<label style="width: 50%;" class="radio <?if($optionsData['post_regime'] == 0){echo 'checked';}?>" for="radio5">
		<input name="post_regime" id="radio5" value="0" type="radio" <?if($optionsData['post_regime'] == 0){echo 'checked="checked"';}?>>
		<span></span> 1 стр. = 1 реципиент
	</label>
  <label style="width: 50%;" class="radio <?if($optionsData['post_regime'] == 1){echo 'checked';}?>" for="radio6">
    <input name="post_regime" id="radio6" value="1" type="radio" <?if($optionsData['post_regime'] == 1){echo 'checked="checked"';}?>>
    <span></span> 1 стр. = N реципиентов
  </label>	
  <br>
	<strong>Размещать ссылки:</strong> <br>
<label style="width: 50%;" class="radio <?if($optionsData['where_put'] == 0){echo 'checked';}?>" for="radio3">
    <input name="where_put" id="radio3" value="0" type="radio" <?if($optionsData['where_put'] == 0){echo 'checked="checked"';}?>>
    <span></span> Везде
  </label>
  <label style="width: 50%;" class="radio <?if($optionsData['where_put'] == 1){echo 'checked';}?>" for="radio4">
    <input name="where_put" id="radio4" value="1" type="radio" <?if($optionsData['where_put'] == 1){echo 'checked="checked"';}?>>
    <span></span> Только на главной
  </label>
  <br>
  	<strong>Файл с добавочными словами:</strong> <br>
	<div style="margin-left: 0px;" class="picker">
    <select name="mixw_file" onchange="changeHref();" id="thisWFile">
	
			<?php if(empty($optionsData['mixw_file']) || count($wFiles) == 0): ?>
				<option value="" selected ></option>
			<?php endif; ?>
			
			<?php foreach($wFiles as $fl): ?>
				<option value="<?=$fl;?>" <?if($optionsData['mixw_file'] == $fl){echo 'selected';}?>><?=$fl;?></option>
			<?php endforeach; ?>	

    </select>
  </div>
  <?php if(count($wFiles) > 0):?>
	<div class="medium danger btn icon-right entypo icon-trash" style="margin-left: 10px;margin-bottom: 2px;"><a style="padding-right: 18px;" id="deleteWFile" onclick="return confirm ('Удалить этот файл?')" href="<?=$HOST;?>/?options=del&file=<?=$optionsData['mixw_file'];?>" title="Удалить текущий файл"></a></div>
  <?php endif; ?>
  <div class="medium secondary btn icon-right entypo icon-attach" style="margin-left: 3px;margin-bottom: 2px;"><a style="padding-right: 18px;" class="switch" title="Загрузить файл со словами" href="#" gumby-trigger="#upload" ></a></div>

		<br>
		<br>
		
		<input name="def_cat" id="check1" value="1" type="hidden" >

		<input style="color: white; width: 180px; font-weight: 500;" name="options" class="large primary btn" type="submit" value="Сохранить">

	</div>
</div>
</form>

	<hr>
	<h4 class="centered text-center">Общие настройки</h4>
	<?=$passStatus;?>
	<form  enctype="multipart/form-data" action="<?=$HOST; ?>/?options"  method="post">
	<div class="twelve columns field">

	
	Новый пароль: <br>
    <input style="width: 250px;" class="input" type="password" name="passwd" placeholder="Главное не забыть..." />
	
	
<input style="color: white; width: 120px;" class="medium primary btn" type="submit" value="Обновить">
  </div>
	</form>
		
	</div>

<div class="modal" id="upload">
  <div class="content">
    <a class="close switch" gumby-trigger="|#upload"><i class="icon-cancel" /></i></a>
    <div class="row">
      <div class="ten columns centered text-center">
        <h3>Загрузка добавочных слов</h3>
		
		Внимание, файл должен быть в UTF-8!
		<div class="field">
		
		<form enctype="multipart/form-data" action="<?php echo $HOST?>/?options" method="POST">
		<hr>
			<center><input type="file" name="uploadFile"/> <br><br>
			<input style="margin-top:5px; color: white; width: 140px;" class="medium primary btn"  type="submit" name="upload" value="Загрузить"/>
		</center>
		</form>
	
		</div>
		
      </div>
    </div>
  </div>
</div>


<script>
function changeHref(){ $("#deleteWFile").attr("href", "<?php echo $HOST?>/?options=del&file=" + $( "#thisWFile" ).val()) }
</script>