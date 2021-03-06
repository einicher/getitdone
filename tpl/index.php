<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta charset="utf-8" />
		<title><?=$this->conf->name?></title>
		<!--[if lt IE 9]>
		<script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link rel="stylesheet" type="text/css" href="<?=$this->link->getRoot('scs-resources/bootstrap/css/bootstrap.min.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?=$this->link->getRoot('scs-resources/jquery-ui-git.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?=$this->link->getRoot('style.css')?>" />
		<script src="<?=$this->link->getRoot('scs-resources/jquery-1.8.3.min.js')?>" type="text/javascript"></script>
		<script src="<?=$this->link->getRoot('scs-resources/jquery-ui-git.js')?>" type="text/javascript"></script>
		<script src="<?=$this->link->getRoot('scs-resources/jquery.touchSwipe.min.js')?>" type="text/javascript"></script>
		<script src="<?=$this->link->getRoot('scs-components/get-it-done/tpl/triggered-autocomplete.js')?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?=$this->link->getRoot('scs-resources/bootstrap/js/bootstrap.min.js')?>"></script>
    	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    	<script type="text/javascript">
			function escapeHtml(text)
			{
				return text
					.replace(/&/g, "&amp;")
					.replace(/</g, "&lt;")
					.replace(/>/g, "&gt;")
					.replace(/"/g, "&quot;")
					.replace(/'/g, "&#039;");
			}
    		jQuery.datepicker.setDefaults({
    			firstDay: 1,
    			showWeek: 1,
    			dateFormat: 'yy-mm-dd',
    			prevText: '«',
    			nextText: '»',
				constrainInput: false
    		});
    	</script>
	</head>
	<body>

<? if (empty(Users::$user)) : ?>
		<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
			<div class="container">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?=$this->link->getRoot()?>"><span class="glyphicon glyphicon-ok" style="font-size: 15px;"></span> <?=$this->conf->name?></a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse navbar-ex1-collapse">
					<ul class="nav navbar-nav navbar-right">
						<li<?=$this->link->isActive('getItDone', true) ? ' class="active"' : '' ?>><a href="<?=$this->link->getRoot()?>"><span class="glyphicon glyphicon-home"></span> <?=§('Home')?></a></li>
						<li<?=$this->link->isActive('getItDone.syntax') ? ' class="active"' : '' ?>><a href="<?=$this->link->get('getItDone.syntax')?>"><span class="glyphicon glyphicon-barcode"></span> <?=§('Syntax')?></a></li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div>
		</nav>
		<div class="container home">

<? else : ?>
		<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
			<div class="container">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?=$this->link->getRoot()?>"><span class="glyphicon glyphicon-ok" style="font-size: 15px;"></span> <?=$this->conf->name?></a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse navbar-ex1-collapse">
					<ul class="nav navbar-nav">
						<li<?=$this->link->isActive('getItDone') ? ' class="active"' : '' ?>><a href="<?=$this->link->getRoot()?>"><span class="glyphicon glyphicon-home"></span> <?=§('Home')?></a></li>
						<li class="dropdown">
							<a href="<?=$this->link->get('getItDone.lists')?>" class="dropdown-toggle" style="display: inline-block; padding-right: 0;"><span class="glyphicon glyphicon-list"></span> <?=§('Lists')?></a><a href="#" class="dropdown-toggle" data-toggle="dropdown" style="display: inline-block; padding-left: 5px;">
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
<? foreach (GetItDone_Lists::getLists() as $list) : ?>
								<li><a href="<?=$this->link->get('getItDone.lists.list', $list->id)?>"><?=$list->name?></a></li>
<? endforeach; ?>
								<li class="divider"></li>
								<li><a href="<?=$this->link->get('getItDone.lists.create')?>"><span class="glyphicon glyphicon-plus"></span> <?=§('Create list')?></a></li>
								<li><a href="<?=$this->link->get('getItDone.lists.import')?>"><span class="glyphicon glyphicon-upload"></span> <?=§('Import list')?></a></li>
							</ul>
						</li>
						<li><a href="<?=$this->link->get('getItDone.projects')?>"><span class="glyphicon glyphicon-plus"></span> <?=§('Projects')?></a></li>
						<li><a href="<?=$this->link->get('getItDone.contexts')?>"><span>@</span> <?=§('Contexts')?></a></li>
						<li><a href="<?=$this->link->get('getItDone.syntax')?>"><span class="glyphicon glyphicon-barcode"></span> <?=§('Syntax')?></a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?=Users::$user->email?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=$this->link->get('users.preferences')?>"><?=§('Preferences')?></a></li>
								<li class="divider"></li>
								<li><a href="<?=$this->link->get('users.logout')?>"><span class="glyphicon glyphicon-log-out"></span> <?=§('Logout')?></a></li>
							</ul>
						</li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div>
		</nav>
		<div class="container">

<? endif; ?>

<?=$content?>

			<hr class="featurette-divider">

			<footer>
				<p class="pull-right"><a href="<?=$this->link->getRoot()?>"><?=§('Home')?></a> | <a href="#"><?=§('Back to top')?></a></p>
				<p>© <?=date('Y')?> <a href="http://einicher.net">einicher.net</a>
			</footer>
		</div>
	</body>
</html>
