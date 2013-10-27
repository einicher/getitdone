<h1><?=§('Import list')?></h1>

<? if ($error) : ?>
<div class="alert alert-danger">
	<?=$error?>
</div>
<? endif; ?>

<form method="post" action="" enctype="multipart/form-data">

	<div class="form-group">
		<label for="name"><?=§('Name')?></label>
		<input type="text" class="form-control" id="name" name="name" value="" />
	</div>

	<div class="form-group">
		<label for="file"><?=§('File')?></label>
		<input type="file" id="file" name="file" />
		<p class="help-block"><?=§('Select a %s formatted .txt file', '<a href="'.$this->link->get('getItDone.syntax').'">todo.txt</a>')?></p>
	</div>

	<button type="submit" class="btn btn-default"><?=§('Import')?></button>
	<input type="hidden" name="listImportNonce" value="true" />

</form>
