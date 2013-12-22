<h1><?=§('Rename context %s', '<span class="label label-warning">'.$context.'</span>')?></h1>
<br />
<form method="post" action="" class="form">
	<div class="form-group">
		<label for="name"><?=§('All @%s context tags in all tasks shall be renamed to:', $context)?></label>
		<input type="text" name="name" id="name" class="form-control" value="" />
	</div>
	<div class="form-group">
		<div class="">
			<input type="hidden" name="formContextRenameNonce" value="true" />
			<button type="submit" class="btn btn-default"><?=§('Rename')?></button>
		</div>
	</div>
</form>
