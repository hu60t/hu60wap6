<div class="topic-panel">
  {if $bbs->canEdit($v.uinfo.uid, true)}
    <a href="{$CID}.edittopic.{$v.topic_id}.{$v.id}.{$p}.{$BID}"><i class="material-icons">edit</i>修改</a>
  {/if}
  {if ($tMeta.essence==0) && $bbs->canSetEssence(true)}
    <a href="{$CID}.setessencetopic.{$v.topic_id}.{$v.id}.{$BID}"><i class="material-icons">whatshot</i>加精</a>
  {/if}
  {if ($tMeta.essence==1) && $bbs->canUnsetEssence(true)}
    <a href="{$CID}.unsetessencetopic.{$v.topic_id}.{$v.id}.{$BID}"><i style="color:gray;" class="material-icons">whatshot</i>取消精华</a>
  {/if}
  {if $bbs->canDel($v.uinfo.uid, true)}
    <a href="{$CID}.deltopic.{$v.topic_id}.{$v.id}.{$BID}"><i class="material-icons">delete</i>删除</a>
  {/if}
  {if $bbs->canSink($v.uinfo.uid,true)}
    <a href="{$CID}.sinktopic.{$v.topic_id}.{$BID}"><i class="material-icons">vertical_align_bottom</i>沉底</a>
  {/if}
  {if $bbs->canMove($v.uinfo.uid,true)}
    <a href="{$CID}.movetopic.{$v.topic_id}.{$BID}"><i class="material-icons">content_cut</i>移动</a>
  {/if}
  {if $bbs->canEdit($v.uinfo.uid, true)}
    {if $tMeta.locked == 2}
      <a href="{$CID}.lockreply.{$v.topic_id}.{$BID}?lock=0">开放评论</a>
    {else}
      <a href="{$CID}.lockreply.{$v.topic_id}.{$BID}?lock=1">关闭评论</a>
    {/if}
  {/if}
	{if $bbs->canFavorite($v.uinfo.uid, true)}
		<a href="#" class="favoriteTopic">
			{if $bbs->isFavoriteTopic($v.topic_id)}
				<i class="material-icons">star</i>取消收藏
			{else}
				<i class="material-icons">star_border</i>加入收藏
			{/if}
		</a>
	{/if}
	<a href="javascript:hu60_user_style_toggle(document.querySelector('#floor_content_0'))">隐藏样式</a>
	<a href="javascript:hu60_content_display_ubb('bbs.topic', {$v.id}, 'floor_content_0')">查看源码</a>
</div>
<div style="clear:both;"></div>
