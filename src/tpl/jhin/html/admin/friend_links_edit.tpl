{extends file='tpl:admin.layout'}
{block name='title'}
    修改友链
{/block}
{block name='body'}
    <div class="columns">
        <div class="column is-offset-2 is-4">
            <form action="admin.friend_links.edit.{$bid}?id={$link.id}" method="post">
                <div class="field">
                    <label class="label">序号</label>
                    <div class="control">
                        <input class="input" type="text" name="new_id" value="{$link.id}" />
                    </div>
                </div>
                <div class="field">
                    <label class="label">网址</label>
                    <div class="control">
                        <input class="input" type="text" name="url" value="{$link.url|code}" />
                    </div>
                </div>
                <div class="field">
                    <label class="label">名称</label>
                    <div class="control">
                        <input class="input" type="text" name="name" value="{$link.name|code}" />
                    </div>
                </div>
                <div class="field">
                    <label class="label">UID</label>
                    <div class="control">
                        <input class="input" type="text" name="uid" value="{$link.uid}" />
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input type="submit" class="button" name="save" value="保存" />
                        <a href="admin.friend_links.{$bid}">返回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
{/block}
