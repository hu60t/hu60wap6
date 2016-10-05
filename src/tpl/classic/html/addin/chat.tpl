{include file="tpl:comm.head" title="聊天室-{$roomname}"}
<script>
    function atAdd(uid,that) {
        that.style.color = "red";
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
<div class="failure">{$err_msg}</div>
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
            {$tmp = $ubbs->setOpt('style.disable', $uinfo->hasPermission(UserInfo::PERMISSION_UBB_DISABLE_STYLE))}
        <div class="i">{$k.lid}. {$ubbs->display($k.content,true)}<br/>
            (<a href="user.info.{$k.uid}.{$BID}">{$k.uname|code}</a> <a href="#" onclick="atAdd('{$k.uname|code}',this);return false">@Ta</a> {date("m-d H:i:s",{$k.time})}{if $chat->canDel($k.uid,true)}/<a href="?del={$k.id}&amp;p={$p}&amp;t={$smarty.server.REQUEST_TIME}" onclick="return confirm('您确定要删除该楼层？')">删</a>{/if})
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
</div>
{include file="tpl:comm.foot"}
