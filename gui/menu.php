<div class="navcontain">
		<div class="pretty navbar" gumby-fixed="top" id="nav3">
			<div class="row">
				<a class="toggle" gumby-trigger="#nav3 > .row > ul" href="#"><i class="icon-menu"></i></a>
				<h1 class="four columns logo">
					<a style="pointer:cursor;">
						<img src="gui/img/mainlogo.png" gumby-retina />
					</a>
				</h1>
				
				<ul class="eight columns">
					<li><a class="mena" href="<?=$HOST;?>">Главная</a></li>
					<li><a class="mena" href="<?=$HOST;?>/?donor">Правила</a></li>
					<li><a class="mena" href="<?=$HOST;?>/?links">Ссылки</a></li>
					<li><a class="mena" href="<?=$HOST;?>/?options">Настройки</a></li>
					<li><p class="btn primary medium"><a href="#" class="switch" gumby-trigger="#modal1">Коды</a></p></li>
					<li>
					<button class="medium pretty default btn">
						<a href="<?=$HOST;?>/?logout"><i class="icon-logout"></i></a>
					</button>

					</li>
				</ul>
			</div>
		</div>
	</div>
	
<div class="modal" id="modal1">
  <div class="content">
    <a class="close switch" gumby-trigger="|#modal1"><i class="icon-cancel" /></i></a>
    <div class="row">
      <div class="ten columns centered text-center">
        <h3>Коды вставки</h3>
		
		Незакодированный (UTF-8)
		<div class="field">
		<textarea style="font-size: 14px; line-height: 18px;" class="input textarea" rows="7" placeholder=""><?= "<?php\r\n".str_replace('#CHARSET#', 'utf8', $clientCode)."?>";?></textarea>
		</div>
		
		Незакодированный (WIN-1251)
		<div class="field">
		<textarea style="font-size: 14px; line-height: 18px;" class="input textarea" rows="7" placeholder=""><?= "<?php\r\n".str_replace('#CHARSET#', 'win1251', $clientCode)."?>";?></textarea>
		</div>
		
		Base64 обфускация (UTF-8)
		<div class="field">
		<textarea style="font-size: 14px; line-height: 18px;" class="input textarea" rows="7" placeholder=""><?= "<?php\r\neval(base64_decode('".base64_encode(str_replace('#CHARSET#', 'utf8', $clientCode))."'));?>";?></textarea>
		</div>
		
		Base64 обфускация (WIN-1251)
		<div class="field">
		<textarea style="font-size: 14px; line-height: 18px;" class="input textarea" rows="7" placeholder=""><?= "<?php\r\neval(base64_decode('".base64_encode(str_replace('#CHARSET#', 'win1251', $clientCode))."'));?>";?></textarea>
		</div>
		
		Wordpress (инклюдить в functions.php)
		<div class="field">
		<textarea style="font-size: 14px; line-height: 18px;" class="input textarea" rows="7" placeholder=""><?= str_replace('#CHARSET#', 'utf8', $clientCodeWP);?></textarea>
		</div>
		<p class="btn default medium">
          <a href="#" class="switch" gumby-trigger="|#modal1">Закрыть</a>
        </p>
      </div>
    </div>
  </div>
</div>