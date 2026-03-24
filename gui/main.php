<?php if(!$installer): ?><div class="row">
	<div class="twelve columns">
	<div><div class="medium primary btn"><a href="<?=$HOST;?>/#">Проверка доноров</a></div></div>
<h2 style="text-transform: uppercase;">Ссылки: <?=$massCount[0][0]?> <span style="font-size: 23px;">шт. ( Доменов: <?=$massCount[1][0]?> <span style="font-size: 15px;">шт.</span> )</span></h2>
<h2 style="text-transform: uppercase;">Ссылок размещено: <?=$massCount[3][0]?> <span style="font-size: 23px;">шт.</span></h2>
<h2 style="text-transform: uppercase;">Доноры: <?=$massCount[2][0]?> <span style="font-size: 23px;">шт. ( Страниц: <?=$massCount[4][0]?> <span style="font-size: 15px;">шт.</span> )</span> </h2>
	</div>
</div>
<?php else: ?>

<div class="row">
	<div class="twelve columns">
<?=$inst_str;?>
<?php if($chc == '4'): ?>
<div><div class="medium primary btn"><a href="<?=$HOST;?>/?install">Install</a></div></div>
<?php endif; ?>
	</div>
</div>
<?php endif; ?>
