{extends file='tpl:comm.default'}
{block name='title'}
    {$title}
{/block}
{block name='body'}
    <div class="pt breadcrumb">
        <a href="user.index.html" title="个人中心" class="pt_z">回个人中心</a> /
        <a href="index.index.html">首页</a>
    </div>

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

    {if count($userList) > 0 }
        <div class="widget-page">
            {$url="user.relationship.{$type}.{$bid}?page=##"}
            {jhinfunc::Pager($currentPage, $totalPage,$url)}
        </div>
    {/if}

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
{/block}
