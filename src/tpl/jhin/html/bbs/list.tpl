<ul class="topic-ul">
  {foreach $topicList as $topic}
    <li>
      <a href="{$CID}.topic.{$topic.topic_id}.{$BID}" class="topic-title">{$topic.title|code}</a>
      <div class="topic-meta">
        (
        <span class="topic-author"><a href="user.info.{$topic.uinfo.uid}.{$BID}">{$topic.uinfo.name|code}</a></span>/
        {$topic.read_count}点击/
        {$topic.reply_count}回复/{str::ago($topic.time)}
        )
      </div>
    </li>
  {/foreach}
</ul>
