{include file="tpl:comm.head" title="用户中心" no_user=true}
{config_load file="conf:site.info"}
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="首页" class="pt_z">回首页</a>
<span class="pt_c">用户中心</span>
<span class="pt_y"><a href="{$u|code}">返回来源</a></span>
</div>
</div>
{div class="title"}用户中心{/div}
{$USER->name}(ID:{$USER->uid}),<a href="user.logout.{$bid}">退出</a>{hr}
<img src="{$USER->getinfo('avatar.url')|code}" width="60"/><br/>
用户名:{$USER->name}<br/>
邮箱:{$USER->mail}<br/>
注册时间:{date('Y年m月d日 H:i:s',$USER->regtime)}<br/>
{include file="tpl:comm.foot"}
