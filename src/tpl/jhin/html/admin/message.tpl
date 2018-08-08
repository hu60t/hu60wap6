{extends file='tpl:admin.layout'}
{block name='title'}
    提示
{/block}
{block name='body'}
    <div class="columns">
        <div class="column is-offset-4 is-8">
            <article class="message">
                <div class="message-body">
                    {$message}
                </div>
            </article>
        </div>
    </div>
{/block}