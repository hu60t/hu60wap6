{include file="tpl:comm.head" title="用户信息"}
{config_load file="conf:site.info"}
<div class="tp">
<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
<span class="pt_c">用户信息</span>
</div>
<p class="txt">
 <img src="{$uinfo->getinfo('avatar.url')|code}" width="30"/><br/>
</p>
<p class="txt">
 UID：{$uinfo->uid|code}
</p>
<p class="txt">
 用户名：{$uinfo->name|code}<br/>
</p>
<p class="txt">
 个性签名：{$uinfo->getinfo('signature')|code}<br/>
</p>
<p class="txt">
 联系方式：{$uinfo->getinfo('contact')|code}<br/>
</p>
<p class="txt">
 注册时间：{if $uinfo->regtime == 0}该用户是很久以前注册的，那时没有记录注册时间{else}{date('Y年m月d日 H:i:s',$uinfo->regtime)}{/if}
</p>
<p class="txt">
发送：<a href="msg.index.send.{$uinfo.uid}.{$bid}">内信</a> / <a href="msg.index.chat.{$uinfo.uid}.{$bid}">聊天模式</a><br/>
</p>
<p class="txt">
查看：<a href="bbs.search.send.{$bid}?username={$uinfo->name|urlencode}">帖子</a><br/>
</p>
{include file="tpl:comm.foot"}
