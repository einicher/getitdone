<?php
	class GetItDone_Api extends Scs_Core
	{
		static $instance;

		static function instance()
		{
			if (empty(self::$instance)) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function login($email, $password)
		{
			if (Users::login($email, $password)) {
				return array('token' => encrypt(Users::$user->id.':::::'.Users::$user->password));
			} else {
				return array('error' => 'Wrong email and/or password.');
			}
		}

		static public function saveTask($content, $id = 0, $list = 0)
		{
			$d = Scs_Database::instance();
			$projects = GetItDone_List::detectProjects($content);
			$contexts = GetItDone_List::detectContexts($content);
			$priority = GetItDone_List::detectPriority($content);
			$due = GetItDone_List::detectDue($content);
			$created = GetItDone_List::detectCreated($content);
			$content = $created[0];
			$done = $created[2];
			$created = $created[1];
			if (empty($id)) {
				$e = $d->prepared('INSERT INTO `#_tasks` SET content=?, projects=?, contexts=?, priority=?, due=?, created=?, uid=?, list=?, done=?', 'ssssssiis', $content, $projects, $contexts, $priority, $due, $created, Users::$user->id, $list, $done);
				$id = $d->insert_id;
			} else {
				$d->prepared('UPDATE `#_tasks` SET content=?, projects=?, contexts=?, priority=?, due=?, created=?, done=? WHERE id=?', 'sssssssi', $content, $projects, $contexts, $priority, $due, $created, $done, $id);
			}
			return (object) array(
				'content' => $content,
				'created' => $created,
				'projects' => $projects,
				'contexts' => $contexts,
				'priority' => $priority,
				'due' => $due,
				'id' => $id
			);
		}
	}
?>
