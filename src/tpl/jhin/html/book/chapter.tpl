{extends file='tpl:comm.default'}
{block name='title'}
	{$chapterMeta.title|code} - {$bookMeta.title|code}
{/block}
{block name='body'}

<div class="widget">
    <div class="widget">
        <div class="bar">
            <a href="{$CID}.index.{$BID}">小说列表</a> > 
            <a href="book.list.{$bookId}.{$BID}">{$bookMeta.title|code}</a> >
            {$chapterMeta.title|code}
        </div>
    </div>
	<p class="book-content">
        {$chapterMeta.content|code:2}
    </p>
	<div class="widget-page">
        {jhinfunc::Pager($chapter,$chapterCount,"{$cid}.{$pid}.{$bookId}.##.{$bid}")}
	</div>
</div>
{/block}
