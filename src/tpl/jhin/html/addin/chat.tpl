{extends file='tpl:comm.default'}

{block name='title'}
聊天室-{$roomname}
{/block}

{block name='body'}
{include file="tpl:comm.at"}
<div class="breadcrumb">
  <a href="index.index.{$bid}" title="回首页">回首页</a>
  {$roomname}
  <a href="addin.chat.{$bid}">切换聊天室</a>
  <a href="addin.chat.{$PAGE->ext[0]|code}.{$bid}?rand={time()}">刷新</a>
</div>
<div class="text-failure">{$err_msg}</div>
{if !$onlyReview}
<div class="widget-form">
  <form method="post" action="addin.chat.{$roomname}.{$bid}" class="chat-form">
    <div>
      <textarea id="content" name="content" class="chat-form-content">{$smarty.post.content|code:false:true}</textarea>
      <p>
        {if $USER->islogin}
        <input type="hidden" name="token" value="{$token->token()}">
        <input type="submit" id="quick_chat_button" name="go" id="submit" class="chat-form-submit" value="快速发言"/>
        <input type="button" id="add_files" value="添加附件" class="chat-form-submit" onclick="addFiles()"/>
        <a id="ubbHelp" href="bbs.topic.80645.{$BID}">UBB说明</a>
        {include file="tpl:comm.addfiles"}
        {else}
        必须<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>才能发言。
        {/if}
      </p>
    </div>
  </form>
</div>
{/if}
<div class="widget-page top-pager">
  {jhinfunc::Pager($p,$maxP,"?p=##")}
  {if $blockedReply}（屏蔽 <a style="padding:0" href="?p={$p}&amp;all=1">{$blockedReply}</a>）{/if}
</div>
<ul class="chat-list">
  {foreach $list as $k}
  {$tmp = $uinfo->uid($k.uid)}
  {$tmp = $uinfo->setUbbOpt($ubbs)}
  <li>
    <div class="chat-meta">
        <div class="chat-number">{if $onlyReview}<a class="floor-link" href="addin.chat.{urlencode($k.room)}.{$bid}?floor={$k.lid}#{$k.lid}">{$k.room} {$k.lid}楼</a>{else}<a class="floor-link" name="{$k.lid}" href="?floor={$k.lid}#{$k.lid}">{$k.lid}</a>{/if}</div>
		<div class="chat-avatar">
            <img src="{$uinfo->avatar()}" class="avatar">
        </div>
        <div class="chat-meta-name">
            <a href="user.info.{$k.uid}.{$BID}">{$k.uname|code}</a>
        </div>
        <div class="chat-actions">
            <a href="#" onclick="atAdd('{$k.uname|code}',this);return false">@Ta</a>
            {str::ago({$k.time})}
            {if $chat->canDel($k.uid,true)}
                <a href="?del={$k.id}&amp;p={$p}&amp;t={$smarty.server.REQUEST_TIME}" onclick="return confirm('您确定要删除该楼层？')">删</a>
            {/if}
        </div>
    </div>
    <div class="chat-content user-content" data-floorID="{$k.lid}" id="floor_content_{$k.lid}">
      {$ubbs->display($k.content,true)}
	</div>
	<div class="floor_fold_bar" id="floor_fold_bar_{$k.lid}"></div>
  </li>
  {/foreach}
</ul>

{include file="tpl:bbs.review-all"}

<div class="widget-page">
  {jhinfunc::Pager($p,$maxP,"?p=##")}
  {if $blockedReply}（屏蔽 <a style="padding:0" href="?p={$p}&amp;all=1">{$blockedReply}</a>）{/if}
</div>
<script>
// 自动折叠过长内容
	$(document).ready(function(){
		var maxHeight = 768;
		$(".chat-content").each(function(){
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
	});
</script>
{/block}
