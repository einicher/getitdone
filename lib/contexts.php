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
	}
?>
