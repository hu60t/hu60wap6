{config_load file="conf:site.info"}
{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{include file="tpl:comm.head" title="{if $tMeta.essence}[精]{/if}{$tMeta.title} - {$fName} - {#BBS_NAME#}" onload="foldFloorOnload({count($tContents)})"}
<script>
	function foldFold(floor) {
		var content = document.getElementById('floor_content_' + floor);
		var foldBar = document.getElementById('floor_fold_bar_' + floor);
		
		content.style.maxHeight = '768px';
		foldBar.innerHTML = '<a id="floor_expand_' + floor +
				'" href="#" onclick="foldExpand(' + floor + ');return false">查看全部</a>';
	}
	
	function foldExpand(floor) {
		var content = document.getElementById('floor_content_' + floor);
		var foldBar = document.getElementById('floor_fold_bar_' + floor);
		
		content.style.maxHeight = '';
		foldBar.innerHTML = '<a id="floor_fold_' + floor +
				'" href="#" onclick="foldFold(' + floor + ');return false">折叠内容</a>';
	}
	
	function foldFloorInit(floor) {
		var content = document.getElementById('floor_content_' + floor);
		var height = content.offsetHeight;
		
		if (height > 768) {
			var foldBar = document.getElementById('floor_fold_bar_' + floor);
			
			foldBar.style.borderTop = '1px solid #BED8EA';
			foldBar.style.borderBottom = '1px solid #BED8EA';
			foldBar.style.height = '24px';
			foldBar.style.textAlign = 'center';
			
			foldFold(floor);
		}
	}
	
	function foldFloorOnload(floorSize) {
		var i;
		
		for (i=0; i<floorSize; i++) {
			foldFloorInit(i);
		}
	}

</script>
{include file="tpl:comm.at"}
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
		{$tmp = $v.uinfo->setUbbOpt($ubb)}
		<a class="floor-link" name="0"></a>
		<p>标题: <span id="topic_title">{if $tMeta.essence}<span style="color:red;">[精]</span>{/if}{$tMeta.title|code}</span></p>
		<p>作者: <a class="user_info_link" href="user.info.{$v.uinfo.uid}.{$BID}">{$v.uinfo.name|code}</a> <a href="#" class="user_at_link" onclick="atAdd('{$v.uinfo.name|code}',this);return false">@Ta</a></p>
		<p>时间: {str::ago($v.ctime)}{if $v.ctime != $v.mtime}发布，{str::ago($v.mtime)}修改{/if}</p>
		<div>
			点击: {$tMeta.read_count}
			{if $v.review}
				<div class="topic-status">待审核</div>
			{/if}
			{if $v.uinfo->hasPermission(UserInfo::PERMISSION_BLOCK_POST)}
				<div class="topic-status">被禁言</div>
			{/if}
			{if $v.locked}
				<div class="topic-status">被锁定</div>
			{/if}
            {if $tMeta.level < 0}
                <div class="topic-status">被下沉</div>
            {/if}
		</div>
		<hr>
		<div class="floor_content" id="floor_content_0">{$ubb->display($v.content,true)}</div>
		<div class="floor_fold_bar" id="floor_fold_bar_0"></div>
		<script>foldFloorInit(0)</script>
		{if $bbs->canEdit($v.uinfo.uid, true) || $bbs->canDel($v.uinfo.uid, true)}
			<hr>
			<p>[{if $bbs->canEdit($v.uinfo.uid, true)}<a href="{$CID}.edittopic.{$v.topic_id}.{$v.id}.{$p}.{$BID}">改</a>{else}改{/if}{if ($tMeta.essence==0) && $bbs->canSetEssence(true)}|<a href="{$CID}.setessencetopic.{$v.topic_id}.{$v.id}.{$BID}">加精</a>{/if}{if ($tMeta.essence==1) && $bbs->canUnsetEssence(true)}|<a href="{$CID}.unsetessencetopic.{$v.topic_id}.{$v.id}.{$BID}">取消精华</a>{/if}|续|{if $bbs->canDel($v.uinfo.uid, true)}<a href="{$CID}.deltopic.{$v.topic_id}.{$v.id}.{$BID}">删</a>{else}删{/if}|{if $bbs->canSink($v.uinfo.uid,true)}<a href="{$CID}.sinktopic.{$v.topic_id}.{$BID}">沉</a>{else}沉{/if}|{if $bbs->canMove($v.uinfo.uid,true)}<a href="{$CID}.movetopic.{$v.topic_id}.{$BID}">移</a>{else}移{/if}|设]</p>
		{/if}
    {if $bbs->canFavorite($v.uinfo.uid, true)}
    <hr>
    {if $bbs->isFavoriteTopic($v.topic_id)}
    <div>
      &nbsp;&nbsp;<a href="#" class="favoriteTopic" style="background-color: #EEE;color: #2e4e7e;">取消收藏</a>
      <span id="favoriteTopicError" style="color: red;display: none;"></span>
    </div>
    {else}
    <div>
      &nbsp;&nbsp;<a href="#" class="favoriteTopic" style="background-color: #EEE;color: #2e4e7e;">加入收藏</a>
      <span id="favoriteTopicError" style="color: red;display: none;"></span>
    </div>
    {/if}
    {/if}
	{else}
		<p>{$tMeta.title|code}</p>
	{/if}
</div>
<hr>
<p>『回复列表({$contentCount-1}{if $blockedReply}, 屏蔽<a href="?all=1">{$blockedReply}</a>{/if})』</p>
<div>
    {foreach $tContents as $v}
		{$tmp = $v.uinfo->setUbbOpt($ubb)}
		<div class="floor_content" id="floor_content_{$v.floor}"><a class="floor-link" name="{$v.floor}" href="?floor={$v.floor}#{$v.floor}">{$v.floor}</a>. {$ubb->display($v.content,true)}</div>
		<div class="floor_fold_bar" id="floor_fold_bar_{$v.floor}"></div>
		<script>foldFloorInit({$v.floor})</script>
		<div>
			(<a class="user_info_link" href="user.info.{$v.uinfo.uid}.{$BID}">{$v.uinfo.name|code}</a>/<a href="#" class="user_at_link" onclick="atAdd('{$v.uinfo.name|code}',this);return false">@Ta</a>/{date('Y-m-d H:i',$v.mtime)}{if $bbs->canEdit($v.uinfo.uid, true)}/<a href="{$CID}.edittopic.{$v.topic_id}.{$v.id}.{$p}.{$BID}">改</a>{/if}{if $bbs->canDel($v.uinfo.uid, true)}/<a href="{$CID}.deltopic.{$v.topic_id}.{$v.id}.{$BID}">删</a>{/if}{if $v.review}
				<div class="topic-status">待审核</div>
			{/if}{if $v.uinfo->hasPermission(UserInfo::PERMISSION_BLOCK_POST)}
				<div class="topic-status">被禁言</div>
			{/if}{if $v.locked}
				<div class="topic-status">被锁定</div>
			{/if})
		</div>
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

<script>
  $(document).ready(function() {
    //帖子收藏与取消
    $(".favoriteTopic").click(function(e) {
      e.preventDefault();

      if($(this).attr('disabled')=='disabled')
        return;

      $(this).attr('disabled', 'disabled');
      if($(this).text()=='加入收藏') {
        $.getJSON("{$CID}.setfavoritetopic.{$v.topic_id}.json", function(r) {
          if(r.success) {
            $(".favoriteTopic").text('取消收藏');
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
            $(".favoriteTopic").text('加入收藏');
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

{include file="tpl:comm.foot"}
