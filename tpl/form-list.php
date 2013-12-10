<h1><?=§('Create List')?></h1>

<form method="post" action="" class="form">
	<div class="form-group">
		<label for="name"><?=§('Name')?></label>
		<input type="text" name="name" id="name" class="form-control" value="<?=@$list->name?>" />
	</div>
	<div class="form-group">
		<div class="">
			<input type="hidden" name="formListNonce" value="true" />
			<button type="submit" class="btn btn-default"><?=§('Save')?></button>
		</div>
	</div>
</form>
