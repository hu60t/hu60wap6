{extends file='tpl:admin.layout'}
{block name='title'}
    板块管理
{/block}
{block name='body'}
    <form action="admin.bbs.forum_rename.{$bid}?id={$forum.id}" method="post">
        <div class="row row-mobile form">
            <div class="col-4-1">
                名称：
            </div>
            <div class="col-4-3">
                <input type="text" name="name" value="{$forum.name}">
            </div>
            <div class="col-4-1">
                父级板块：
            </div>
            <div class="col-4-3">
                <select name="parent_id">
                        <option value="0">顶级板块</option>
                    {foreach $forum_list as $f}
                        <option value="{$f.id}" {if $f.id eq $forum.parent_id}selected{/if}>{$f.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-4-1"></div>
            <div class="col-4-3">
                <button type="submit">保存</button>
            </div>
        </div>
    </form>
{/block}
