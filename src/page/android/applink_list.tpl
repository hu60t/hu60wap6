{include file="tpl:comm.head" title="应用程序链接器"}

{foreach $list as $i=>$v}
  {div class="{cycle values="tip,notice"}"}
    app：{$v['dir']}<br/>
    {if $v['apptype'] == 'f'}
      <a href="{$CID}.applink.{$BID}?act=linkapp&app={$v['app']}">连接app</a> 未连接
    {else}
      已连接 <a href="{$CID}.applink.{$BID}?act=unlinkapp&app={$v['app']}">移回app</a> 
    {/if}
    <br/>
    {if $v['dirtype'] == 'd'}
      <a href="{$CID}.applink.{$BID}?act=linkdir&dir={$v['dir']}">连接目录</a> 未连接
    {else}
      已连接 <a href="{$CID}.applink.{$BID}?act=unlinkdir&dir={$v['dir']}">移回目录</a> 
    {/if}
  {/div}
{/foreach}

{include file="tpl:comm.foot"}
