<?php /* Smarty version Smarty-3.1.12, created on 2013-12-11 22:12:54
         compiled from "tpl:index" */ ?>
<?php /*%%SmartyHeaderCode:3140252a872e6298fd3-80343964%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5df75ada26cad26e5784873bcb27df231a243cff' => 
    array (
      0 => 'tpl:index',
      1 => 1386766819,
      2 => 'tpl',
    ),
  ),
  'nocache_hash' => '3140252a872e6298fd3-80343964',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52a872e73841f2_44733894',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52a872e73841f2_44733894')) {function content_52a872e73841f2_44733894($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include 'D:\\Development\\hu60wap6\\src/class/smarty/plugins\\function.cycle.php';
?><?php echo $_smarty_tpl->getSubTemplate ("tpl:comm.head", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('title'=>"hu60t网站开发框架"), 0);?>

<div class="<?php echo "content";?>">
欢迎使用 hu60t网站开发框架 进行你的xhtml/wml兼容WAP应用的开发。<br/>
<a href="http://hu60.cn/">访问hu60.cn查看相关文档</a>
</div>
<div class="<?php echo "title";?>">
访问测试模板：
</div>
<?php ob_start();?><?php echo smarty_function_cycle(array('values'=>'tp,'),$_smarty_tpl);?>
<?php $_tmp1=ob_get_clean();?><div class="<?php echo $_tmp1;?>">
<a href="test.index.form.<?php echo $_smarty_tpl->tpl_vars['page']->value['bid'];?>
">表单测试</a><br/>
<a href="test.index.jump.<?php echo $_smarty_tpl->tpl_vars['page']->value['bid'];?>
">URL跳转测试</a><br/>
<a href="test.index.isbid.<?php echo $_smarty_tpl->tpl_vars['page']->value['bid'];?>
">isbid测试</a>
</div>
<div class="<?php echo "title";?>">许可证：
</div>
<div class="<?php echo "content";?>">
本框架由<a href="http://hu60.cn/">绿虎众</a>创建，你可以打开程序根目录的license.txt查看许可证，这是一个UTF-8编码的文件。
</div>
<div class="<?php echo "title";?>">hu60t报时：</div>
<?php echo $_smarty_tpl->getSubTemplate ("tpl:comm.foot", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>