{include file="tpl:comm.head" title="『聊天室』{$roomname}"}
{$roomname}<br />
{$err_msg}
{form action="addin.chat.{$roomname}.{$bid}" method="post"}
{input type="textarea" name="neirong"}<br/>
{input type="submit" name="go" value="快速发言"}<a href=''>刷新本页</a>
{/form}<br/>{hr}
{foreach $chatlist.row as $k}
{$k.lid}# {$ubbs->display($k.content,true)}<br />
(<a href="">{$k.uname}</a> {date("m-d h:i:s",{$k.time})})<hr />
{/foreach}
{$chatlist.px}<hr />
<a href="addin.chat.{$bid}">切换聊天室</a>
{include file="tpl:comm.foot"}