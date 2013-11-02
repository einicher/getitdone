<div id="taskList">

<h1><?

	if ($list->isProject) :
		echo §('Project %s', '<a href="'.$this->link->get('getItDone.projects.project', $list->project).'" class="label label-info">'.$list->project.'</a>');
	elseif ($list->isContext) :
		echo §('Context %s', '<a href="'.$this->link->get('getItDone.contexts.context', $list->context).'" class="label label-warning">'.$list->context.'</a>');
	elseif (empty($list->id)) :
		echo $list->name;
	else :
		echo §('List %s', '<a href="'.$this->link->get('getItDone.lists.list', $list->id).'" class="label label-success">'.$list->name.'</a>');
	endif;

?></h1>

<div class="subpanel">
		<div class="btn-group">
			<a class="btn btn-default<?= !empty(Users::$user->settings->status) && Users::$user->settings->status == 2 ? ' active' : '' ?>" href="?setting=status&value=2"><?=§('All')?></a>
			<a class="btn btn-default<?= !empty(Users::$user->settings->status) && Users::$user->settings->status == 1 ? ' active' : '' ?>" href="?setting=status&value=1"><?=§('Done')?></a>
			<a class="btn btn-default<?= empty(Users::$user->settings->status) ? ' active' : '' ?>" href="?setting=status&value=0"><?=§('Undone')?></a>
		</div>
<? if ($list->id) : ?>
		<a class="btn btn-default" href="<?=$this->link->get('getItDone.lists.list.delete', $list->id)?>"><?=§('Delete')?></a>
<? endif; ?>
<? if ($export) : ?>
		<a class="btn btn-default pull-right" href="<?=$export?>"><?=§('Export')?></a>
<? endif; ?>
</div>

<form id="taskListForm" method="post" action="" class="form-inline">
	<div class="input-group">
		<input type="text" name="listNewTask" id="listNewTask" class="form-control" placeholder="<?=§('Enter your to do here and hit return..')?>" value="<?=$listNewTask?>" autofocus="autofocus">
		<span class="input-group-btn">
			<button id="deadlineButton" class="btn btn-default btn-default" type="button"><?=§('Deadline')?></button>
			<button class="btn btn-default btn-primary" type="submit"><?=§('Add')?></button>
		</span>
	</div>
	<script type="text/javascript">
		jQuery('#listNewTask').datepicker({
			dateFormat: 'yy-mm-dd',
			showOn: 'none',
			constrainInput: false,
			showWeek: true,
			onSelect: function(date) {
				jQuery(this).val(this.cacheValue + ' DUE:' + date);
			},
			beforeShow: function(i) {
				this.cacheValue = i.value;
			},
			firstDay: 1
		});
		el = jQuery('#listNewTask').get(0);
		elemLen = el.value.length;
		el.selectionStart = elemLen;
		el.selectionEnd = elemLen;
		el.focus();
		jQuery('#deadlineButton').click(function() {
			jQuery('#listNewTask').datepicker('show');
		});
	</script>
</form>


<div class="row-fluid">
	<table class="tasks table table-striped table-condensed">
		<tr>
			<th class="hidden-xs"><?=§('Controls')?></th>
			<th><?=§('Task')?></th>
			<th class="hidden-xs"><?=§('Created')?></th>
		</tr>

<? foreach ($tasks as $task) : ?>

		<tr class="task<?=$task->done > 0 ? ' done' : ''?>" id="task-<?=$task->id?>">
			<td class="controls hidden-xs">
				<span class="glyphicon <?=$task->done > 0 ? 'glyphicon-repeat task-undone' : 'glyphicon-ok task-done' ?>" title="<?=$task->done > 0 ? §('Undone') : §('Done') ?>"></span>
				<span class="task-edit glyphicon glyphicon-edit" title="<?=§('Edit')?>"></span>
				<span class="task-remove glyphicon glyphicon-trash" title="<?=§('Delete')?>"></span>
			</td>
			<td class="swipeControls" style="display: none;">
				<div class="swipeControl task-done"><span class="glyphicon glyphicon-ok"></span> <?=§('Done')?></div>
				<div class="swipeControl task-edit"><span class="glyphicon glyphicon-edit"></span> <?=§('Edit')?></div>
				<div class="swipeControl task-remove"><span class="glyphicon glyphicon-trash"></span> <?=§('Delete')?></div>
			</td>
			<td class="content"><?=$list->detectSyntax($task->content)?></td>
			<td class="created hidden-xs"><?=date(§('Y-m-d H:i'), strtotime($task->created))?></td>
		</tr>

<? endforeach; ?>

	</table>
</div>
<div id="hiddenTaskEditForm" style="display: none;">
	<div class="form-group">
		<textarea name="task" class="form-control"></textarea>
	</div>
	<div class="form-group" style="margin: -10px 0 0 0;">
		<button class="deadlineButton btn btn-default btn-default btn-lg" type="button">Deadline</button>
		<button type="button" name="cancel" class="btn btn-lg"><?=§('Cancel')?></button>
		<button type="submit" name="submit" class="btn btn-primary btn-lg">Save</button>
	</div>
	<div class="form-group" style="margin: 0; line-height: 0;">
		<input type="text" name="datepickerSucks" style="height: 0; padding: 0; border: 0;" />
	</div>
</div>

<script type="text/javascript">
	jQuery('#taskList table tr').swipe({
		swipeLeft: mobileControls,
		swipeRight: mobileControls,
		threshold: 100 //Default is 75px, set to 0 for demo so any distance triggers swipe
	});

	function mobileControls(event, direction, distance, duration, fingerCount)
	{
		jQuery('.swipeControls', this).toggle();
		jQuery('.content', this).toggle();
	}

	function taskEditFormControls()
	{
		jQuery('table.tasks tr').off('dblclick');
		jQuery('table.tasks tr .task-edit').off('click');
		jQuery('table.tasks tr .task-remove').off('click');
		jQuery('table.tasks tr .task-done').off('click');
		jQuery('table.tasks tr .task-undone').off('click');

		jQuery('table.tasks tr').dblclick(taskEditForm);
		jQuery('table.tasks tr .task-edit').click(taskEditForm);
		jQuery('table.tasks tr .task-remove').click(taskRemoveForm);
		jQuery('table.tasks tr .task-done').click(taskDoneForm);
		jQuery('table.tasks tr .task-undone').click(taskUndoneForm);
	}
	function taskEditForm()
	{
		if (this.tagName == 'TR') {
			var tr = jQuery(this);
		} else {
			var tr = jQuery(this).parent().parent();
		}
		tr.off('dblclick');
		var cache = tr.html();
		var id = tr.attr('id').replace('task-', '');
		//var value = jQuery('td.content', tr).html();
		tr.html('<td colspan="3">' + jQuery('#hiddenTaskEditForm').html() + '</td>');

		jQuery('textarea', tr).keydown(function(e) {
			if (e.keyCode == 13) {
				save();
				return false;
			}
		});

		jQuery('*[name=datepickerSucks]', tr).datepicker({
			dateFormat: 'yy-mm-dd',
			showOn: 'none',
			constrainInput: false,
			showWeek: true,
			onSelect: function(date) {
				jQuery('*[name=task]', tr).val(this.cacheValue.replace(/ DUE:.*-.*-.* .*:.*:.*/g, '').replace(/ DUE:.*-.*-.* .*:.*/g, '').replace(/ DUE:.*-.*-.*/g, '') + ' DUE:' + date);
			},
			beforeShow: function(i) {
				this.cacheValue = jQuery('*[name=task]', tr).val();
				var m = this.cacheValue.match(/DUE:([0-9]*-[0-9]*-[0-9]*)/g);
				if (m) {
					m = m[0].replace('DUE:','');
					console.log(m);
					jQuery(this).datepicker('setDate', m);
				}
			},
			firstDay: 1
		});
		jQuery('.deadlineButton', tr).click(function() {
			jQuery('*[name=datepickerSucks]', tr).datepicker('show');
		});
		
		var cancel = function() {
			tr.html(cache);
			taskEditFormControls();
			jQuery('.swipeControls', tr).hide();
			jQuery('.content', tr).show();
		};
		jQuery('button[name=cancel]', tr).click(cancel);
		jQuery(document).keyup(function(e) {
			if (e.which == 27) {
				cancel();
		}
		});
		var value = jQuery.get('<?=$this->link->get('getItDone.ajax')?>/get-task-content/'+id, function(r) {
			jQuery('*[name=task]', tr).val(r);
		});
		var save = function() {
			jQuery('*[name=task]', tr).val();
			jQuery.post('<?=$this->link->get('getItDone.ajax')?>/change-task-content/'+id, {content: jQuery('*[name=task]', tr).val()}, function(r) {
				r = eval('(' + r + ')');
				tr.html(cache);
				jQuery('td.content', tr).html(r.content);
				jQuery('td.created', tr).html(r.created);
				taskEditFormControls();
				jQuery('.swipeControls', tr).hide();
				jQuery('.content', tr).show();
			});
		}
		jQuery('button[name=submit]', tr).click(save);
		jQuery('input', tr).keypress(function(e) {
			if(e.which == 13) {
				save();
			}
		});
	}
	function taskRemoveForm()
	{
		if (confirm('<?=§('Are you sure you want to delete this task?')?>')) {
			var tr = jQuery(this).parent().parent();
			var id = tr.attr('id').replace('task-', '');

			var value = jQuery.get('<?=$this->link->get('getItDone.ajax')?>/delete-task/'+id, function(r) {
				tr.remove();
			});
		}
	}
	function taskDoneForm()
	{
		var tr = jQuery(this).parent().parent();
		var id = tr.attr('id').replace('task-', '');
		var value = jQuery.get('<?=$this->link->get('getItDone.ajax')?>/set-task-done/'+id, function(r) {
			r = eval('(' + r + ')');
			tr.addClass('done');
			jQuery('.glyphicon-ok', tr).removeClass('glyphicon-ok').addClass('glyphicon-repeat');
			jQuery('.task-done', tr).removeClass('task-done').addClass('task-undone');
			jQuery('td.content', tr).html(r.content);
			jQuery('td.created', tr).html(r.created);
			taskEditFormControls();
			jQuery('.swipeControls', tr).hide();
			jQuery('.content', tr).show();
		});
	}
	function taskUndoneForm()
	{
		var tr = jQuery(this).parent().parent();
		var id = tr.attr('id').replace('task-', '');
		var value = jQuery.get('<?=$this->link->get('getItDone.ajax')?>/set-task-undone/'+id, function(r) {
			r = eval('(' + r + ')');
			tr.removeClass('done');
			jQuery('.glyphicon-repeat', tr).removeClass('glyphicon-repeat').addClass('glyphicon-ok');
			jQuery('.task-undone', tr).removeClass('task-undone').addClass('task-done');
			jQuery('td.content', tr).html(r.content);
			jQuery('td.created', tr).html(r.created);
			taskEditFormControls();
			jQuery('.swipeControls', tr).hide();
			jQuery('.content', tr).show();
		});
	}
	taskEditFormControls();
	jQuery('#listNewTask').triggeredAutocomplete({
		sources: {
			'+': '<?=$this->link->get('getItDone.ajax')?>/suggest-projects',
			'@': '<?=$this->link->get('getItDone.ajax')?>/suggest-contexts'
		}
	});
</script>

</div>
