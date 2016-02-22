{include file="tpl:comm.head" title="后台管理" no_user=true}
{config_load file="conf:site.info"}
{div class="title"}基本设置{/div}
{if $smarty.post.yes}
{div class="msg"}保存成功！{/div}
&lt;&lt;<a href="admin.site.index.{$bid}">返回</a>
{else}
{form action="admin.site.index.{$bid}" method="post"}
网站标题：{input type="text" name="site_name" value="{#SITE_NAME#}"}<br/>
论坛名称：{input type="text" name="bbs_name" value="{#BBS_NAME#}"}<br/>
论坛首页名称：{input type="text" name="bbs_index_name" value="{#BBS_INDEX_NAME#}"}<br/>
报时：{input type="text" name="clock_name" value="{#CLOCK_NAME#}"}<br/>
{input type="submit" name="yes" value="保存"}
&lt;&lt;<a href="admin.index.{$bid}">返回</a>
{/form}
{/if}
{include file="tpl:comm.foot"}