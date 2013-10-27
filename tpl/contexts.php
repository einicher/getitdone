<h1><?=§('Contexts')?></h1>

<ul class="contexts list-group clearfix">
<? foreach ($contexts as $context => $count) : ?>
	<a href="<?=$this->link->get('getItDone.contexts.context', $context)?>" class="list-group-item"><?=$context?> <span class="badge"><?=$count?></span></a>
<? endforeach; ?>
</ul>
