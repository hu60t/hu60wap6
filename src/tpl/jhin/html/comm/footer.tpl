<div class="layout-foot-inner">
  {if !$base}
    {if !$no_chat}
      {$chat=chat::getInstance()}
      {$newChat=$chat->newChat()}
      {if !empty($newChat)}
        {$ubb=ubbDisplay::getInstance()}
        {$content=strip_tags($ubb->display($newChat.content, true))}
        <div class="chat-new">
          [<a href="addin.chat.{$newChat.room|code}.{$BID}">聊天-{$newChat.room|code}</a>]{$newChat.uname|code}:{str::cut($content,0,20,'…')}
        </div>
      {/if}
    {/if}
  {/if}
</div>
