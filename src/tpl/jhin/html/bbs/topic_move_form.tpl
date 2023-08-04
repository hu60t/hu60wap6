{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}

{block name='title'}
	移动帖子 - {$tMeta.title} - {#BBS_NAME#}
{/block}

{block name='body'}
<form id="topic_move_form" method="post" action="{$CID}.{$PID}.{$topicId}.{$BID}">
	<input type="hidden" id="newFid" name="newFid" value="{$fid}" />
	<input type="hidden" name="go" value="go" />
</form>

<script>
	function moveTopic(newFid) {
		document.getElementById('newFid').value = newFid;
		document.getElementById('topic_move_form').submit();
	}
</script>

<div class="breadcrumb">
	<span class="user-title">{$tMeta.title|code}</span> | <a href="{$CID}.topic.{$topicId}.{$BID}">返回帖子</a>
</div>
<h3>选择新版块：</h3>
<div id="move_forum_list">
	{$forumStack = [ $forums ]}

	<ol>
	{while !empty($forumStack)}
		{$forumList = array_shift($forumStack)}

		{while !empty($forumList)}
			{$forum = array_shift($forumList)}

			<li>
				{if $forum.notopic}
					{$forum.name}
				{else}
					<a href="#" onclick="moveTopic({$forum.id});return false">
						{if $fid == $forum.id}
							<span class="success">{$forum.name}</span>
						{else}
							{$forum.name}
						{/if}
					</a>
				{/if}
				{if $USER->canAccess(1) && $forum.access == 0}
                    <div class="topic-status">公开</div>
                {/if}
			</li>

			{if !empty($forum.child)}
				{$tmp = array_unshift($forumStack, $forumList)}
				{$forumList = $forum.child}

				<ol>
			{/if}
		{/while}

		</ol>
	{/while}

</div>

{/block}
