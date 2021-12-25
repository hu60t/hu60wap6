<form action="admin.bbs.createbk.{$bid}" method="post">
    <div class="field">
        <label class="label">版块名称</label>
        <div class="control">
            <input type="text" name="name" id="username_LCxiI" class="txt input" value=""/>
        </div>
    </div>
    <div class="field">
        <label class="label">父版块</label>
        <div class="control">
            <div class="select">
                <select name="parent_id">
                    {foreach $forumList as $v}
                        <option value="{$v.id}">{$v.title}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
    <div class="field">
        <div class="control">
            <input type="submit" name="yes" id="submit" class="cr_login_submit button" value="创建论坛板块"/>
        </div>
    </div>
</form>