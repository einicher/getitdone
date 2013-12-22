<h1><?=§('Share list %s', '<a href="'.$this->link->get('getItDone.lists.list', $list->id).'" class="label label-success">'.$list->name.'</a>')?></h1>
<p><?=§('Use the link below to share this list with others.')?></p>

<h2><?=§('Link')?></h2>
<a class="lg" href="<?=$this->link->getHome().str_replace('../', '', $this->link->get('getItDone.hash.hash', $list->hash))?>" target="_blank"><?=$this->link->getHome().str_replace('../', '', $this->link->get('getItDone.hash.hash', $list->hash))?></a>

<h2><?=§('Reset link')?></h2>
<p><?=§('To restrict access again, you can reset the link.')?></p>
<a class="btn btn-danger btn-lg" href="?reset"><?=§('Reset link')?></a>
