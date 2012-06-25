{include file="tpl:comm.head" title="出错啦！！"}
{div class="tip"}抱歉，由于开发人员的各种不小心，该页面在执行过程中发生了一些错误。{/div}
{div class="content"}错误代码：{span class="notice"}{$err->getcode()|code}{/span}{/div}
{div class="tip"}错误信息：{span class="notice"}{$err->getmessage()|code}{/span}{/div}
{div class="content"}错误发生在 {span class="notice"}{$err->getfile()|code}{/span} 的第 {span class="notice"}{$err->getline()|code}{/span} 行{/div}
{div class="title"}错误追踪信息：{/div}
{div class="content"}
{span class="notice"}{$err->getTraceAsString()|code:true}{/span}
{/div}
{include file="tpl:comm.foot"}