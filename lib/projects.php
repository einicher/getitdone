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

		function rename($levels, $level)
		{
			$project = urldecode($levels[2]);
			if (!empty($_POST['formProjectRenameNonce'])) {
				$new = $_POST['name'];
				$q = $this->d->query('SELECT * FROM `#_tasks` WHERE FIND_IN_SET("'.$this->d->escape($project).'", projects)');
				while ($f = $q->fetch_object()) {
					$f->content = preg_replace('/\+'.$project.'/iU', '+'.$new, $f->content);
					$f->projects = explode(',', $f->projects);
					foreach ($f->projects as $k => $v) {
						if (strtolower($v) == strtolower($project)) {
							$f->projects[$k] = $new;
						}
					}
					$f->projects = implode(',', $f->projects);
					$this->d->prepared('UPDATE `#_tasks` SET content=?, projects=? WHERE id=?', 'ssi', $f->content, $f->projects, $f->id);
				}
				header('Location: '.$this->link->get('getItDone.projects.project', urlencode($new)));
				exit;
			}
			return $this->t->get('project-rename.php', array(
				'project' => $project
			));
		}
	}
?>
