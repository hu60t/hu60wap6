<?php
$tpl=$PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);

if (!$USER->islogin || $USER->uid != 1)
    die('403 Forbidden');
switch($PAGE->ext[0]){
case 'createbk':
if($_POST['yes'] && $_POST['name']){
if(!$_POST['parent_id']){
$pid=0;
}else{
$pid=$_POST['parent_id'];
}
$ok = $bbs->createForum($_POST['name'],$pid,$_POST['bz']);
    if (!$ok)
        throw new Exception('未知原因发帖失败，请重试或联系管理员');
}
$arr = $bbs->childForumMeta(0, '*', 0);
$array['父版块']=0;
foreach($arr as $v){
$array['父版块：'.$v['name']]=$v['id'];
}
break;
case 'bk':
if($_POST['sc']){

}elseif($_POST['xg']){
$xg= $bbs->scxg($_POST['bbid'],'xg');
$tpl->assign('xg',$xg);
}else{
$arr = $bbs->plate('lan');
foreach($arr as $v){
if($v['parent_id']==0){
$n='父';
}else{
$n='-----子';
}
}
$array[$n.'版块：'.$v['name']]=$v['id'];
}
break;
}$tpl->assign('array',$array);
$tpl->display('tpl:bbs');