{config_load file="conf:site.info"}
{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{include file="tpl:comm.head" title="{$tMeta.title} - {$fName} - {#BBS_NAME#}"}

<script>
	function atAdd(uid,that) {
		that.style.color = "#FFA500";
		var nr = document.getElementById("content");
		nr.value += "@"+uid+"，";
	}
</script>
{$ok=$ubb->setOpt('at.jsFunc', 'atAdd')}

<!--导航栏-->
<div class="tp">
    <a href="index.index.{$BID}">首页</a>
    {foreach $fIndex as $forum}
        &gt; <a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a>
    {/foreach}
    {if !$forum.notopic}(<a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a>){/if}
</div>

<div>
	{if $p == 1}

		{$v=array_shift($tContents)}
		<p>标题: {$tMeta.title|code}</p>
		<p>作者: <a href="user.info.{$v.uinfo.uid}.{$BID}">{$v.uinfo.name|code}</a> <a href="#" onclick="atAdd('{$v.uinfo.name|code}',this);return false">@Ta</a></p>
		<p>时间: {date('Y-m-d H:i',$v.mtime)}</p>
		<p>点击: {$tMeta.read_count}</p>
		<hr>
		<div>{$ubb->display($v.content,true)}</div>
		{if $bbs->canEdit($v.uinfo.uid, true) || $bbs->canDel($v.uinfo.uid, true)}
			<hr>
			<p>[{if $bbs->canEdit($v.uinfo.uid, true)}<a href="{$CID}.edittopic.{$v.topic_id}.{$v.id}.{$BID}">改</a>{else}改{/if}|续|{if $bbs->canDel($v.uinfo.uid, true)}<a href="{$CID}.deltopic.{$v.topic_id}.{$v.id}.{$BID}">删</a>{else}删{/if}|{if $bbs->canSink($v.uinfo.uid,true)}<a href="{$CID}.sinktopic.{$v.topic_id}.{$BID}">沉</a>{else}沉{/if}|移|设]</p>
		{/if}
	{else}
		<p>{$tMeta.title|code}</p>
	{/if}
</div>
<hr>
<p>『回复列表({$contentCount-1})』</p>
<div>
    {foreach $tContents as $v}
		{$tmp = $ubb->setOpt('style.disable', $v.uinfo->hasPermission(UserInfo::PERMISSION_UBB_DISABLE_STYLE))}
		<div>{$v.floor}. {$ubb->display($v.content,true)}</div>
		<p>(<a href="user.info.{$v.uinfo.uid}.{$BID}">{$v.uinfo.name|code}</a>/<a href="#" onclick="atAdd('{$v.uinfo.name|code}',this);return false">@Ta</a>/{date('Y-m-d H:i',$v.mtime)}{if $bbs->canEdit($v.uinfo.uid, true)}/<a href="{$CID}.edittopic.{$v.topic_id}.{$v.id}.{$BID}">改</a>{/if}{if $bbs->canDel($v.uinfo.uid, true)}/<a href="{$CID}.deltopic.{$v.topic_id}.{$v.id}.{$BID}">删</a>{/if})</p>
		<hr>
    {/foreach}
</div>

<div>
    {if $maxPage > 1}
        {if $p < $maxPage}<a href="{$cid}.{$pid}.{$tid}.{$p+1}.{$bid}">下一页</a>{/if}
		{if $p > 1}<a href="{$cid}.{$pid}.{$tid}.{$p-1}.{$bid}">上一页</a>{/if}
		{$p}/{$maxPage}页,共{$contentCount-1}楼
		<input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='{$CID}.{$PID}.{$tid}.'+this.value+'.{$BID}'; }">
		<hr>
    {/if}
</div>

<!--回复框-->
<div>
	{if $tMeta.locked}
		<div class="notice">该帖子已锁定，不能回复。</div>
    {elseif $USER->islogin}
        <form method="post" action="{$CID}.newreply.{$tid}.{$p}.{$BID}">
            <textarea id="content" name="content" style="width:80%;height:100px">{$smarty.post.content}</textarea>
            <input type="hidden" name="token" value="{$token->token()}">
            <p><input type="submit" id="reply_topic_button" name="go" value="评论该帖子"></p>
		</form>
    {else}
        回复需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。
    {/if}
</div>

{include file="tpl:comm.foot"}
