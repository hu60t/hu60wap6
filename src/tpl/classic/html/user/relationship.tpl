{config_load file="conf:site.info"}
{include file="tpl:comm.head" title=$title}

<div class="relationship-wrapper">
    {if count($userList) > 0 }
        {foreach $userList as $user}
            <div  class="user-item">
                <a href="user.info.{$user->uid|code}.{$BID}">
                    <img src="{$user->avatar()}" class="avatar">
                    <div class="info">
                        <div class="name">{$user->name|code}</div>
                        <div class="signature">{$user->getinfo('signature')|code}</div>
                    </div>
                </a>
                <a class="remove" href="javascript:relationship({$user->uid}, 'un{$type}')">移除</a>
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
