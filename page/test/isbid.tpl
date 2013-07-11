{include file="tpl:comm.head" title="isbid测试"}
{div class="tip"}
{isxhtml}这是WAP2.0版{/isxhtml}
{iswml}这是WAP1.0版{/iswml}。切换版本看看。{/div}
{div class="content"}
在xhtml看来，{span style="color:#ff0000;"}这是红色的{/span}。对吗？
{/div}
{div class="title"}载入配置文件测试{/div}
{div class="content"}
{config_load file="conf:test"}
{#test#}<br/>
{#test2#}
{/div}
{include file="tpl:comm.foot"}