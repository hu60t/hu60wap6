<div class="layout-foot-inner">
  {if !$base}
    {if !$no_chat}
      {$chat=chat::getInstance()}
      {if is_object($USER) && $USER->getinfo('chat.newchat_num') > 0}
        {$newChatNum=$USER->getinfo('chat.newchat_num')}
      {else}
        {$newChatNum=1}
      {/if}
      {$newChats=$chat->newChats($newChatNum)}
      {if !empty($newChats)}
        {$ubb=ubbDisplay::getInstance()}
        <div class="chat-new content-box">
          {foreach $newChats as $newChat}
            {$content=strip_tags($ubb->display($newChat.content, true))}
            <p>[<a href="addin.chat.{$newChat.room|code}.{$BID}">聊天-{$newChat.room|code}</a>] {$newChat.uname|code}：{str::cut($content,0,20,'…')}</p>
          {/foreach}
        </div>
      {/if}
    {/if}
  {/if}
</div>
