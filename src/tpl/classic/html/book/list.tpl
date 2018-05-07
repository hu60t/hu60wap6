{include file="tpl:comm.head" title="{$bookMeta.title|code} - 小说阅读"}
<div class="widget">
    <div class="widget">
        <div class="bar">
            <a href="{$CID}.index.{$BID}">小说列表</a> > {$bookMeta.title|code}
        </div>
    </div>
	<ul class="topic-ul">
		{foreach $chapterList as $chapter}
			<li>
				<div class="topic-anchor">
                    {$chapter.chapter}
                </div>
                <div class="topic-title">
                    <a href="book.chapter.{$bookId}.{$chapter.chapter}.{$BID}">{$chapter.title|code}</a>
                    <div class="topic-meta">
						更新时间: {str::ago($chapter.mtime)}
                    </div>
                </div>
			</li>
		{/foreach}
	</ul>
	<div class="widget-page">
		{if $maxPage > 1}
            {jhinfunc::Pager($p,$maxPage,"{$cid}.{$pid}.{$bookId}.##.{$bid}")}
		{/if}
	</div>
</div>
{include file="tpl:comm.foot"}
