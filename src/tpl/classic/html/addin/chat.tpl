{include file="tpl:comm.head" title="聊天室-{$roomname}"}
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
				'" href="#" onclick="foldFold(' + floor + ');return false">折叠过长内容</a>';
	}
	
	function foldFloorInit(floor) {
		var content = document.getElementById('floor_content_' + floor);
        if (!content) return;
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
<div class="top_nav">
    <a href="index.index.{$bid}" title="回首页" class="pt_z">回首页</a>
    {$roomname}
    <span class="pt_c"><a href="addin.chat.{$bid}">切换聊天室</a></span>
    <span class="pt_y"><a href="addin.chat.{$PAGE->ext[0]|code}.{$bid}?rand={time()}">刷新</a></span>
</div>
<div class="failure">{$err_msg}</div>
{if !$onlyReview}
<div class="topic_area">
    <form method="post" action="addin.chat.{$roomname}.{$bid}">
        <div>
            <p>
                <textarea class="txt" id="content" name="content">{$smarty.post.content|code:false:true}</textarea>
            </p>
            <p>
                {if $USER->islogin}
                    <input type="hidden" name="token" value="{$token->token()}">
                    <input type="submit" id="quick_chat_button" name="go" id="submit" class="cr_login_submit" value="快速发言"/>
                    <input type="submit" id="preview_button" name="preview" value="预览"/>
                    <input type="button" id="add_files" value="添加附件" onclick="addFiles()"/>
                    <a id="ubbHelp" href="bbs.topic.80645.{$BID}">UBB说明</a>
                    {include file="tpl:comm.addfiles"}
                {else}
                    必须<a href="user.login.{$BID}?u={$PAGE->geturl()|urlenc}">登录</a>才能发言。
                {/if}
            </p>
        </div>
    </form>
</div>
<hr>
{/if}
{if $preview}
    <div class="tp" style="margin-bottom: 10px">
        预览：
    </div>
    <div class="topic-content user-content">
	    {$ubbs->display($preview, false)}
	</div>
    <hr>
{/if}
<div class="pager">
    {if $p < $maxP}<a href="?p={$p+1}">下一页</a>{/if}
    {if $p > 1}<a href="?p={$p-1}">上一页</a>{/if}
    {$p}/{$maxP}页,共{$count}楼
    <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='?p='+this.value; }">
</div>
<hr>
<div class="content">
    {foreach $list as $k}
        {$tmp = $uinfo->uid($k.uid)}
        {$tmp = $uinfo->setUbbOpt($ubbs)}
        <div class="i">
        <div class="floor_content user-content" id="floor_content_{$k.lid}">{if $onlyReview}<a class="floor-link" name="{$k.lid}" href="addin.chat.{urlencode($k.room)}.{$bid}?floor={$k.lid}#{$k.lid}">{$k.room} {$k.lid}楼</a>{else}<a class="floor-link" name="{$k.lid}" href="?{$k.lid}#{$k.lid}">{$k.lid}</a>{/if}<a name="/{$k.lid}"></a>. {$ubbs->display($k.content,true)}</div>
		<div class="floor_fold_bar" id="floor_fold_bar_{$k.lid}"></div>
		<script>foldFloorInit({$k.lid})</script>
		<div>(<a href="user.info.{$k.uid}.{$BID}">{$k.uname|code}</a> <a href="#" onclick="atAdd('{$k.uname|code}',this);return false">@Ta</a> {date("m-d H:i:s",{$k.time})}{if $chat->canDel($k.uid,true)}/<a href="?del={$k.id}&amp;p={$p}&amp;t={$smarty.server.REQUEST_TIME}" onclick="return confirm('您确定要删除该楼层？')">删</a>/{/if}<a href="javascript:hu60_user_style_toggle(document.querySelector('#floor_content_{$k.lid}'))">样</a>/<a href="javascript:hu60_content_display_ubb('addin.chat', {$k.id}, 'floor_content_{$k.lid}')">源</a>)</div>
        </div>
        <hr>
    {/foreach}
</div>

{include file="tpl:bbs.review-all"}

<div class="pager">
    {if $p < $maxP}<a href="?p={$p+1}">下一页</a>{/if}
    {if $p > 1}<a href="?p={$p-1}">上一页</a>{/if}
    {$p}/{$maxP}页,共{$count}楼
    <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='?p='+this.value; }">
</div>
{include file="tpl:comm.foot"}
