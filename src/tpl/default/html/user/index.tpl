{include file="tpl:comm.head" title="用户中心" no_user=true}
{config_load file="conf:site.info"}
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="首页" class="pt_z">回首页</a>
<span class="pt_c">用户中心</span>
<span class="pt_y"><a href="user.exit.{$bid}">退出</a></span>
</div>
</div>
<div class="cr_login_submit" style="background:#020">欢迎你哦！{$USER->name|code}(ID:{$USER->uid|code})</div>
{div class="cr180"}
{div}
<p class="txt">
 <img src="{$USER->getinfo('avatar.url')|code}" width="30"/><br/>
</p>
<p class="txt">
 用户名:{$USER->name|code}<br/>
</p>
<p class="txt">
 邮箱:{$USER->mail|code}<br/>
</p>
<p class="txt">
 个性签名: {$USER->getinfo('signature')|code}<br/>
</p>
<p class="txt">
 联系方式: {$USER->getinfo('contact')|code}<br/>
</p>
<p class="txt">
 注册时间: {if $USER->regtime == 0}您是很久以前注册的，那时没有记录注册时间{else}{date('Y年m月d日 H:i:s',$USER->regtime)}{/if}
</p>
<p class="txt">
查看：<a href="msg.index.{$bid}">内信</a>/<a href="msg.index.@.{$bid}">@消息</a><br/>
</p>
{/div}
{/div}
{if $mmbt}
<a href='admin.index.{$bid}' class="cr_login_submit" style="background:#060">管理后台</a>
{/if}
{include file="tpl:comm.foot"}
