{config_load file="conf:site.info"}
{include file="tpl:comm.head" title=$title}

<div class="bar breadcrumb">
    {if $type == "follow"}我关注的{else}<a href="{$CID}.{$PID}.follow.{$BID}">我关注的</a>{/if} |
    {if $type == "block"}我屏蔽的{else}<a href="{$CID}.{$PID}.block.{$BID}">我屏蔽的</a>{/if} |
    {if $type == "follow_me"}关注我的{else}<a href="{$CID}.{$PID}.follow_me.{$BID}">关注我的</a>{/if} |
    {if $type == "block_me"}屏蔽我的{else}<a href="{$CID}.{$PID}.block_me.{$BID}">屏蔽我的</a>{/if} |
    {if $type == "no_disturb"}免打扰{else}<a href="{$CID}.{$PID}.no_disturb.{$BID}">免打扰</a>{/if}
</div>
<hr>
<div class="relationship-wrapper">
    {if count($userList) > 0 }
            {foreach $userList as $user}
                <div class="user-item">
                    <div class="avatar">
                        <a href="user.info.{$user->uid|code}.{$BID}"><img src="{$user->avatar()}"></a>
                    </div>
                    <div class="info">
                        <div class="name_action">
                            <div class="name">
                                <a href="user.info.{$user->uid|code}.{$BID}">{$user->name|code}</a>
                            </div>
                            <div class="action_bar">
                                {if !$inverseRelationship || !$inverseRelationship[$user->uid]}
                                    {foreach $actions as $action=>$actionName}
                                        <a class="action" href="javascript:relationship({$user->uid}, '{$action}')">{$actionName}</a>
                                    {/foreach}
                                {else}
                                    {foreach $inverseActions as $action=>$actionName}
                                        <a class="action" href="javascript:relationship({$user->uid}, '{$action}')">{$actionName}</a>
                                    {/foreach}
                                {/if}
                            </div>
                        </div>
                        <div class="signature">{$user->getinfo('signature')|code}</div>
                    </div>
                </div>
            {/foreach}
    {else}
        <div class="empty">暂无用户</div>
    {/if}
</div>

<div class="pager">
    {if $currentPage < $totalPage}<a style="display:inline" href="user.relationship.{$type}.{$bid}?page={$currentPage + 1}">下一页</a>{/if}
    {if $currentPage > 1}<a style="display:inline" href="user.relationship.{$type}.{$bid}?page={$currentPage-1}">上一页</a>{/if}
    {if $totalPage > 1}({$currentPage} / {$totalPage}页){/if}
</div>

<script>
    function relationship(targetUid, type) {
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'user.relationship.{$bid}', false);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                let data = JSON.parse(xhr.responseText);
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } else {
                alert('请求失败');
            }
        };
        xhr.send('action=' + type + "&targetUid=" + targetUid);
    }
</script>
{include file="tpl:comm.foot"}
