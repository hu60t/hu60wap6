{extends file='tpl:comm.default'}
{block name='title'}
	小说阅读 - {#SITE_NAME#}
{/block}
{block name='body'}

<div class="widget">
    <div class="widget">
        <div class="bar">
            小说列表
        </div>
    </div>
	<ul class="topic-ul">
		{foreach $bookList as $book}
			<li>
				<div class="topic-anchor">
                    {$book.type}
                </div>
                <div class="topic-title">
                    <a href="{$CID}.list.{$book.id}.{$BID}">{$book.title|code}</a>
                    <div class="topic-meta">
						作者: {$book.author} / 主要人物: {$book.characters|code}
                    </div>
                </div>
                <div class="topic-reply-count">
                    {$book.chapter_count}
                </div>
                <div class="topic-forum-name">
                    {$book.status}
                </div>
			</li>
		{/foreach}
	</ul>
	<div class="widget-page">
		{if $maxPage > 1}
            {jhinfunc::Pager($p,$maxPage,"{$cid}.{$pid}.##.{$bid}")}
		{/if}
	</div>
</div>
{/block}
