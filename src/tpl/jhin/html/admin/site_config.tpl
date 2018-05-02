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
        <form action="admin.site.index.{$bid}" method="post">
            <div class="row row-mobile">
                <div class="col-4-1">网站标题：</div>
                <div class="col-4-3">
                    <input type="text" name="site_name" value="{#SITE_NAME#}">
                </div>
                <div class="col-4-1">网站简称：</div>
                <div class="col-4-3">
                    <input type="text" name="site_simple_name" value="{#SITE_SIMPLE_NAME#}">
                </div>
                <div class="col-4-1">论坛名称：</div>
                <div class="col-4-3">
                    <input type="text" name="bbs_name" value="{#BBS_NAME#}">
                </div>
                <div class="col-4-1">论坛首页名称：</div>

                <div class="col-4-3">
                    <input type="text" name="bbs_index_name" value="{#BBS_INDEX_NAME#}">
                </div>
                <div class="col-4-1">报时：</div>
                <div class="col-4-3">
                    <input type="text" name="clock_name" value="{#CLOCK_NAME#}">
                </div>
                <div class="col-4-1"></div>
                <div class="col-4-3"><input type="submit" name="yes" value="保存"></div>
                &lt;&lt;<a href="admin.index.{$bid}">返回</a>
            </div>
        </form>
    {/if}
{/block}
