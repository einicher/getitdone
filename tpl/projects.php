<h1><?=§('Projects')?></h1>

<div class="projects list-group clearfix">
<? foreach ($projects as $project => $count) : ?>
	<a href="<?=$this->link->get('getItDone.projects.project', $project)?>" class="list-group-item"><?=$project?> <span class="badge pull-right"><?=$count?></span></a>
<? endforeach; ?>
</div>
