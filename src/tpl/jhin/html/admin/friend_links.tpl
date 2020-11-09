{extends file='tpl:admin.layout'}
{block name='title'}
    友链管理
{/block}
{block name='body'}
    <style>
        .avatar {
            width: 32px;
            min-width: 32px;
            height: 32px;
            min-height: 32px;
            border-radius: 50%;
            vertical-align: middle;
        }
    </style>
    <div class="columns">
        <div class="column is-offset-2 is-8">
            <table class="table">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>网址</th>
                    <th>名称</th>
                    <th>用户</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {foreach $friend_links as $link}
                    {$maxId = max($maxId, $link.id)}
                    {$tmp = $uinfo->uid($link.uid)}
                    <tr>
                        <td>{$link.id}</td>
                        <td>
                            <a href="{$link.url|code}">{$link.url|code}</a>
                        </td>
                        <td>{$link.name|code}</td>
                        <td>
                            <img class="avatar" src="{$uinfo->avatar()|code}" />
                            <a href="user.info.{$link.uid}.{$bid}">{$uinfo.name|code}</a>
                        </td>
                        <td>
                            <a href="admin.friend_links.edit.{$bid}?id={$link.id}">修改</a>
                            <a href="javascript:
                                if(confirm('你确定要删除 {$link.url|code} ({$link.name|code}) 吗？'))
                                    document.querySelector('#del_link_{$link.id}').submit();">删除</a>
                            <form id="del_link_{$link.id}" action="admin.friend_links.del.{$bid}" method="post">
                                <input type="hidden" name="id" value="{$link.id}" />
                            </form>
                        </td>
                    </tr>
                {/foreach}
                    <tr><form action="admin.friend_links.add.{$bid}" method="post">
                        <td>{$maxId + 1}</td>
                        <td>
                            <input type="text" name="url" placeholder="网址" />
                        </td>
                        <td>
                            <input type="text" name="name" placeholder="名称" />
                        </td>
                        <td>
                            <input type="text" name="uid" placeholder="UID" />
                        </td>
                        <td>
                            <input type="submit" name="save" value="添加" placeholder="UID" />
                        </td>
                    </form></tr>
                </tbody>
            </table>
        </div>
    </div>
{/block}
