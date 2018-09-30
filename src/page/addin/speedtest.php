<?php
$testSites = [
    //'main' => ['name'=>'半ssl', 'urlPrefix'=>'https://hu60.cn'],
    'mainssl' => ['name'=>'ssl', 'urlPrefix'=>'https://ssl.hu60.cn'],
    'baidu' => ['name'=>'百度', 'urlPrefix'=>'http://baidu.cdn.hu60.cn'],
    'yundun' => ['name'=>'云盾', 'urlPrefix'=>'http://yd.cdn.hu60.cn'],
    'cmcc' => ['name'=>'腾讯云', 'urlPrefix'=>'https://hu60.cn'],
];

switch ($_GET['action']) {
    case 'send':
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/plain');
        header('Cache-Control: no-cache');

        //测试文件大小：10KB
        for ($i = 0; $i < 320; $i++) {
            echo md5(rand()); //md5长32字节，32*320=10240
        }

        break;

    case 'report':
        /* data = {
         *     tag1: { startTime:111, endTime:222, success:true, speed: 1111 },
		 *     tag2: { startTime:112, endTime:245, success:false, errcode:'timeout' },
		 *     ...
         * }
		 */
        $data = json_decode($_POST['data'], false, 3);

        $remote = $_SERVER['REMOTE_ADDR'];
        $Xa = trim($_SERVER['HTTP_CLIENT_IP']);
        $Xb = str_replace(' ', '', trim($_SERVER['HTTP_X_FORWARDED_FOR']));
        $Xc = trim($_SERVER['HTTP_VIA']);

        $ip = [ $remote ];
        if (!empty($Xc)) { array_unshift($Xc); }
        if (!empty($Xb)) { array_unshift($Xb); }
        if (!empty($Xa)) { array_unshift($Xa); }

        $ip = implode(',', $ip);

        $sql = 'INSERT INTO '.DB_A.'speedtest(ip, tag, startTime, endTime, speed, success, errCode) values(?, ?, ?, ?, ?, ?, ?)';

        $db = db::conn();
        $rs = $db->prepare($sql);

        if (!$rs) {
            echo json_encode(['success'=>false, 'errCode'=>'db-prepare']);
            return;
        }

        $tags = array_keys($testSites);
        $count = 0;

        foreach ($tags as $tag) {
            $item = $data->$tag;
            $ok = $rs->execute([$ip, $tag, $item->startTime, $item->endTime, $item->speed, $item->success, $item->errCode]);
            if ($ok) { $count++; }
        }

        if ($count > 0) {
            echo json_encode(['success'=>true, 'count'=>$count]);
        } else {
            echo json_encode(['success'=>false, 'errCode'=>'data-insert']);
        }

        break;

    default:
        $tpl = $PAGE->start();
        $tpl->assign('TEST_FILE_SIZE', 10240); //测试文件大小：10KB
		
		$testResults = $testSites;
		$db = db::conn();
		
		$rs = $db->query('SELECT tag, count(*) as size, avg(speed) as speed FROM '.DB_A.'speedtest GROUP BY tag');
		$data = $rs->fetchAll(db::ass);
		
		foreach ($data as $v) {
			$tag = $v['tag'];
			unset($v['tag']);

			if (isset($testResults[$tag])) {
				$testResults[$tag] += $v;
			}
		}
		
        //只统计成功者的用时
		$rs = $db->query('SELECT tag, count(*) as successSize, avg(endTime-startTime) as time FROM '.DB_A.'speedtest WHERE success=1 GROUP BY tag');
		$data = $rs->fetchAll(db::ass);
		
		foreach ($data as $v) {
			$tag = $v['tag'];
			unset($v['tag']);
			if (isset($testResults[$tag])) {
				$testResults[$tag] += $v;
				//成功率
				$testResults[$tag]['successRate'] = $testResults[$tag]['successSize'] / $testResults[$tag]['size'];
			}
		}
		
        $tpl->assign('testSites', $testSites);
		$tpl->assign('testResults', $testResults);
		
        $tpl->display('tpl:addin.speedtest');
        break;
}
