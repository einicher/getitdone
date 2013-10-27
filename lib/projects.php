<?php
	class GetItDone_Projects extends Scs_Core
	{
		static $instance;

		static function instance()
		{
			if (empty(self::$instance)) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function projects($levels, $level)
		{
			$projects = array();
			$tasks = $this->d->get('SELECT projects FROM `#_tasks` WHERE uid='.Users::$user->id.'');
			foreach ($tasks as $task) {
				$ps = explode(',', $task->projects);
				foreach ($ps as $p) {
					if (!empty($p)) {
						@$projects[$p] += 1;
					}
				}
			}
			ksort($projects);
			return $this->t->get('projects.php', array(
				'projects' => $projects
			));
		}

		function project($levels, $level)
		{
			$list = new GetItDone_List(array('project' => $levels[2]));
			if (isset($levels[3]) && $levels[3] == $this->link->getAssignment('getItDone.projects.project.export', 'link')) {
				return $list->export();
			}
			return $list->view($levels, $level);
		}
	}
?>
