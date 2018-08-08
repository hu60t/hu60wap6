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
                    <th>父级板块</th>
                    <th>发帖数量</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {foreach $forum_list as $forum}
                    <tr>
                        <td>
                            <a href="bbs.forum.{$forum.id}.{$bid}">{$forum.name}</a>
                        </td>
                        <td>{$forum.parent_name}</td>
                        <td>{$forum.topic_sum}</td>
                        <td>
                            <a href="admin.bbs.forum_rename.{$bid}?id={$forum.id}">修改</a>
                            {*TODO: 实现删除板块的功能*}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/block}
