	<link rel="stylesheet" href="gui/forma/css/common.css" type="text/css" />
	<link type="text/css" rel="stylesheet" href="gui/forma/css/jquery-ui.css" />
	<link type="text/css" href="gui/forma/css/ui.multiselect.css" rel="stylesheet" />
	
	
	<script type="text/javascript" src="gui/forma/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="gui/forma/js/plugins/localisation/jquery.localisation-min.js"></script>
	<script type="text/javascript" src="gui/forma/js/plugins/scrollTo/jquery.scrollTo-min.js"></script>
	<script type="text/javascript" src="gui/forma/js/ui.multiselect.js"></script>
	<script type="text/javascript">
		$(function(){
			$.localise('ui-multiselect', {language: 'ru', path: 'gui/forma/js/locale/'});
			$(".multiselect").multiselect();
		});
	</script>

<div class="row">
	<div class="twelve columns">
		
<?=$optStatus;?>

<section class="vertical tabs">
	<ul class="tab-nav four columns">
		<li class="active"><a href="#">Основная группа</a></li>
		
		<?if(isset($categData)):
			foreach($categData as $categStr):?>
			<li><a href="#"><?=$categStr['name'];?></a></li>
			<?endforeach;
		endif;?>
		

	</ul>
	<div class="tab-content eight columns active">
<form action="<?=$HOST?>/?donors" method="post">
	
	<h4>Доноры <span style="font-size: 15px;">(площадки для простановки ссылок)</span></h4>
	<select id="domainslist" class="multiselect" multiple="multiple" name="domainsDonors[]">
<? if(isset($donorDomainJoin)):
			foreach($donorDomainJoin as $domDonorStr): ?>
				<option value="<?=$domDonorStr['id'];?>" <? if($domDonorStr['status'] == '1'){echo 'selected';}?>><?=$domDonorStr['domain'];?></option>
	<?  endforeach;
	 endif;?>
	</select>
	
	<h4 style="margin-top: 15px;">Ссылки</h4>
	<select id="links" class="multiselect" multiple="multiple" name="domainsLinks[]">
		<?if(isset($urlDomainsData)):
			foreach($urlDomainsData as $domStr): ?>
			<option value="<?=$domStr['id'];?>" <? if($domStr['status'] == '1'){echo 'selected';}?>><?=$domStr['domain'];?></option>
			<? endforeach;
		endif;?>
	</select>
		<input type="hidden" name="rCatID" value="1">
      <input type="submit" name="rulesJoin" value="Сохранить" style="color: white; width: 150px; margin-top: 7px; font-weight: 500;"  class="medium primary btn"/>
    </form>
	</div>
	
</section>

</div>
</div>