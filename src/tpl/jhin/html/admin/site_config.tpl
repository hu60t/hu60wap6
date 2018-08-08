{extends file='tpl:admin.layout'}
{block name='title'}
    后台管理
{/block}
{block name='body'}
    {if $smarty.post.yes}
        <div class='msg'>保存成功！</div>
        &lt;&lt;
        <a href="admin.site.index.{$bid}">返回</a>
    {else}
        <div class="columns">
            <div class="column is-offset-2 is-4">

                <form action="admin.site.index.{$bid}" method="post">
                    <div class="field">
                        <label class="label">网站标题：</label>
                        <div class="control">
                            <input class="input" type="text" name="site_name" value="{#SITE_NAME#}">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">网站简称：</label>
                        <div class="control">
                            <input class="input" type="text" name="site_simple_name" value="{#SITE_SIMPLE_NAME#}">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">论坛名称：</label>
                        <div class="col-4-3">
                            <input class="input" type="text" name="bbs_name" value="{#BBS_NAME#}">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">论坛首页名称：</label>

                        <div class="col-4-3">
                            <input class="input" type="text" name="bbs_index_name" value="{#BBS_INDEX_NAME#}">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">报时：</label>
                        <div class="col-4-3">
                            <input class="input" type="text" name="clock_name" value="{#CLOCK_NAME#}">
                        </div>
                    </div>
                    <div class="field">
                        <div class="col-4-3"><input class="button" type="submit" name="yes" value="保存">
                            <a class="button" href="admin.index.{$bid}">返回</a>
                        </div>
                </form>
            </div>
        </div>
    {/if}
{/block}
