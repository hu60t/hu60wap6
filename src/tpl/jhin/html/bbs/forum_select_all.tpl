{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}

{block name='title'}
  发帖 - {#BBS_NAME#}
{/block}

{block name='body'}

<!--导航栏-->
<div class="breadcrumb">
    <a href="index.index.{$BID}">首页</a> &gt;
	<a href="{$CID}.forum.{$BID}">{#BBS_INDEX_NAME#}</a> &gt;
    选择版块
</div>

<div id="newtopic_forum_list">
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
					<a href="{$CID}.{$PID}.{$forum.id}.{$BID}">
						{$forum.name}
					</a>
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
