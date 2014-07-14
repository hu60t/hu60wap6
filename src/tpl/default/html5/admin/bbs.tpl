{include file="tpl:comm.head" title="后台管理" no_user=true}
{config_load file="conf:site.info"}
<!--导航栏-->
<div class="pt">
<div class="cr180_ptzmenu">
    {foreach $fIndex as $forum}
    {/foreach}
<a  href="javascript:;" onclick="location.href='admin.index.{$BID}'" class="pt_z">管理页</a>
            <span class="pt_c">论坛管理</span>
<span class="pt_y"><a href="{$CID}.{$PID}.{$PAGE->ext[0]|code}.{$BID}">刷新</a></span>
</div>
</div>
{if $PAGE->ext[0]=='createbk'}
{if $smarty.post.yes}
<div class="message_f_c" style="padding:50px 10px; background:#f5f5f5">
<div id="messagetext">
<p>
{if $smarty.post.name}
{div class="msg"}创建成功!{/div}
{else}
{div class="msg"}创建失败!内容不能为空。{/div}
{/if}
</p>
        <p><a href="javascript:history.back();">[ 点击这里返回上一页 ]</a></p>
</div>
</div>
{else}
{form action="admin.bbs.createbk.{$bid}" method="post"}
{div class="cr180_form"}
<div >
<p>
<input type="text" name="name" id="username_LCxiI" class="txt" placeholder="版块名称" value=""/>
</p>
<p>{select name="parent_id" option=$array}<br/>
</p>
<p>
<input type="text" name="bz" id="username_LCxiI" class="txt" placeholder="版主(用户ID，以“|”隔开)" value="{$array['bz']}"/>
</p>
    <p><input type="submit" name="yes" id="submit" class="cr_login_submit" value="创建论坛板块" /></p>
    {/div}
	{/form}
    {/div}
{/if}
{elseif $page->ext[0]=='bk'}
{if $smarty.post.sc}
<div class="message_f_c" style="padding:50px 10px; background:#f5f5f5">
<div id="messagetext">
<p>删除板块，成功删除！</p>
        <p><a href="javascript:history.back();">[ 点击这里返回上一页 ]</a></p>
</div>
</div>
{elseif $smarty.post.xg}
{div class="title"}修改板块信息{/div}
{if $smarty.post.yes}
{div class="msg"}修改成功!{/div}
{/if}
{form action="admin.bbs.bk.{$bid}" method="post"}
{input type="hidden" name="xg" value="我要修改你"}
{input type="hidden" name="parent_id" value="{$smarty.post.bbid}"}
版块名：{input type="text" name="name" value="{$xg['name']}"}<br/>
<p>{select name="parent_id" option=$array}<br/>
{if $xg['parent_id']!=0}版主列表：{input type="text" name="bz" value="{$xg['bz']}"}(用ID表示，以“,”隔开)<br/>{/if}
{input type="submit" name="yes" value="修改"}
{/form}
{else}
{form action="admin.bbs.bk.{$bid}" method="post"}
{div class="cr180_form"}
<div >
<p>{select name="bbid" option=$array}<br/>
</p>
    <p><input type="submit" name="xg" id="submit" class="cr_login_submit" style="width:49%;" value="修改论坛板块" />
    <input type="submit" name="sc" id="submit" class="cr_login_submit" style="width:49%;" value="删除论坛板块" /></p>
    {/div}
    {/div}
	{/form}
{/if}
{/if}
{include file="tpl:comm.foot"}