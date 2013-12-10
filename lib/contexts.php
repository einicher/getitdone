<?php
	class GetItDone_Contexts extends Scs_Core
	{
		static $instance;

		static function instance()
		{
			if (empty(self::$instance)) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function contexts($levels, $level)
		{
			$contexts = array();
			$tasks = $this->d->get('SELECT contexts FROM `#_tasks` WHERE uid='.Users::$user->id.'');
			foreach ($tasks as $task) {
				$ps = explode(',', $task->contexts);
				foreach ($ps as $p) {
					if (!empty($p)) {
						@$contexts[$p] += 1;
					}
				}
			}
			ksort($contexts);
			return $this->t->get('contexts.php', array(
				'contexts' => $contexts
			));
		}

		function context($levels, $level)
		{
			$list = new GetItDone_List(array('context' => $levels[2]));
			if (isset($levels[3]) && $levels[3] == $this->link->getAssignment('getItDone.contexts.context.export', 'link')) {
				return $list->export();
			}
			return $list->view($levels, $level);
		}

		function rename($levels, $level)
		{
			$context = urldecode($levels[2]);
			if (!empty($_POST['formContextRenameNonce'])) {
				$new = $_POST['name'];
				$q = $this->d->query('SELECT * FROM `#_tasks` WHERE FIND_IN_SET("'.$this->d->escape($context).'", contexts)');
				while ($f = $q->fetch_object()) {
					echo µ($f);
					$f->content = preg_replace('/\@'.$context.'/iU', '@'.$new, $f->content);
					$f->contexts = explode(',', $f->contexts);
					foreach ($f->contexts as $k => $v) {
						if (strtolower($v) == strtolower($context)) {
							$f->contexts[$k] = $new;
						}
					}
					$f->contexts = implode(',', $f->contexts);
					$this->d->prepared('UPDATE `#_tasks` SET content=?, contexts=? WHERE id=?', 'ssi', $f->content, $f->contexts, $f->id);
				}
				header('Location: '.$this->link->get('getItDone.contexts.context', urlencode($new)));
				exit;
			}
			return $this->t->get('context-rename.php', array(
				'context' => $context
			));
		}
	}
?>
