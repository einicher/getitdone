<?php
	class GetItDone extends Scs_Component
	{
		function __construct()
		{
			if (!$this->d->checkForTable('tasks')) {
				$this->d->query('
					CREATE TABLE `#_tasks` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`uid` int(11) NOT NULL,
						`list` int(11) NOT NULL,
						`priority` varchar(1) NOT NULL,
						`created` datetime NOT NULL,
						`content` text NOT NULL,
						`projects` varchar(255) NOT NULL,
						`contexts` varchar(255) NOT NULL,
						`due` datetime NOT NULL,
						`done` datetime NOT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;
				');
			}

			if (!$this->d->checkForTable('lists')) {
				$this->d->query('
					CREATE TABLE `#_lists` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`uid` int(11) NOT NULL,
						`name` varchar(255) NOT NULL,
						`created` int(11) NOT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;
				');
			}

			$this->link->assign('getItDone', '', array(&$this, 'index'));
			$this->link->assign('getItDone.export', 'export', array(&$this, 'index'), 'getItDone', true);

			$this->link->assign('getItDone.projects', 'projects', array('GetItDone_Projects::instance', 'projects'), 'getItDone');
			$this->link->assign('getItDone.projects.project', '*', array('GetItDone_Projects::instance', 'project'), 'getItDone.projects');
			$this->link->assign('getItDone.projects.project.export', 'export', array('GetItDone_Projects::instance', 'project'), 'getItDone.projects.project');
			$this->link->assign('getItDone.projects.project.edit', 'export', array('GetItDone_Projects::instance', 'edit'), 'getItDone.projects.project');

			$this->link->assign('getItDone.contexts', 'contexts', array('GetItDone_Contexts::instance', 'contexts'), 'getItDone');
			$this->link->assign('getItDone.contexts.context', '*', array('GetItDone_Contexts::instance', 'context'), 'getItDone.contexts');
			$this->link->assign('getItDone.contexts.context.export', 'export', array('GetItDone_Contexts::instance', 'context'), 'getItDone.contexts.context');

			$this->link->assign('getItDone.lists', 'lists', array('GetItDone_Lists::instance', 'lists'), 'getItDone');
			$this->link->assign('getItDone.lists.create', 'create', array('GetItDone_Lists::instance', 'createList'), 'getItDone.lists');
			$this->link->assign('getItDone.lists.import', 'import', array('GetItDone_Lists::instance', 'import'), 'getItDone.lists');
			$this->link->assign('getItDone.lists.list', '*', array('GetItDone_Lists::instance', 'getList'), 'getItDone.lists');
			$this->link->assign('getItDone.lists.list.export', 'export', array('GetItDone_Lists::instance', 'getList'), 'getItDone.lists.list');
			$this->link->assign('getItDone.lists.list.delete', 'delete', array('GetItDone_Lists::instance', 'getList'), 'getItDone.lists.list');

			$this->link->assign('getItDone.syntax', 'syntax', array(&$this, 'syntax'), 'getItDone');

			$this->link->assign('getItDone.api', 'api', array(&$this, 'api'), '', true);
			$this->link->assign('getItDone.ajax', 'get-it-done-ajax-api', array(&$this, 'getAjax'), '', true);

			$this->o->connect('scs.runLoop', 'runLoop', $this);

			if (isset(Users::$user->settings->language)) {
				Scs_Language::$language = Users::$user->settings->language;
			}
		}

		function runLoop($return, $path)
		{
			if (empty(Users::$user->id) && substr($path, 0, 3) != 'api' && substr($path, 0, 8) != 'password') {
				return false;
			} else {
				return true;
			}
		}

		function processOutput($content, $levels, $level)
		{
			if (empty(Users::$user)
			 && $levels[0] != Scs_Link::$assignments['users.preferences.password']['link']
			 && $levels[0] != Scs_Link::$assignments['getItDone.api']['link']
			) {
				$signUp = '';
				$login = '';

				if (!empty($_POST['signUpEmail'])) {
					$signUp = Users::signUp($_POST['signUpEmail']);
				}
				if (!empty($_POST['loginEmail']) && !empty($_POST['loginPassword'])) {
					$login = Users::doLogin($_POST['loginEmail'], $_POST['loginPassword'], empty($_POST['loginRemember']) ? false : true);
				}

				$content = $this->t->get('login.php', array(
					'processor' => &$this,
					'signUp' => $signUp,
					'login' => $login
				));
			} else {
				
			}
			return $this->t->get('index.php', array(
				'content' => $content,
				'processor' => &$this
			));
		}

		function index($levels, $level)
		{
			$list = new GetItDone_List;
			if (isset($levels[1]) && $levels[1] == $this->link->getAssignment('getItDone.export', 'link')) {
				return $list->export();
			}

			return $this->t->get('user.php', array(
				'processor' => &$this,
				'content' => $list->view($levels, $level)
			));
		}

		function syntax($levels, $level)
		{
			return $this->t->get('syntax.php');
		}

		function getAjax($levels, $level)
		{
			if (!empty($levels[2])) {
				switch ($levels[2]) {
					case 'get-task-content':
						if (!empty($levels[3])) {
							$task = $this->d->prepared('SELECT * FROM `#_tasks` WHERE id=?', 'i', $levels[3]);
							return $task->content;
						}
					break;
					case 'change-task-content':
						if (!empty($levels[3])) {
							$task = GetItDone_Api::saveTask($_POST['content'], $levels[3]);
							return json_encode(array(
								'content' => GetItDone_List::detectSyntax($task['content']),
								'created' => $task['created']
							));
						}
					break;
					case 'delete-task':
						if (!empty($levels[3])) {
							$this->d->prepared('DELETE FROM `#_tasks` WHERE id=?', 'i', $levels[3]);
						}
					break;
					case 'set-task-done':
						if (!empty($levels[3])) {
							$this->d->prepared('UPDATE `#_tasks` SET done=NOW(), content=CONCAT(content, " DONE:", NOW()) WHERE id=?', 'i', $levels[3]);
							$task = $this->d->prepared('SELECT * FROM `#_tasks` WHERE id=?', 'i', $levels[3]);
							return json_encode(array(
								'content' => GetItDone_List::detectSyntax($task->content),
								'created' => $task->created
							));
						}
					break;
					case 'set-task-undone':
						if (!empty($levels[3])) {
							$task = $this->d->prepared('SELECT * FROM `#_tasks` WHERE id=?', 'i', $levels[3]);
							$content = GetItDone_List::removeDoneTag($task->content);
							$this->d->prepared('UPDATE `#_tasks` SET done=0, content=? WHERE id=?', 'si', $content, $levels[3]);
							return json_encode(array(
								'content' => GetItDone_List::detectSyntax($content),
								'created' => $task->created
							));
						}
					break;
					case 'suggest-projects':
						$projects = $this->d->prepared('SELECT * FROM `#_tasks` WHERE uid=? && projects REGEXP CONCAT("(^|,)", ?, ".+(,|$)")', 'is', Users::$user->id, $_GET['term']);
						$pp = array();
						foreach ($projects as $p) {
							foreach (explode(',', $p->projects) as $key => $value) {
								if (strpos($value, $_GET['term']) !== false) {
									if (!in_array($value, $pp)) {
										$pp[] = $value;
									}
								}
							}
						}
						return json_encode($pp);
					break;
					case 'suggest-contexts':
						$contexts = $this->d->prepared('SELECT * FROM `#_tasks` WHERE uid=? && contexts REGEXP CONCAT("(^|,)", ?, ".+(,|$)")', 'is', Users::$user->id, $_GET['term']);
						$cc = array();
						foreach ($contexts as $c) {
							foreach (explode(',', $c->contexts) as $key => $value) {
								if (strpos($value, $_GET['term']) !== false) {
									if (!in_array($value, $cc)) {
										$cc[] = $value;
									}
								}
							}
						}
						return json_encode($cc);
					break;
				}
			}
		}
		
		function api($levels, $level)
		{
			header('Content-type: application/json; charset=UTF-8');
			$output = array('error' => 'No section selected.');
			if (!empty($levels[1])) {
				$api = GetItDone_Api::instance();
				switch ($levels[1]) {
					case 'login':
						if (empty($_POST['email']) || empty($_POST['password'])) {
							$output = array('error' => 'Fields email, password left empty.');
						} else {
							$output = $api->login($_POST['email'], $_POST['password']);
						}
					break;
					default:
						$output = array('error' => 'Unknown section “'.$levels[1].'”.');
				}
			}
			return json_encode(array_merge(array(
				'version' => '1.0',
				'time' => microtime(true)
			), $output));
		}
	}
?>
