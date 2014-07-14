{include file="tpl:comm.head" title="『进入聊天室』"}
聊天室名称（输入自定义名称可自建聊天室）<br />
{form action="addin.chat.{$bid}" method="get"}
{input name="roomname" value="公共聊天室"}<br />
{input type="submit" name="go" value="快速进入"}
{/form}<br/>{hr}
聊天室列表：<br />
{foreach $list.row as $k}
<a href="addin.chat.{$k.name}.{$bid}">{$k.name}</a>({$k.ctime})<br />
{/foreach}
{$list.px}<hr />
{include file="tpl:comm.foot"}