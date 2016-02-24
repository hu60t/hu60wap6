{include file="tpl:comm.head" title="聊天室-{$roomname}"}
<script>
	function atAdd(uid) {
		var nr = document.getElementById("content");
		nr.value += "@"+uid+"，";
	}
</script>
<div class="pt">
<div class="cr180_ptzmenu">
                <a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="回首页" class="pt_z">回首页</a>
            <span class="pt_c"><a  href="javascript:;" onclick="location.href='addin.chat.{$bid}'" >切换聊天室</a>{$roomname}</span>
<span class="pt_y"><a href="addin.chat.{$PAGE->ext[0]|code}.{$bid}?rand={time()}">刷新</a></span>
</div>
</div>
{$err_msg}
<div class="topic_area">
    <div class="cr180_form">
    <form method="post" action="addin.chat.{$roomname}.{$bid}"><div >
<p>
        <textarea class="txt" id="content" name="neirong" style="width:80%;height:100px;"></textarea>
<p>
</p>
    <p><input type="submit" name="go" id="submit" class="cr_login_submit" value="快速发言" /></p>
            </div>
	</form>    </div>
	<hr>
<div class="content">
{foreach $list.row as $k}
<div class="i">{$k.lid}. {$k.content}<br />
(<a href="user.info.{$k.uid}.{$BID}">{$k.uname|code}</a> <a href="#" onclick="atAdd('{$k.uname|code}');return false">@Ta</a> {date("m-d H:i:s",{$k.time})})
</div>
<hr>
{/foreach}
</div>
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="回首页" class="pt_z">回首页</a>
            <span class="pt_c">{$list.px}</span>
<span class="pt_y"><a href="">刷新</a></span>
</div>
</div>
{include file="tpl:comm.foot"}