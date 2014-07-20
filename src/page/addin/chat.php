<?php
$tpl = $PAGE -> start();
$USER -> start();
$user = $USER;
$chat = new chat;
if($PAGE -> ext[0]){
     $roomname = $PAGE -> ext[0];
     $tpl -> assign('roomname', $roomname);
     $chat -> checkroom($roomname);
     if($_POST['go'])
    {
         if(!$user -> islogin)
             $err_msg = '你必须要<a href="user.login.' . $PAGE -> bid . '">登录</a>才能发言';
         else
            {
             if($_POST['neirong'] == '')
                 $err_msg = '内容不能为空';
             else
                {
                 $ubb = new ubbparser;
                 $content = $ubb -> parse($_POST['neirong'], true);
                 $chat -> chatsay($roomname, $user -> uid, $user -> name, $content, time());
                 }
             }
         }
     $ubbs = new ubbdisplay();
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
