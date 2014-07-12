{include file="tpl:comm.head" title="用户中心" no_user=true}
{config_load file="conf:site.info"}
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="首页" class="pt_z">回首页</a>
<span class="pt_c">用户中心</span>
<span class="pt_y"><a href="user.exit.{$bid}">退出</a></span>
</div>
</div>
欢迎你哦！{$USER->name|code}(ID:{$USER->uid|code}){hr}
<img src="{$USER->getinfo('avatar.url')|code}" width="60"/><br/>
用户名:{$USER->name|code}<br/>
邮箱:{$USER->mail|code}<br/>
注册时间:{date('Y年m月d日 H:i:s',$USER->regtime)}<br/>
{include file="tpl:comm.foot"}
