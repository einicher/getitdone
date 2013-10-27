
<h1><?=§('Lists')?></h1>

<div class="subpanel">
	<a href="<?=$this->link->get('getItDone.lists.create')?>" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> <?=§('Create list')?></a>
	<a href="<?=$this->link->get('getItDone.lists.import')?>" class="btn btn-default"><span class="glyphicon glyphicon-upload"></span> <?=§('Import list')?></a>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="lists table table-striped table-condensed">
			<tr>
				<th><?=§('Name')?></th>
				<th><?=§('Created')?></th>
			</tr>
<? foreach ($lists as $list) : ?>
			<tr>
				<td>
<a href="<?=$this->link->get('getItDone.lists.list', $list->id)?>"><?=$list->name?></a>
				</td>
				<td><?=date(§('Y-m-d H:i'), $list->created)?></td>
			</tr>
<? endforeach; ?>
		</table>
	</div>
</div>
