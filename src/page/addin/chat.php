<?php
$tpl = $PAGE -> start();
$USER -> start();
$user = $USER;
$chat = new chat($USER);
if($PAGE -> ext[0]){
     $roomname = $PAGE -> ext[0];
     $tpl -> assign('roomname', $roomname);
	 $chat -> checkName($roomname);
     if($_POST['go'])
    {
         if(!$user -> islogin)
             $err_msg = '你必须要<a href="user.login.' . $PAGE -> bid . '">登录</a>才能发言';
         else
            {
			 $chat -> checkroom($roomname);
             if($_POST['neirong'] == '')
                 $err_msg = '内容不能为空';
             else
                {
                 $chat -> chatsay($roomname, $_POST['neirong'], time());
                 }
             }
         }
     $ubbs = new ubbdisplay();
	 $ubbs->setOpt('at.jsFunc', 'atAdd');
     $tpl -> assign('err_msg', $err_msg);
     $list = $chat -> chatlist($roomname);
     foreach($list[row] as $k => $m){
         $list[row][$k][content] = $ubbs -> display($m[content], truw);
         }
     $tpl -> assign('list', $list);
     $tpl -> display("tpl:chat");
     }else{
     if($_POST[roomname]){
         $url = 'addin.chat.' . $_POST[roomname] . '.' . $PAGE -> bid;
         header("location:$url");
         }
     // 聊天室列表
    $list = $chat -> roomlist();
     $tpl -> assign('list', $list);
     $tpl -> display("tpl:chat_list");
     }
