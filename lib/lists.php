<?php
	class GetItDone_Lists extends Scs_Core
	{
		static $instance;

		static function instance()
		{
			if (empty(self::$instance)) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function lists($levels, $level)
		{
			$lists = self::getLists();	
			return $this->t->get('lists.php', array(
				'lists' => $lists
			));
		}
		
		static function getLists()
		{
			return Scs_Database::instance()->get('SELECT * FROM `#_lists` WHERE uid='.Users::$user->id.' ORDER BY name DESC');
		}

		function getList($levels, $level)
		{
			$list = new GetItDone_List(array('id' => $levels[2]));
			if ($list->uid != Users::$user->id) {
				return §('This list does not belong to you.');
			}
			if (isset($levels[3]) && $levels[3] == $this->link->getAssignment('getItDone.lists.list.export', 'link')) {
				return $list->export();
			}
			if (isset($levels[3]) && $levels[3] == $this->link->getAssignment('getItDone.lists.list.delete', 'link')) {
				$error = '';
				if (isset($_POST['listDeleteNonce'])) {
					if (empty($_POST['choice'])) {
						$error = §('Please make a choice.');
					} else {
						if ($_POST['choice'] == 1) { //delete todos
							$this->d->query('DELETE FROM `#_tasks` WHERE list='.$levels[2]);
						} elseif ($_POST['choice'] == 2) { // move to zero
							$this->d->query('UPDATE `#_tasks` SET list=0 WHERE list='.$levels[2]);
						}
						$this->d->query('DELETE FROM `#_lists` WHERE id='.$levels[2]);
						header('Location: '.$this->link->get('getItDone.lists'));
						exit;
					}
				}
				
				return $this->t->get('list-delete.php', array(
					'list' => $list,
					'error' => $error
				));
			}
			return $list->view($levels, $level);
		}

		function createList()
		{
			if (!empty($_POST['formListNonce'])) {
				$this->d->prepared('INSERT INTO `#_lists` SET name=?, uid=?, created=?', 'sii', $_POST['name'], Users::$user->id, time());
				header('Location: '.$this->link->get('getItDone.lists.list', $this->d->insert_id));
				exit;
			}
			return $this->t->get('form-list.php');
		}

		function editList($levels, $level)
		{
			if (!empty($_POST['formListNonce'])) {
				$this->d->prepared('UPDATE `#_lists` SET name=? WHERE id=?', 'si', $_POST['name'], $levels[2]);
				header('Location: '.$this->link->get('getItDone.lists.list', $levels[2]));
				exit;
			}
			$lists = $this->d->get('SELECT * FROM `#_lists` WHERE id='.$levels[2]);
			return $this->t->get('form-list.php', array(
				'list' => $lists[0]
			));
		}

		function hash($levels, $level)
		{
			if (empty($levels[2])) {
				return 'Hash needed.';
			} else {
				$l = $this->d->prepared('SELECT id FROM `#_lists` WHERE hash=?', 's', $levels[2]);
				if (empty($l)) {
					return §('Hash %s not found. Maybe the link was broken or the owner reset the link.', '<b>'.$levels[2].'</b>');
				} else {
					$list = new GetItDone_List(array('id' => $l->id));
					return $list->view($levels, $level, true);
				}
			}
		}

		function share($levels, $level)
		{
			$list = $this->d->prepared('SELECT * FROM `#_lists` WHERE id=?', 'i', $levels[2]);
			if (empty($list->hash) || isset($_GET['reset'])) {
				$list->hash = uniqid();
				$this->d->prepared('UPDATE `#_lists` SET hash=? WHERE id=?', 'si', $list->hash, $levels[2]);
				if (isset($_GET['reset'])) {
					header('Location: '.$this->link->getCurrent());
				}
			}
			return $this->t->get('list-share.php', array(
				'list' => $list
			));
		}

		function import($levels, $level)
		{
			$error = '';

			if (isset($_POST['listImportNonce'])) {
				if (empty($_FILES['file']) || $_FILES['file']['error'] === 4) {
					$error = §('Please select a file.');
				} elseif ($_FILES['file']['error'] !== 0) {
					$error = §('An error occured during file upload, please try again.');
				} else {
					if (empty($_POST['name'])) {
						$_POST['name'] = §('List imported %s', §('on %s at %s', date(§('Y-m-d')), date(§('H:i'))));
					}

					$this->d->prepared('INSERT INTO `#_lists` SET name=?, uid=?, created=?', 'sii', $_POST['name'], Users::$user->id, time());
					$list = $this->d->insert_id;

					$fs = fopen($_FILES['file']['tmp_name'], 'r');
					while (($line = fgets($fs)) !== FALSE) {
						GetItDone_Api::saveTask(trim($line), 0, $list);
					}
					fclose($fs);

					header('Location: '.$this->link->get('getItDone.lists.list', $list));
					exit;
				}
			}
			
			return $this->t->get('list-import.php', array(
				'error' => $error
			));
		}
	}
?>
