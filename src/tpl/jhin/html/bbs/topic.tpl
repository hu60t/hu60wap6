{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}
{block name='title'}
	{$tMeta.title} - {$fName} - {#BBS_NAME#}
{/block}
{block name='body'}
{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{include file="tpl:comm.at"}
{$ok=$ubb->setOpt('at.jsFunc', 'atAdd')}

<!--导航栏-->
<div class="breadcrumb">
    <a href="index.index.{$BID}">首页</a>
    {foreach $fIndex as $forum}
        &gt; <a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a>
    {/foreach}
    {if !$forum.notopic}(<a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a>){/if}
</div>

<div class="topic">
	{if $p == 1}

		{$v=array_shift($tContents)}
		{$tmp = $v.uinfo->setUbbOpt($ubb)}
		<h1>标题: {$tMeta.title|code}</h1>
		<div class="topic-meta">

					<img src="{$v.uinfo->avatar()}" class="avatar">
					<a class="topic-author" href="user.info.{$v.uinfo.uid}.{$BID}">{$v.uinfo.name|code}</a>
					<a href="#" onclick="atAdd('{$v.uinfo.name|code}',this);return false">@Ta</a>
			时间: {str::ago($v.mtime)}
			点击: {$tMeta.read_count}
		</div>
		<div class="topic-content" data-floorID="0" id="floor_content_0">
			{$ubb->display($v.content,true)}
		</div>
		<div class="floor_fold_bar" id="floor_fold_bar_0"></div>
		{include file="tpl:bbs.topic_manager"}
	{else}
		<p>{$tMeta.title|code}</p>
	{/if}
</div>
<div class="comments">
	<div class="bar">回复列表({$contentCount-1})</div>
	{if count($tContents)>0}
	<div class="comments-list">
		<ul class="comments-ul">
			{foreach $tContents as $v}
			{$tmp = $v.uinfo->setUbbOpt($ubb)}
			<li>
				<div class="floor-content" data-floorID="{$v.floor}" id="floor_content_{$v.floor}">
					<span class="comments-number">#{$v.floor}</span>
					<div class="comments-meta">
						<img src="{$v.uinfo->avatar()}" class="avatar">
						<a href="user.info.{$v.uinfo.uid}.{$BID}" class="comments-author">{$v.uinfo.name|code}</a>
						(
						<a href="#" onclick="atAdd('{$v.uinfo.name|code}',this);return false">@Ta</a>
						/ {str::ago($v.mtime)}
						{if $bbs->canEdit($v.uinfo.uid, true)}
							/ <a href="{$CID}.edittopic.{$v.topic_id}.{$v.id}.{$BID}">改</a>
						{/if}
						{if $bbs->canDel($v.uinfo.uid, true)}
							/ <a href="{$CID}.deltopic.{$v.topic_id}.{$v.id}.{$BID}">删</a>
						{/if})
					</div>
					<div class="comments-content">
						{$ubb->display($v.content,true)}
					</div>
				</div>
				<div class="floor_fold_bar" id="floor_fold_bar_{$v.floor}"></div>
			</li>
			{/foreach}
		</ul>
	</div>
	{else}
	<div class="text-notice">帖子没有回复</div>
	{/if}

	<div class="widget-page">
		{if $maxPage > 1}
		{if $p < $maxPage}<a href="{$cid}.{$pid}.{$tid}.{$p+1}.{$bid}">下一页</a>{/if}
		{if $p > 1}<a href="{$cid}.{$pid}.{$tid}.{$p-1}.{$bid}">上一页</a>{/if}
		{$p}/{$maxPage}页,共{$contentCount-1}楼
		<input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='{$CID}.{$PID}.{$tid}.'+this.value+'.{$BID}'; }">
		{/if}
	</div>
	<!--回复框-->
	<div class="comments-replay">
		<div class="bar">添加新回复</div>
		{if $tMeta.locked}
		<div class="text-notice">该帖子已锁定，不能回复。</div>
		{elseif $USER->islogin}
		<form method="post" action="{$CID}.newreply.{$tid}.{$p}.{$BID}" class="comments-form">
			<textarea id="content" name="content" class="comments-form-content">{$smarty.post.content}</textarea>
			<input type="hidden" name="token" value="{$token->token()}">
			<p>
				<input type="submit" id="reply_topic_button" name="go" value="评论该帖子"/>
				<input type="button" id="add_files" value="添加附件" onclick="addFiles()"/>
				<a id="ubbHelp" href="https://hu60.cn/q.php/bbs.topic.80645.html">UBB说明</a>
				{include file="tpl:comm.addfiles"}
			</p>
		</form>
		{else}
		回复需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。
		{/if}
	</div>
	<div class="ubb-content">

	</div>
</div>
<style>
.comments-form-content{
	width:80%;
	height:100px;
	margin:8px 5px;
}
</style>
<script>
// 自动折叠过长内容
	$(document).ready(function(){
		var maxHeight = 768;
		$(".topic-content,.floor-content").each(function(){
			var that =$(this);
			var id=this.getAttribute("data-floorID");
			if(that.height() >  maxHeight){
				that.height(maxHeight);
				$('#floor_fold_bar_'+id).html("<button data-floorID='"+id+"'>展开隐藏内容</button>");
				$('#floor_fold_bar_'+id+">button").on('click',function(){
					var id=this.getAttribute("data-floorID");
					var that=$("#floor_content_"+id)
					if(that.height()>maxHeight){
						that.height(maxHeight);
						this.innerHTML='展开超出内容';
					}else{
						that.height(that[0].scrollHeight);
						this.innerHTML='折叠超出内容';
					}
				});
			}
		});
	});


</script>
{/block}
