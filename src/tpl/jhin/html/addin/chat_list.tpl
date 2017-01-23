{extends file='tpl:comm.default'}

{block name='title'}
选择你喜欢的聊天室-进入聊天室
{/block}

{block name='body'}
<div class="breadcrumb">
  <a href="index.index.{$bid}" title="回首页" class="pt_z">回首页</a>
  <span class="pt_c">聊天室名称（输入自定义名称可自建聊天室）</span>
  <span class="pt_y"><a href="addin.chat.{$bid}">刷新</a></span>
</div>
<div class="widget-chat">
  <form method="post" action="addin.chat.{$bid}" class="chat-form">
    <p class="chat_quick_enter">
      <input type="text" name="roomname" id="username_LCxiI" class="txt" placeholder="聊天室名(例如:公共聊天室)" value=""/>
      <input type="submit" name="go" id="submit" class="cr_login_submit" value="快速进入"/>
    </p>
  </form>
  <p class="title">
    <span class=" a">聊天室列表</span>
  </p>
  <ul class="chat_list">
    {foreach $list as $k}
    <li>
      <a href="addin.chat.{urlencode($k.name)}.{$bid}">{$k.name} ({chat::time_trun(time()-$k.ztime)})</a>
    </li>
    {/foreach}
  </ul>
</div>
{/block}
