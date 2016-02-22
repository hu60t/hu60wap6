{config_load file="conf:site.info"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{include file="tpl:comm.head" title="{$tMeta.title} - {$fName} - {#BBS_NAME#}"}
<script>
function atAdd(uid) {
  var nr = document.getElementById("content");
  nr.value += "@"+uid+"，";
}
</script>
{$ok=$ubb->setOpt('at.jsFunc', 'atAdd')}
<!--导航栏-->
<div class="pt">
<div class="cr180_ptzmenu">
    <a href="index.index.{$BID}">首页</a>
    {foreach $fIndex as $forum}
        <a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a>
        {if $forum.id != $fid}<!-- --!>{/if}
    {/foreach}
    {if !$forum.notopic}<a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a>{/if}
</div>
</div>
<div class="vt" id="th_list" style="margin-top:0">
    <div class="bm">
        <div class="cr180_title" style="clear:left">
        <a  id="thread_subject" > {$tMeta.title|code}</a></div>
    {foreach $tContents as $v}
                    <div class="cr180_postbox nos ">
                    <div id="post_2837" class="cr180_v_bmh cl">
                    <div class="cr180_avatar">
                    <a href="user.info.{$v.uinfo.uid}.{$BID}" target="_    blank" ><img src="http://www.wapvy.cn/uc_server/avatar.php?uid=1261&size=small" /></a>
                    </div>
                    <div class="z cr180_member_jon">
                    <a href="msg.index.send.{$v.uinfo.uid}.{$BID}" target="_blank" >{$v.uinfo.name|code}</a>
					<a href="#" onclick="atAdd('{$v.uinfo.name|code}');return false">@Ta</a>
                    <p class="cl">
                    <em class="dateline cus">{date('Y-m-d H:i:s',$v.mtime)}</em>
                    {if $bbs->canEdit($v.uinfo.uid,true)}<a href="{$CID}.edittopic.{$v.topic_id}.{$v.id}.{$BID}">编辑</a>{/if}
                    </p>
                    </div><a  class="view_author">{if $v.floor == 0}楼主{else}{$v.floor}楼{/if}</a></div>
<div class="pbody cl">
<div class="pbody_s1"></div>
<div class="pbody_s2 cl">
<div class="mes">
<div id="postmessage_{$tid}" class="postmessage">{$ubb->display($v.content,true)}</div>
</div></div></div>
    {/foreach}
    <div>
    {if $maxPage > 1}
        {if $p > 1}<a href="{$cid}.{$pid}.{$tid}.{$p-1}.{$bid}">上一页</a>{/if}
        {if $p < $maxPage}<a href="{$cid}.{$pid}.{$tid}.{$p+1}.{$bid}">下一页</a>{/if}
    ({$p}/{$maxPage})
    {/if}
    </div>
    <!--发帖框-->
<div id="Cr180return_commentform" style="display:none"></div>
<div class="ft">
        {if $USER->islogin}
            {form method="post" action="{$CID}.newreply.{$tid}.{$p}.{$BID}"}
 <div class="cr180_form">
                <textarea class="txt" id="content" name="content" style="width:100%;height:100px;">{$smarty.post.content}</textarea>
                {input type="hidden" name="token" value=$token->token()}
                </div><div class="o pns cl"><button type="submit" class="submit_ye" value="true" name="go" tabindex="3"><span>评论该帖子</span></button></div></form>
            {/form}
        {else}
{div class="forum_login"}
            回复需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。
{/div}
        {/if}
</div>
</div>
</div>
{include file="tpl:comm.foot"}
