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
		<a class="floor-link" name="0"></a><a name="/0"></a>
		<h1>{if $tMeta.essence}<i class="material-icons" style="color:red;">whatshot</i>{/if}<span class="user-title" id="topic_title">{$tMeta.title|code}</span></h1>
		<div class="topic-meta">
            <div class="topic-avator">
                <img src="{$v.uinfo->avatar()}" class="avatar">
            </div>
            <div class="topic-meta-name">
			    <a class="topic-author" href="user.info.{$v.uinfo.uid}.{$BID}">{$v.uinfo.name|code}</a>
            </div>
            <div class="topic-actions">
			    <a href="#" onclick="atAdd('{$v.uinfo.name|code}',this);return false">@Ta</a>
			    {str::ago($v.ctime)}{if $v.ctime != $v.mtime}发布，{str::ago($v.mtime)}修改{/if}
			    {$tMeta.read_count}点击
				{if $v.review}
					<div class="topic-status">{bbs::getReviewStatName($v.review)}</div>
				{/if}
				{if $v.uinfo->hasPermission(UserInfo::DEBUFF_BLOCK_POST)}
					<div class="topic-status">被禁言</div>
				{/if}
				{if $v.locked}
					<div class="topic-status">被锁定</div>
				{/if}
				{if $tMeta.level < 0}
					<div class="topic-status">被下沉</div>
				{/if}
				{if $tMeta.locked == 2}
				    <div class="topic-status">评论关闭</div>
				{/if}
            </div>
		</div>
		<div class="topic-content user-content" data-floorID="0" id="floor_content_0">
			{$ubb->display($v.content,true)}
		</div>
		<div class="floor_fold_bar" id="floor_fold_bar_0"></div>
		{include file="tpl:bbs.topic_manager"}
    {if $bbs->canFavorite($v.uinfo.uid, true)}
    <hr/>
    {if $bbs->isFavoriteTopic($v.topic_id)}
    <div style="background-color: #EEE;">
      &nbsp;&nbsp;<a href="#" class="favoriteTopic" style="color: #2e4e7e;"><i class="material-icons">star</i>取消收藏</a>
      <span id="favoriteTopicError" style="color: red;display: none;"></span>
    </div>
    {else}
    <div style="background-color: #EEE;">
      &nbsp;&nbsp;<a href="#" class="favoriteTopic" style="color: #2e4e7e;"><i class="material-icons">star_border</i>加入收藏</a>
      <span id="favoriteTopicError" style="color: red;display: none;"></span>
    </div>
    {/if}
    {/if}
	{else}
		<p class="user-title">{$tMeta.title|code}</p>
	{/if}
</div>
<div class="comments">
	<div class="bar">回复列表({$contentCount-1-$blockedReply})</div>
	{if count($tContents)>0}
	<div class="comments-list">
		<ul class="comments-ul">
			{foreach $tContents as $v}
			{$tmp = $v.uinfo->setUbbOpt($ubb)}
			<li>
				<div class="floor-content" data-floorID="{$v.floor}" id="floor_content_{$v.floor}">
					<div class="comments-meta">
					    <div class="comments-number"><a class="floor-link" name="{$v.floor}" href="?floor={$v.floor}#{$v.floor}">{$v.floor}</a><a name="/{$v.floor}"></a></div>
						<div class="comments-avatar">
                            <img src="{$v.uinfo->avatar()}" class="avatar">
                        </div>
                        <div class="comments-meta-name">
    						<a href="user.info.{$v.uinfo.uid}.{$BID}" class="comments-author">{$v.uinfo.name|code}</a>
                        </div>
                        <div class="comments-actions">
    						<a href="#" onclick="atAdd('{$v.uinfo.name|code}',this);return false">@Ta</a>
	    					/ {str::ago($v.mtime)}
		    				{if $bbs->canEdit($v.uinfo.uid, true)}
			    				/ <a href="{$CID}.edittopic.{$v.topic_id}.{$v.id}.{$p}.{$BID}">改</a>
				    		{/if}
					    	{if $bbs->canDel($v.uinfo.uid, true, $tMeta.uid)}
						    	/ <a href="{$CID}.deltopic.{$v.topic_id}.{$v.id}.{$BID}">删</a>
    						{/if}
							{if $v.review}
								<div class="topic-status">{bbs::getReviewStatName($v.review)}</div>
							{/if}
							{if $v.uinfo->hasPermission(UserInfo::DEBUFF_BLOCK_POST)}
								<div class="topic-status">被禁言</div>
							{/if}
							{if $v.locked}
								<div class="topic-status">被锁定</div>
							{/if}
                        </div>
					</div>
					<div class="comments-content user-content">
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

	{include file="tpl:bbs.review-all"}

	<div class="widget-page">
		{if $maxPage > 1}
            {jhinfunc::Pager($p,$maxPage,"{$cid}.{$pid}.{$tid}.##.{$bid}")}
		{/if}
	</div>
	<!--回复框-->
	<div class="comments-replay">
		<div class="bar">添加新回复</div>
		{if $tMeta.locked == 2 && $USER.uid != $tMeta.uid}
			<div class="notice">该帖子已关闭评论，仅楼主可回复。</div>
		{elseif $tMeta.locked && $tMeta.locked != 2}
			<div class="text-notice">该帖子已锁定，不能回复。</div>
		{elseif $tMeta.review && $USER.uid != $tMeta.uid && !$USER->hasPermission(userinfo::PERMISSION_REVIEW_POST)}
			<div class="text-notice">为了减少无关评论，未审核通过的帖子只有楼主和管理员可以回复。</div>
		{elseif $USER->islogin}
			{if $tMeta.locked == 2}
				<div class="notice">该帖子已关闭评论，仅楼主可回复。</div>
			{/if}
			<form method="post" action="{$CID}.newreply.{$tid}.{$p}.{$BID}" class="comments-form">
				<textarea id="content" name="content" class="comments-form-content">{$smarty.post.content}</textarea>
				<input type="hidden" name="token" value="{$token->token()}">
				<p>
					<input type="submit" id="reply_topic_button" name="go" value="评论该帖子"/>
					<input type="button" id="add_files" value="添加附件" onclick="addFiles()"/>
					<a id="ubbHelp" href="bbs.topic.80645.{$BID}">UBB说明</a>
					{include file="tpl:comm.addfiles"}
				</p>
			</form>
		{else}
			回复需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。
		{/if}
	</div>
</div>
<script>
	$(document).ready(function(){
    // 自动折叠过长内容
		var maxHeight = 768;
		$(".topic-content,.floor-content").each(function(){
			var that =$(this);
			var id=this.getAttribute("data-floorID");
			if(that.height() >  maxHeight){
				that.height(maxHeight);
				$('#floor_fold_bar_'+id).html("<button data-floorID='"+id+"'>查看全部</button>");
				$('#floor_fold_bar_'+id+">button").on('click',function(){
					var id=this.getAttribute("data-floorID");
					var that=$("#floor_content_"+id);
					// 不要使用that.height()进行判断，返回值是浮点数，不一定精确相等
					if(this.innerHTML == '折叠过长内容'){
						that.height(maxHeight);
						this.innerHTML='查看全部';
					}else{
						that.height(that[0].scrollHeight);
						this.innerHTML='折叠过长内容';
					}
				});
			}
		});

    //帖子收藏与取消
    $(".favoriteTopic").click(function(e) {
      e.preventDefault();

      if($(this).attr('disabled')=='disabled')
        return;

      $(this).attr('disabled', 'disabled');
      if($(this).find('.material-icons').text()=='star_border') {
        $.getJSON("{$CID}.setfavoritetopic.{$v.topic_id}.json", function(r) {
          if(r.success) {
            $(".favoriteTopic").html('<i class="material-icons">star</i>取消收藏');
          } else {
            $("#favoriteTopicError").text(r.notice);
            $("#favoriteTopicError").show();
            setTimeout('$("#favoriteTopicError").hide()', 2000);
          }
          $(".favoriteTopic").removeAttr('disabled');
        });
      } else {
        $.getJSON("{$CID}.unsetfavoritetopic.{$v.topic_id}.json", function(r) {
          if(r.success) {
            $(".favoriteTopic").html('<i class="material-icons">star_border</i>加入收藏');
          } else {
            $("#favoriteTopicError").text(r.notice);
            $("#favoriteTopicError").show();
            setTimeout('$("#favoriteTopicError").hide()', 2000);
          }
          $(".favoriteTopic").removeAttr('disabled');
        });
      }
    });
	});


</script>
{/block}
