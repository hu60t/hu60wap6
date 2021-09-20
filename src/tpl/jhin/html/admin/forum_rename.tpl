{extends file='tpl:admin.layout'}
{block name='title'}
    板块管理
{/block}
{block name='body'}
    <div class="columns">
        <div class="column is-offset-2 is-4">
            <form action="admin.bbs.forum_rename.{$bid}?id={$forum.id}" method="post">
                <div class="field">
                    <label class="label">版块名称</label>
                    <div class="control">
                        <input class="input" type="text" name="name" value="{$forum.name}">
                    </div>
                </div>
                <div class="field">
                    <label class="label">父版块</label>
                    <div class="control">
                        <div class="select">
                            <select name="parent_id">
                                {foreach $forumList as $name=>$id}
                                    <option value="{$id}" {if $id == $forum.parent_id}selected{/if}>{$name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input type="submit" class="button" value="保存"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
{/block}
