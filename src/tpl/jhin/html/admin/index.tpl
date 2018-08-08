{extends file='tpl:admin.layout'}
{block name='title'}
    后台管理
{/block}
{block name='body'}
    <nav class="level">
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">注册会员总数</p>
                <p class="title">{$site.user_sum}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">帖子总数</p>
                <p class="title">{$site.topic_sum}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">24小时活动会员</p>
                <p class="title">{$site.user_24h}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">24发帖/回帖总数</p>
                <p class="title">{$site.topic_24h}/{$site.reply_24h}</p>
            </div>
        </div>
    </nav>
{/block}
