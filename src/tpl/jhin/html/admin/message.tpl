{extends file='tpl:admin.layout'}
{block name='title'}
    提示
{/block}
{block name='body'}
    <div class="row row-mobile">
        <div class="col-4-4" id="message">
            {$message}
        </div>
    </div>
    <style>
        #message {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
        }
    </style>
{/block}