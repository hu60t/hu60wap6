{include file="tpl:comm.head" title="用户中心" no_user=true}
{config_load file="conf:site.info"}
<div class="tp">
<a href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
<span class="pt_c">用户中心</span>
<span class="pt_y"><a href="user.exit.{$bid}">退出</a></span>
</div>
<!--p class="txt">
 <img src="{$USER->getinfo('avatar.url')|code}" width="30"/><br/>
</p-->
<p class="txt">
 UID：{$USER->uid|code}
</p>
<p class="txt">
 用户名：{$USER->name|code}<br/>
</p>
<p class="txt">
 邮箱：{$USER->mail|code}<br/>
</p>
<p class="txt">
 个性签名：{$USER->getinfo('signature')|code}<br/>
</p>
<p class="txt">
 联系方式：{$USER->getinfo('contact')|code}<br/>
</p>
<p class="txt">
 注册时间：{if $USER->regtime == 0}您是很久以前注册的，那时没有记录注册时间{else}{date('Y年m月d日 H:i:s',$USER->regtime)}{/if}
</p>
<p class="txt">
查看：<a href="msg.index.{$bid}">内信</a>/<a href="msg.index.@.{$bid}">@消息</a>/<a href="bbs.search.send.{$bid}?username={$USER->name|urlencode}">帖子</a><br/>
</p>
<p class="txt">
更改：<a href="{$cid}.chname.{$bid}">用户名</a>/<a href="{$cid}.chpwd.{$bid}">密码</a>/<a href="{$cid}.chinfo.{$bid}">个性签名/联系方式</a>
</p>
<p class="txt">
绑定：{if $hasRegPhone}已绑定手机号{else}<a href="{$CID}.active.{$BID}?sid={$USER->sid}">手机号</a>{/if}
</p>
<p class="txt">
界面：<a href="link.css.default.{$BID}?url64={code::b64e($page->geturl())}">白天模式</a>/<a href="link.css.night.{$BID}?url64={code::b64e($page->geturl())}">夜间模式</a><br/>
</p>
<p class="txt">
模板：<a href="user.template.html?name=classic">默认模板</a>/<a href="user.template.html?name=jhin">测试模板</a><br/>
</p>
<p class="txt">
论坛楼层排序：
{if $floorReverse}
	<a href="?floorReverse=0">正序</a>/倒序
{else}
	正序/<a href="?floorReverse=1">倒序</a>
{/if}
<br/>
</p>
<p class="txt">
功能：<a href="addin.webplug.{$BID}">网页插件</a>
<br/>
</p>
{if $mmbt}
<p class="txt">
进入：<a href='admin.index.{$bid}' class="cr_login_submit">管理后台</a>
<br/>
</p>
{/if}
{include file="tpl:comm.foot"}
