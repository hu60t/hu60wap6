{include file="tpl:comm.head" title="查看信息"}
查看信息：<hr />
发给:{$msg.touid}<br />
来自:{$msg.byuid}<br />
状态:{if $msg.isread==0}(未读){else}(已读){/if}<br />
内容:{$msg.content}<br />
发送时间:{date('Y-m-d H:i:s',$msg.ctime)}<br />
{if $msg.rtime}阅读时间:{date('Y-m-d H:i:s',$msg.rtime)}{/if}<hr />
{include file="tpl:comm.foot"}