<ul class="topic-ul">
    {foreach $topicList as $topic}
        <li>
            <div class="topic-anchor">
                <a href="user.info.{$topic.uinfo.uid}.{$BID}">
                <img src="{$topic.uinfo->avatar()}" class="avatar">
                </a>
                <a href="user.info.{$topic.uinfo.uid}.{$BID}">{$topic.uinfo.name|code}</a>
            </div>
            <div class="topic-title">
                <a href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>
                <div class="topic-meta">
                    {$topic.read_count}点击 / {str::ago($topic.mtime)}
                </div>
            </div>
            <div class="topic-reply-count">
                <a href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.reply_count}</a>
            </div>
        </li>
    {/foreach}
</ul>
