<h1><?=§('Rename project %s', '<span class="label label-info">'.$project.'</span>')?></h1>
<br />
<form method="post" action="" class="form">
	<div class="form-group">
		<label for="name"><?=§('All +%s project tags in all tasks shall be renamed to:', $project)?></label>
		<input type="text" name="name" id="name" class="form-control" value="" />
	</div>
	<div class="form-group">
		<div class="">
			<input type="hidden" name="formProjectRenameNonce" value="true" />
			<button type="submit" class="btn btn-default"><?=§('Rename')?></button>
		</div>
	</div>
</form>
