{extends file='tpl:admin.layout'}
{block name='title'}
    后台管理
{/block}
{block name='body'}
    <div class="row row-mobile">
        <div class="col-4-4">
            <table class="list">
                <tr>
                    <th colspan="2">
                        <h1>统计信息</h1>
                    </th>
                </tr>
                <tr>
                    <th>注册会员总数</th>
                    <td>{$site.user_sum}</td>
                </tr>
                <tr>
                    <th>帖子总数</th>
                    <td>{$site.topic_sum}</td>
                </tr>
                <tr>
                    <th>24小时活动会员</th>
                    <td>{$site.user_24h}</td>
                </tr>
                <tr>
                    <th>24发帖总数</th>
                    <td>{$site.topic_24h}</td>
                </tr>

                <tr>
                    <th>24回帖总数</th>
                    <td>{$site.reply_24h}</td>
                </tr>
            </table>
        </div>
    </div>
{/block}
