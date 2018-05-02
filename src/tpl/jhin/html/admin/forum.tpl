{extends file='tpl:admin.layout'}
{block name='title'}
    板块管理
{/block}
{block name='body'}
    <div class="row row-mobile">
        <div class="col-4-4">
            <table class="list">
                <tr>
                    <th>名称</th>
                    <th>父级板块</th>
                    <th>发帖数量</th>
                    <th>操作</th>
                </tr>
                {foreach $forum_list as $forum}
                    <tr>
                        <td>
                            <a href="bbs.forum.{$forum.id}.{$bid}">{$forum.name}</a>
                        </td>
                        <td>{$forum.parent_name}</td>
                        <td>{$forum.topic_sum}</td>
                        <td><a href="admin.bbs.forum_rename.{$bid}?id={$forum.id}">修改</a> </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    </div>
{/block}
