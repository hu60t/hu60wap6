<?php /* Smarty version Smarty-3.1.12, created on 2013-12-11 22:12:55
         compiled from "tpl:comm.head" */ ?>
<?php /*%%SmartyHeaderCode:1397952a872e77ec1d3-54651062%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fcc624029969ca4ccde759f0a5f89b5577ec402e' => 
    array (
      0 => 'tpl:comm.head',
      1 => 1386766819,
      2 => 'tpl',
    ),
  ),
  'nocache_hash' => '1397952a872e77ec1d3-54651062',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
    'time' => 0,
    'url' => 0,
    'css' => 0,
    'title' => 0,
    'base' => 0,
    'no_user' => 0,
    'user' => 0,
    'bid' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52a872e7c5d367_39445137',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52a872e7c5d367_39445137')) {function content_52a872e7c5d367_39445137($_smarty_tpl) {?><?php header('Content-type: '.$_smarty_tpl->tpl_vars['page']->value['mime'].'; charset='."utf-8"); ?>
<?php echo '<?xml';?> version="1.0" encoding="utf-8" <?php echo '?>';?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="<?php echo $_smarty_tpl->tpl_vars['page']->value['mime'];?>
;charset=utf-8"/><?php if ($_smarty_tpl->tpl_vars['time']->value!==null){?><meta http-equiv="refresh" content="<?php echo $_smarty_tpl->tpl_vars['time']->value;?>
;url=<?php if ($_smarty_tpl->tpl_vars['url']->value===null){?><?php echo code::html(hu60::getmyurl());?>
<?php }else{ ?><?php echo code::html($_smarty_tpl->tpl_vars['url']->value);?>
<?php }?>"/><?php }?>
<?php if ($_smarty_tpl->tpl_vars['css']->value===null){?><?php $_smarty_tpl->tpl_vars['css'] = new Smarty_variable("css.wap.".((string)$_GET['css']).".css", null, 0);?><?php }?>
<link rel="stylesheet" type="text/css" href="<?php echo code::html($_smarty_tpl->tpl_vars['css']->value);?>
"/>
<title><?php echo code::html($_smarty_tpl->tpl_vars['title']->value);?>
</title>
</head>
<body><?php if (!$_smarty_tpl->tpl_vars['base']->value){?><a id="top" href="#bottom" accesskey="6"></a>
<?php if (!$_smarty_tpl->tpl_vars['no_user']->value&&is_object($_smarty_tpl->tpl_vars['user']->value)){?><div class="tip">
<?php if ($_smarty_tpl->tpl_vars['user']->value->uid){?><?php echo code::html($_smarty_tpl->tpl_vars['user']->value->name);?>
[<?php if ($_smarty_tpl->tpl_vars['user']->value->islogin){?><a href="msg.list.<?php echo $_smarty_tpl->tpl_vars['bid']->value;?>
">内信</a>|<a href="msg.atlist.<?php echo $_smarty_tpl->tpl_vars['bid']->value;?>
">动态</a>|<a href="user.exit.<?php echo $_smarty_tpl->tpl_vars['bid']->value;?>
?u=<?php echo urlencode($_smarty_tpl->tpl_vars['page']->value->geturl());?>
">退出</a><?php }else{ ?>已掉线，<a href="user.login.<?php echo $_smarty_tpl->tpl_vars['bid']->value;?>
?u=<?php echo urlencode($_smarty_tpl->tpl_vars['page']->value->geturl());?>
">重新登陆</a><?php }?>]
<?php }else{ ?>#旅行者#[<a href="user.login.<?php echo $_smarty_tpl->tpl_vars['bid']->value;?>
?u=<?php echo urlencode($_smarty_tpl->tpl_vars['page']->value->geturl());?>
">登陆</a>|<a href="user.reg.<?php echo $_smarty_tpl->tpl_vars['bid']->value;?>
?u=<?php echo urlencode($_smarty_tpl->tpl_vars['page']->value->geturl());?>
">注册</a>]<?php }?>
</div><?php }?>
<?php }?><?php }} ?>