<?php /* Smarty version Smarty-3.1.12, created on 2013-12-12 10:46:51
         compiled from "tpl:error.pageerr" */ ?>
<?php /*%%SmartyHeaderCode:2409052a9239bea7d43-67010237%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bc2996c610d08a3ac1baaddef2be1cac7ed6a3ee' => 
    array (
      0 => 'tpl:error.pageerr',
      1 => 1386766819,
      2 => 'tpl',
    ),
  ),
  'nocache_hash' => '2409052a9239bea7d43-67010237',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'err' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52a9239c5d5f68_68974043',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52a9239c5d5f68_68974043')) {function content_52a9239c5d5f68_68974043($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("tpl:comm.head", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('title'=>"出错啦！！"), 0);?>

<div class="<?php echo "tip";?>">抱歉，由于开发人员的各种不小心，该页面在执行过程中发生了一些错误。</div>
<div class="<?php echo "content";?>">错误代码：<span class="<?php echo "notice";?>"><?php echo code::html($_smarty_tpl->tpl_vars['err']->value->getcode());?>
</span></div>
<div class="<?php echo "tip";?>">错误信息：<span class="<?php echo "notice";?>"><?php echo code::html($_smarty_tpl->tpl_vars['err']->value->getmessage());?>
</span></div>
<div class="<?php echo "content";?>">错误发生在 <span class="<?php echo "notice";?>"><?php echo code::html($_smarty_tpl->tpl_vars['err']->value->getfile());?>
</span> 的第 <span class="<?php echo "notice";?>"><?php echo code::html($_smarty_tpl->tpl_vars['err']->value->getline());?>
</span> 行</div>
<div class="<?php echo "title";?>">错误追踪信息：</div>
<div class="<?php echo "content";?>">
<span class="<?php echo "notice";?>"><?php echo code::html($_smarty_tpl->tpl_vars['err']->value->getTraceAsString(),'<br/>');?>
</span>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("tpl:comm.foot", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>