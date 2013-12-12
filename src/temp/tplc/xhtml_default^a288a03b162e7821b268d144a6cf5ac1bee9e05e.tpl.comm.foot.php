<?php /* Smarty version Smarty-3.1.12, created on 2013-12-11 22:12:56
         compiled from "tpl:comm.foot" */ ?>
<?php /*%%SmartyHeaderCode:1080852a872e80f5261-75586403%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a288a03b162e7821b268d144a6cf5ac1bee9e05e' => 
    array (
      0 => 'tpl:comm.foot',
      1 => 1386766819,
      2 => 'tpl',
    ),
  ),
  'nocache_hash' => '1080852a872e80f5261-75586403',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'base' => 0,
    'page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52a872e814def8_34475228',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52a872e814def8_34475228')) {function content_52a872e814def8_34475228($_smarty_tpl) {?><?php if (!$_smarty_tpl->tpl_vars['base']->value){?><div class="content">
<?php echo date("n月j日 H:i");?>
 星期<?php echo call_user_func_array("str::星期",array(date("w")));?>

<a id="bottom" href="#top" accesskey="3"></a>
</div>
<div class="tip">
切换:<span class="linktext">彩版</span>|<a href="<?php echo code::html($_smarty_tpl->tpl_vars['page']->value->geturl(array("bid"=>"wml")));?>
">简版</a>|<a href="#top">回顶</a><br/>
效率:<?php echo round(microtime(true)-$_SERVER['REQUEST_TIME_FLOAT'],3);?>
秒(压缩:<?php if ($_smarty_tpl->tpl_vars['page']->value['gzip']){?>开<?php }else{ ?>关<?php }?>)</div>
<?php }?>
</body>
</html><?php }} ?>