{include file="tpl:comm.head" title="选择你喜欢的聊天室-进入聊天室"}
<p class="top_nav">
    <a href="index.index.{$bid}" title="回首页" class="pt_z">回首页</a>
    <span class="pt_c">聊天室名称（输入自定义名称可自建聊天室）</span>
    <span class="pt_y"><a href="addin.chat.{$bid}">刷新</a></span>
</p>
<form method="post" action="addin.chat.{$bid}">
    <p class="chat_quick_enter">
        <input type="text" name="roomname" id="username_LCxiI" class="txt" placeholder="聊天室名(例如:公共聊天室)" value=""/>
        <input type="submit" name="go" id="submit" class="cr_login_submit" value="快速进入"/>
    </p>
</form>
<p class="title"><span class=" a">聊天室列表</span></p>
<ul class="chat_list">
    {foreach $list as $k}
        <li>
            <a href="addin.chat.{urlencode($k.name)}.{$bid}">{$k.name} ({chat::time_trun(time()-$k.ztime)})</a>
            {if $USER->hasPermission(UserInfo::PERMISSION_EDIT_TOPIC)}
                <form style="display: inline" action="addin.chat.{$bid}" method="post">
                    <input type="hidden" name="deleteroom" value="{$k.name}" />
                    <input type="submit" value="删除" onclick="return deleteChatRoomConfirm('{$k.name}')" />
                </form>
                <form style="display: inline" action="addin.chat.{$bid}" method="post">
                    <input type="hidden" name="emptyroom" value="{$k.name}" />
                    <input type="submit" value="清空" onclick="return emptyChatRoomConfirm('{$k.name}')" />
                </form>
            {/if}
        </li>
    {/foreach}
</ul>
<script>
  function deleteChatRoomConfirm(name) {
    return prompt("确定删除聊天室“"+name+"”？\n\n输入yes确定删除。") == 'yes'
  }
  function emptyChatRoomConfirm(name) {
    return prompt("确定清空聊天室“"+name+"”？\n\n输入yes确定清空。") == 'yes'
  }
</script>
{include file="tpl:comm.foot"}