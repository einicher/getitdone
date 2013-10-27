<h1><?=§('Delete list %s', $list->name)?></h1>
<br />

<? if ($error) : ?>
<div class="alert alert-danger">
	<?=$error?>
</div>
<? endif; ?>

<form method="post" action="">
	<div class="radio">
		<label>
			<input type="radio" name="choice" id="choice1" value="1" />
			<?=§('Delete list and delete all assigned to dos')?>
		</label>
	</div>
	<div class="radio">
		<label>
			<input type="radio" name="choice" id="choice2" value="2" />
			<?=§('Delete list and keep all assigned to dos')?>
		</label>
	</div>
	<br />
	<button type="submit" class="btn btn-danger btn-lg"><?=§('Delete')?></button>
	<input type="hidden" name="listDeleteNonce" value="true" />
</form>
