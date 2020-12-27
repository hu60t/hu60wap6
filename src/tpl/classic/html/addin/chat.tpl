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
<div class="top_nav">
    <a href="index.index.{$bid}" title="回首页" class="pt_z">回首页</a>
    {$roomname}
    <span class="pt_c"><a href="addin.chat.{$bid}">切换聊天室</a></span>
    <span class="pt_y"><a href="addin.chat.{$PAGE->ext[0]|code}.{$bid}?rand={time()}">刷新</a></span>
</div>
<div class="failure">{$err_msg}</div>
<div class="topic_area">
    <form method="post" action="addin.chat.{$roomname}.{$bid}">
        <div>
            <p>
                <textarea class="txt" id="content" name="content" style="width:80%;height:100px;">{$smarty.post.content|code:false:true}</textarea>
            </p>
            <p>
                {if $USER->islogin}
                    <input type="hidden" name="token" value="{$token->token()}">
                    <input type="submit" id="quick_chat_button" name="go" id="submit" class="cr_login_submit" value="快速发言"/>
                    <input type="button" id="add_files" value="添加附件" onclick="addFiles()"/>
                    <a id="ubbHelp" href="bbs.topic.80645.{$BID}">UBB说明</a>
                    {include file="tpl:comm.addfiles"}
                {else}
                    必须<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>才能发言。
                {/if}
            </p>
        </div>
    </form>
</div>
<hr>
<div class="pager">
    {if $p < $maxP}<a href="?p={$p+1}">下一页</a>{/if}
    {if $p > 1}<a href="?p={$p-1}">上一页</a>{/if}
    {$p}/{$maxP}页,共{$count}楼
    <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='?p='+this.value; }">
    {if $blockedReply}(屏蔽 <a style="padding:0" href="?p={$p}&amp;all=1">{$blockedReply}</a>){/if}
</div>
<hr>
<div class="content">
    {foreach $list as $k}
        {if $k.hidden}
        <div class="i">
            {$tmp = $uinfo->uid($k.hidden)}
            {*if $k.hidden == $k.uid}
                {$k.lid}. 该楼层已被层主 <a href="#" onclick="atAdd('{$uinfo->name|code}',this);return false">@</a><a href="user.info.{$k.hidden}.{$BID}">{$uinfo->name|code}</a> 自行删除。
            {else*}
                {$k.lid}. 该楼层已被管理员 <a href="#" onclick="atAdd('{$uinfo->name|code}',this);return false">@</a><a href="user.info.{$k.hidden}.{$BID}">{$uinfo->name|code}</a> 删除，层主：<a href="#" onclick="atAdd('{$k.uname|code}',this);return false">@</a><a href="user.info.{$k.uid}.{$BID}">{$k.uname|code}</a>。
            {*/if*}
        </div>
        {else}
            {$tmp = $uinfo->uid($k.uid)}
            {$tmp = $uinfo->setUbbOpt($ubbs)}
        <div class="i">
        <div class="floor_content" id="floor_content_{$k.lid}"><a class="floor-link" name="{$k.lid}" href="?{$k.lid}#{$k.lid}">{$k.lid}</a>. {$ubbs->display($k.content,true)}</div>
		<div class="floor_fold_bar" id="floor_fold_bar_{$k.lid}"></div>
		<script>foldFloorInit({$k.lid})</script>
		<div>(<a href="user.info.{$k.uid}.{$BID}">{$k.uname|code}</a> <a href="#" onclick="atAdd('{$k.uname|code}',this);return false">@Ta</a> {date("m-d H:i:s",{$k.time})}{if $chat->canDel($k.uid,true)}/<a href="?del={$k.id}&amp;p={$p}&amp;t={$smarty.server.REQUEST_TIME}" onclick="return confirm('您确定要删除该楼层？')">删</a>{/if})</div>
        </div>
        {/if}
        <hr>
    {/foreach}
</div>
<div class="pager">
    {if $p < $maxP}<a href="?p={$p+1}">下一页</a>{/if}
    {if $p > 1}<a href="?p={$p-1}">上一页</a>{/if}
    {$p}/{$maxP}页,共{$count}楼
    <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='?p='+this.value; }">
    {if $blockedReply}(屏蔽 <a style="padding:0" href="?p={$p}&amp;all=1">{$blockedReply}</a>){/if}
</div>
{include file="tpl:comm.foot"}
