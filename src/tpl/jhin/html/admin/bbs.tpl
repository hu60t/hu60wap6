{extends file='tpl:admin.layout'}
{block name='title'}
    后台管理
{/block}
{block name='body'}
    {*TODO: 清除多余内容*}
    <div class="columns">
        <div class="column is-offset-2 is-4">
            {if $PAGE->ext[0]=='createbk'}
                {if $smarty.post.yes}
                    <article class="message">
                        <div class="message-body">
                            <p>
                                {if $smarty.post.name}
                            <div class='msg'>创建成功!</div>
                            {else}
                            <div class='msg'>创建失败!内容不能为空。</div>
                            {/if}
                            </p>
                            <p><a href="javascript:history.back();">[ 点击这里返回上一页 ]</a></p>
                        </div>
                    </article>
                {else}
                    {include file='tpl:bbs_create'}
                {/if}
            {elseif $page->ext[0]=='bk'}
                {if $smarty.post.sc}
                    <article class="message">
                        <div class="message-body">
                            <p>删除板块，成功删除！</p>
                            <p><a href="javascript:history.back();">[ 点击这里返回上一页 ]</a></p>
                        </div>
                    </article>
                {elseif $smarty.post.xg}
                    {include file='tpl:bbs_modifier'}
                {else}
                    <form action="admin.bbs.bk.{$bid}" method="post">
                        <div class='cr180_form'>
                            <div>
                                <p>{select name="bbid" option=$array}<br/>
                                </p>
                                <p>
                                    <input type="submit" name="xg" id="submit" class="cr_login_submit"
                                           style="width:49%;"
                                           value="修改论坛板块"/>
                                    <input type="submit" name="sc" id="submit" class="cr_login_submit"
                                           style="width:49%;"
                                           value="删除论坛板块"/>
                                </p>
                            </div>
                        </div>
                    </form>
                {/if}
            {/if}

        </div>
    </div>
{/block}
