{extends file='tpl:admin.layout'}
{block name='title'}
    板块管理
{/block}
{block name='body'}
    <div class="columns">
        <div class="column is-offset-2 is-8">
            <table class="table">
                <thead>
                <tr>
                    <th>名称</th>
                    <th>发帖数量</th>
                    <th>可访问用户组</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {foreach $forumList as $v}
                    <tr>
                        <td>
                            <a href="bbs.forum.{$v.id}.{$bid}">{$v.title}</a>
                        </td>
                        <td>{$topicSum[$v.id]}</td>
                        <td>{str::bitset2str($v.access)}</td>
                        <td>
                            <a href="admin.bbs.forum_rename.{$bid}?id={$v.id}">修改</a>
                            {*TODO: 实现删除板块的功能*}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/block}
