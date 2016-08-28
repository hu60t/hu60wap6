{include file="tpl:comm.head" title="聊天室-{$roomname}"}
<script>
    function atAdd(uid) {
        var nr = document.getElementById("content");
        nr.value += "@" + uid + "，";
    }
</script>
<div class="top_nav">
    <a href="index.index.{$bid}" title="回首页" class="pt_z">回首页</a>
    {$roomname}
    <span class="pt_c"><a href="addin.chat.{$bid}">切换聊天室</a></span>
    <span class="pt_y"><a href="addin.chat.{$PAGE->ext[0]|code}.{$bid}?rand={time()}">刷新</a></span>
</div>
{$err_msg}
<div class="topic_area">
    <form method="post" action="addin.chat.{$roomname}.{$bid}">
        <div>
            <p>
                <textarea class="txt" id="content" name="neirong" style="width:80%;height:100px;"></textarea>
            </p>
            <p>
                <input type="submit" id="quick_chat_button" name="go" id="submit" class="cr_login_submit" value="快速发言"/>
            </p>
        </div>
    </form>
</div>
<hr>
<div class="content">
    {foreach $list as $k}
        <div class="i">{$k.lid}. {$ubbs->display($k.content,true)}<br/>
            (<a href="user.info.{$k.uid}.{$BID}">{$k.uname|code}</a> <a href="#" onclick="atAdd('{$k.uname|code}');return false">@Ta</a> {date("m-d H:i:s",{$k.time})})
        </div>
        <hr>
    {/foreach}
</div>
<div class="pager">
    {if $p < $maxP}<a href="?p={$p+1}">下一页</a>{/if}
    {if $p > 1}<a href="?p={$p-1}">上一页</a>{/if}
    {$p}/{$maxP}页,共{$count}楼
    <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='?p='+this.value; }">
</div>
{include file="tpl:comm.foot"}
