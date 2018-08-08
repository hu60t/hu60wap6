{extends file='tpl:comm.default'}
{block name='title'}
    收信箱
{/block}
{block name='body'}
<div class="breadcrumb">
  收件箱：
  {if !in_array($PAGE.ext[1],['yes','no'])}全部{else}<a href="msg.index.inbox.all.{$bid}">全部</a>{/if}&nbsp;
  {if $PAGE.ext[1] == 'no'}未读{else}<a href="msg.index.inbox.no.{$bid}">未读</a>{/if}&nbsp;
  {if $PAGE.ext[1] == 'yes'}已读{else}<a href="msg.index.inbox.yes.{$bid}">已读</a>{/if}&nbsp;
  <a href="msg.index.send.{$bid}">发信</a>
</div>

{if !empty($list)}
<script>
    function checkCleanAll() {
        var req = prompt("您正在清空收件箱，所有发给你的内信（包括已读和未读）都将被永久删除，此操作不可恢复！\n" +
                         "请在输入框内输入“我要清空收件箱”（不包括引号）并点击确认按钮。");
        if (req != '我要清空收件箱') {
            alert('操作已取消');
            return false;
        }
        return true;
    }
</script>
<div class="msg_action">
<form action="{$PAGE->getUrl()}" method="post">
    <input type="hidden" name="clean" value="msg">
    <input type="hidden" name="actionToken" value="{$actionToken}">
    <input type="submit" name="action" value="全部设为已读">
    <input type="submit" name="action" value="清空收件箱" onclick="return checkCleanAll()">
</form>
</div>
<hr />
{foreach $list as $k}
{$tmp=$uinfo->uid($k.byuid)}
<div class="msg_box">
  <p>{if $k.isread==0}[新] {/if}来自：<a href="user.info.{$k.byuid}.{$bid}">{$uinfo.name}</a></p>
  <p>内容：<a href="msg.index.view.{$k.id}.{$bid}">{str::cut($ubbs->display($k.content,true),0,20,'...')|code}</a></p>
  <p>时间：{date("Y-m-d H:i:s",$k.ctime)}</p>
</div>
<hr />
{/foreach}
    <div class="widget-page">
        {jhinfunc::Pager($p,$pMax,"?p=##")}
    </div>
{else}
<div class="msg_empty">
  收件箱里空空的。
</div>
{/if}
<div class="breadcrumb">
聊天模式 |
<a href="msg.index.outbox.all.{$bid}">发件箱</a> |
<a href="msg.index.@.{$bid}">@消息</a>
</div>
{/block}
