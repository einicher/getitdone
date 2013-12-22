<?php
	class GetItDone_List extends Scs_Core
	{
		public $id = 0;
		public $uid;
		public $name;
		private $args = array();
		public $isProject = false;
		public $isContext = false;

		function __construct($args = array())
		{
			foreach ($args as $key => $value) {
				$this->$key = $value;
			}
			if (!empty($this->id)) {
				$list = $this->d->get('SELECT * FROM `#_lists` WHERE id='.$this->id);
				foreach ($list[0] as $key => $value) {
					$this->$key = $value;
				}
			}
			if (empty($this->uid)) {
				$this->uid = Users::$user->id;
			}
			if (isset($this->project)) {
				$this->project = urldecode($this->project);
				$this->isProject = true;
				$this->name = §('Project %s', $this->project);
			} elseif (isset($this->context)) {
				$this->context = urldecode($this->context);
				$this->isContext = true;
				$this->name = §('Context %s', $this->context);
			} else {
				if (empty($this->name)) {
					$this->name = §('Home');
				}
			}
		}

		function getTasks()
		{
			$sql = '';

			if (!empty($this->project)) {
				$sql .= ' && FIND_IN_SET("'.$this->d->escape($this->project).'", projects)';
			}
			if (!empty($this->context)) {
				$sql .= ' && FIND_IN_SET("'.$this->d->escape($this->context).'", contexts)';
			}
			if (!empty($this->id)) {
				$sql .= ' && list='.$this->id;
			}
			if (empty(Users::$user->settings->status)) {
				$sql .= ' && done="0000-00-00 00:00:00"';
			} elseif (Users::$user->settings->status == 2) {
			
			} else {
				$sql .= ' && done!="0000-00-00 00:00:00"';
			}
			if (!empty($_GET['projects'])) {
				$projects = preg_split('/,/', $_GET['projects'], -1, PREG_SPLIT_NO_EMPTY);
				foreach ($projects as $project) {
					$sql .= ' && FIND_IN_SET("'.$this->d->escape($project).'", projects)';
				}
			}
			if (!empty($_GET['contexts'])) {
				$contexts = preg_split('/,/', $_GET['contexts'], -1, PREG_SPLIT_NO_EMPTY);
				foreach ($contexts as $context) {
					$sql .= ' && FIND_IN_SET("'.$this->d->escape($context).'", contexts)';
				}
			}
			$sql = 'SELECT *,IF(priority="","ZZ",priority) orderPrio,IF(due=0,"9999-12-31 00:00:00",due) orderDue FROM `#_tasks` WHERE uid='.$this->uid.$sql.' ORDER BY orderPrio ASC, `orderDue` ASC, `created` DESC';
			//echo µ($sql);

			$tasks = $this->d->get($sql);

			if (empty($tasks)) {
				$tasks = array();
			} elseif (!is_array($tasks)) {
				$tasks = array($tasks);
			}
			return $tasks;
		}

		function view($levels, $level, $readonly = true)
		{
			$lnt = '';
			$export = '';
			if (isset($levels[1]) && $levels[1] == SCS_Link::getAssignment('getItDone.projects', 'link')) {
				$lnt = '+'.urldecode($levels[2]).' ';
				$export = $this->link->get('getItDone.projects.project.export', $this->project);
			} elseif (isset($levels[1]) && $levels[1] == SCS_Link::getAssignment('getItDone.contexts', 'link')) {
				$lnt = '@'.urldecode($levels[2]).' ';
				$export = $this->link->get('getItDone.contexts.context.export', $this->context);
			} elseif (empty($this->id)) {
				$export = $this->link->get('getItDone.export');
			} else {
				$export = $this->link->get('getItDone.lists.list.export', $this->id);
			}
			
			if (!empty($_POST['listNewTask'])) {
				$task = GetItDone_Api::saveTask($_POST['listNewTask'], 0, $this->id);
			}

			return $this->t->get('list.php', array(
				'tasks' => $this->getTasks(),
				'list' => &$this,
				'listNewTask' => $lnt,
				'export' => $export,
				'readonly' => $readonly 
			));
		}

		static public function detectProjects($t)
		{
			$return = '';
			preg_match_all('/\+([^\s]+)/', $t, $r);
			if (!empty($r[1])) {
				$return = implode(',', $r[1]);
			}
			return $return;
		}

		static public function detectContexts($t)
		{
			$return = '';
			preg_match_all('/\@([^\s]+)/', $t, $r);
			if (!empty($r[1])) {
				$return = implode(',', $r[1]);
			}
			return $return;
		}

		static public function detectPriority($t)
		{
			$return = '';
			if (substr($t, 0, 1) == '(' && substr($t, 2, 1) == ')') {
				$return = strtoupper(substr($t, 1, 1));
			}
			return $return;
		}

		static public function detectDue($t)
		{
			$return = '';
			preg_match('/DUE:([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', $t, $r);
			if (!empty($r)) {
				$return = $r[1].' '.$r[2];
			} else {
				preg_match('/DUE:([0-9]*-[0-9]*-[0-9]*)/', $t, $r);
				if (!empty($r)) {
					$return = $r[1].' 00:00:00';
				}
			}
			return $return;
		}

		static public function removeDoneTag($t)
		{
			return trim(preg_replace('/DONE:([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', '', $t));
		}

		static public function detectDone($t, $set = false)
		{
			$x = false;
			$done = '';
			preg_match('/^(x) ([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', $t, $r);
			if (!empty($r)) {
				$x = true;
				$done = $r[2].' '.$r[3];
				$t = preg_replace('/^(x) ([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', '', $t);
			} else {
				preg_match('/^(x) ([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*)/', $t, $r);
				if (!empty($r)) {
					$x = true;
					$done = $r[2].' '.$r[3];
					$t = preg_replace('/^(x) ([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*)/', '', $t);
				} else {
					preg_match('/^(x) ([0-9]*-[0-9]*-[0-9]*)/', $t, $r);
					if (!empty($r)) {
						$x = true;
						$done = $r[2].' 00:00:00';
						$t = preg_replace('/^(x) ([0-9]*-[0-9]*-[0-9]*)/', '', $t);
					} else {
						preg_match('/^(x) /', $t, $r);
						if (!empty($r)) {
							$x = true;
							$done = '';
							$t = preg_replace('/^(x) /', '', $t);
						} else {

						}
					}
				}
			}

			if ($set) {
				if (empty($done)) {
					$done = date('Y-m-d').' '.date('H:i:s');
					$x = true;
				}
				$content = 'x '.$done.' '.$t;
			} else {
				$content = $t;
			}

			return array(trim($content), $done, $x);
		}

		static public function detectCreated($t, $customStamp = '')
		{
			$done = self::detectDone($t, false);
			$t = $done[0];
			$content = $t;

			preg_match('/^([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', $t, $r);
			if (!empty($r)) {
				$created = $r[1].' '.$r[2];
			} else {
				preg_match('/^([0-9]*-[0-9]*-[0-9]*)/', $t, $r);
				if (!empty($r)) {
					$created = $r[1].' 00:00:00';
				} else {
					preg_match('/^\([A-Z]\) ([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', $t, $r);
					if (!empty($r)) {
						$created = $r[1].' '.$r[2];
					} else {
						preg_match('/^\([A-Z]\) ([0-9]*-[0-9]*-[0-9]*)/', $t, $r);
						if (!empty($r)) {
							$created = $r[1].' 00:00:00';
						} else {
							$created = empty($customStamp) ? date('Y-m-d H:i:s') : $customStamp;
							if (substr($content, 0, 1) == '(' && substr($content, 2, 1) == ')') {
								$content = substr($content, 0, 3).' '.$created.' '.trim(substr($content, 3));
							} else {
								$content = $created.' '.$content;
							}
						}
					}
				}
			}

			if (!empty($done[2])) {
				if (!empty($done[1])) {
					$content = $done[1].' '.$content;
				}
				$content = 'x '.$content;
			}

			return array($content, $created, $done[1]);
		}

		static public function detectSyntax($text)
		{
			$text = trim($text);
			$text = preg_replace('/^(x) ([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', '<span class="label label-default" title="'.§('Done on %s at %s', '\\2', '\\3').'">x \\2 \\3</span>', $text);
			$text = preg_replace('/^(x) ([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*)/', '<span class="label label-default" title="'.§('Done on %s at %s', '\\2', '\\3').'">x \\2 \\3</span>', $text);
			$text = preg_replace('/^(x) ([0-9]*-[0-9]*-[0-9]*)/', '<span class="label label-default" title="'.§('Done on %s', '\\2').'">x \\2</span>', $text);
			$text = preg_replace('/^([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', '', $text);
			$text = preg_replace('/^([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*)/', '', $text);
			$text = preg_replace('/^([0-9]*-[0-9]*-[0-9]*)/', '', $text);
			$text = preg_replace('/^(\([A-Z]\)*) ([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', '\\1 ', $text);
			$text = preg_replace('/^(\([A-Z]\)*) ([0-9]*-[0-9]*-[0-9]*)/', '\\1 ', $text);
			$text = preg_replace('/@([^\s]+)/', '<a href="'.Scs_Link::instance()->get('getItDone.contexts.context', '\\1').'" class="label label-warning" title="'.§('Context').'">\\1</a>', $text);
			$text = preg_replace('/\+([^\s]+)/', '<a href="'.Scs_Link::instance()->get('getItDone.projects.project', '\\1').'" class="label label-info" title="'.§('Project').'">\\1</a>', $text);
			$text = preg_replace('/^\(([A-Z])\)/', '<a href="" class="label label-danger" title="'.§('Priority').'">\\1</a>', $text);
			$text = preg_replace('/DONE:([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', '<span class="label label-default" title="'.§('Done on %s at %s', '\\1', '\\2').'">\\1 \\2</span>', $text);
			$text = preg_replace('/DONE:([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*)/', '<span class="label label-default" title="'.§('Done on %s at %s', '\\1', '\\2').'">\\1 \\2</span>', $text);
			$text = preg_replace('/DONE:([0-9]*-[0-9]*-[0-9]*)/', '<span class="label label-default" title="'.§('Done on %s', '\\1').'">\\1', $text);
			$text = preg_replace('/DUE:([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*:[0-9]*)/', '<span class="label label-danger" title="'.§('Due on %s at %s', '\\1', '\\2').'">\\1 \\2</span>', $text);
			$text = preg_replace('/DUE:([0-9]*-[0-9]*-[0-9]*) ([0-9]*:[0-9]*)/', '<span class="label label-danger" title="'.§('Due on %s at %s', '\\1', '\\2').'">\\1 \\2</span>', $text);
			$text = preg_replace('/DUE:([0-9]*-[0-9]*-[0-9]*)/', '<span class="label label-danger" title="'.§('Due on %s', '\\1', '\\2').'">\\1</span>', $text);
			$text = preg_replace('/DUE:([0-9]*) ([0-9]*)/', '<span class="label label-danger" title="'.§('Due on %s at %s', '\\1', '\\2').'">\\1 \\2</span>', $text);
			$text = preg_replace('/DUE:([0-9]*)/', '<span class="label label-danger" title="'.§('Due on %s at %s', '\\1', '\\2').'">\\1 \\2</span>', $text);
			return $text;
		}

		function export()
		{
			header('content-type: application/octet-stream');
			header('content-disposition: attachment; filename="'.$this->link->transform($this->name).'-'.date('Ymd-His').'.txt"');
			ob_implicit_flush(true);
			$tasks = $this->getTasks();
			foreach ($tasks as $t) {
				echo trim($t->content)."\n";
			}
			exit;
		}
	}
?>
