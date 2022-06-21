<?php
jsonpage::start();

try {
	$USER->start();
	$webplug = new WebPlug($USER);

	switch ($PAGE->ext[0]) {
		case '':
		case 'list':
			$data = ['data' => $webplug->getList()];
			break;
		case 'all':
			$data = ['data' => $webplug->getAll()];
			break;
		case 'export':
			if (!empty($_POST['id'])) {
				$id = $_POST['id'];
			} elseif (!empty($_GET['id'])) {
				$id = $_GET['id'];
			} elseif (!empty($PAGE->ext[1])) {
				$id = $PAGE->ext[1];
			}

			if (!$id) {
				$data = ['data' => $webplug->getAll()];
				$basename = '网页插件-全部导出_'.date('Y-m-d_H-i-s').'.json';
			} else {
				$data = ['data' => $webplug->get((int)$id)];
				$basename = '网页插件-'.$data['data']['name'].'_'.date('Y-m-d_H-i-s').'.json';
			}

			header("Content-Disposition: attachment; filename=\"$basename\"; filename*=utf-8''$basename");
			break;
		case 'get':
			if (!empty($_POST['id'])) {
				$id = $_POST['id'];
			} elseif (!empty($_GET['id'])) {
				$id = $_GET['id'];
			} elseif (!empty($PAGE->ext[1])) {
				$id = $PAGE->ext[1];
			} else {
				throw new Exception('id不能为空');
			}
			$data = ['data' => $webplug->get((int)$id)];
			break;
		case 'set_load_order':
			if (isset($_POST['data'])) {
				$orderArray = json_decode($_POST['data'], true);
			} else {
				$orderArray = json_decode(file_get_contents('php://input'), true);
			}
			if (empty($orderArray)) {
				throw new Exception('加载顺序列表为空');
			}
			$data = ['updated' => $webplug->setLoadOrder($orderArray)];
			break;
		case 'update':
			if (empty($_POST['id'])) {
				throw new Exception('id不能为空');
			}
			$data = ['updated' => $webplug->update((int)$_POST['id'], $_POST['name'], $_POST['content'])];
			break;
		case 'enable':
			if (empty($_POST['id'])) {
				throw new Exception('id不能为空');
			}
			$data = ['updated' => $webplug->enable((int)$_POST['id'], (bool)$_POST['enabled'])];
			break;
		case 'add':
			$data = ['newId' => $webplug->add((int)$_POST['load_order'],
				(bool)$_POST['enabled'], $_POST['name'], $_POST['content'],
				(int)$_POST['author_uid'], str::word($_POST['webplug_id']))];
			break;
		case 'import':
			if (isset($_POST['data'])) {
				$json = json_decode($_POST['data'], true);
			} else {
				$json = json_decode(file_get_contents('php://input'), true);
			}
			if (empty($json)) {
				throw new Exception('导入数据为空');
			}
			$data = ['updated' => $webplug->import($json)];
			break;
		case 'delete':
			if (empty($_POST['id'])) {
				throw new Exception('id不能为空');
			}
			$data = ['updated' => $webplug->delete((int)$_POST['id'])];
			break;
		case 'html':
			echo $webplug->getHTML();
			exit;
		default:
			throw new Exception("未知操作：{$PAGE->ext[0]}");
	}

	jsonpage::output([
		'success'=>true,
		'islogin'=>$USER->islogin,
	] + $data);
}
catch (Exception $e) {
	jsonpage::output([
		'success'=>false,
		'islogin'=>$USER->islogin,
		'errmsg'=>$e->getMessage()
	]);
}
