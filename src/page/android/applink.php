<?php
$data = '/data/data';
$app = '/data/app';
$extdata = '/data/sdext2/dataDIR';
$extapp = '/data/sdext2';

$tpl=$PAGE->start();

$act = $_GET['act'];
class msg extends exception {}
try {
if ($act == '') {
  $p = opendir($app);
  if (!$p) throw new msg('打开app目录失败，请检查该目录的路径设置是否正确。');
  $list = array();
  while(false !== ($f = readdir($p))) {
    if ($f == '.' || $f == '..'){
      continue;
    }
    $path = "$app/$f";
    $info = array('app' => $f);
    if (is_link($path)) {
      $info['apptype'] = 'l'; //文件
    } elseif (is_file($path)) {
      $info['apptype'] = 'f'; //符号连接
    } elseif (is_dir($path)) {
      $info['apptype'] = 'd'; //目录
    } else {
      $info['apptype'] = 'n'; //未知文件类型
    }
    $dir = preg_replace('/(-\d+)?\.apk/is', '', $f);
    $info['dir'] = $dir;
    $path = "$data/$dir";
       if (is_link($path)) {
      $info['dirtype'] = 'l'; //文件
    } elseif (is_file($path)) {
      $info['dirtype'] = 'f'; //符号连接
    } elseif (is_dir($path)) {
      $info['dirtype'] = 'd'; //目录
    } else {
      $info['dirtype'] = 'n'; //未知文件类型
    } 
    $list[] = $info;
  }
  $tpl->assign('list', $list);
  $tpl->display('tpl:applink_list');
} elseif ($act == 'linkapp') {
  throw new msg("不能通过本程序连接app，请在Link2SD中连接“$_GET[app]”");
} elseif ($act == 'unlinkapp') {
  throw new msg("不能通过本程序移回app，请在Link2SD中移回“$_GET[app]”");
} elseif ($act == 'linkdir') {
  $dir = $_GET['dir'];
  if ($dir == '') {
    throw new msg("要移动的目录不能为空");
  }
  $notice = '';
  $src = "$data/$dir";
  $dest = "$extdata/$dir";
  $rename = false;
  if (is_link($src) || !is_dir($src)) {
    throw new msg("目录已移动。");
  }
  while (file_exists($dest)) {
    exec("mv '$dest' '$dest'".rand(0,65536));
    $notice .= "目标目录已存在，自动重命名。\n";
  }
  exec("mv '$src' '$dest'");
  if (file_exists($dest)) {
    $notice .= "目录移动成功。\n";
    exec("ln -s '$dest' '$src'");
    if (file_exists($src)) {
      $notice .= "目录连接成功。\n";
    } else {
      $notice .= "目录连接失败。\n";
    }
  } else {
    $notice .="目录移动失败。\n";
  }
  throw new msg($notice);
} elseif($act == 'unlinkdir') {
  $dir = $_GET['dir'];
  if ($dir == '') {
    throw new msg("要移回的目录不能为空");
  }
  $notice = '';
  $src = "$data/$dir";
  $dest = "$extdata/$dir";
  $rename = false;
  if (is_link($dest) || !is_dir($dest)) {
    throw new msg("目录已移动。");
  }
  if (!is_link($src)) {
    throw new msg("原目录不是符号连接，不能移回。");
  }
  exec("rm -r '$src'");
  if (!file_exists($src)) {
    $notice .= "连接删除成功。\n";
    exec("mv '$dest' '$src'");
    if (is_dir($src)) {
      $notice .= "移回目录成功。\n";
    } else {
      $notice .= "移回目录失败。\n";
    }
  } else {
    $notice .="连接删除失败。\n";
  }
  throw new msg($notice); 
}
} catch (msg $e) {
  $tpl->assign('msg', $e);
  $tpl->display('tpl:notice');
}