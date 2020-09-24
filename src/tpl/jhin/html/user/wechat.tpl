{extends file='tpl:comm.default'}
{block name='title'}
用户中心
{/block}
{block name='body'}
<style>
	table {
		word-break: break-all;
	}
</style>
<div class="breadcrumb">
  <a href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
  <span class="pt_c">用户中心</span>
  <span class="pt_y"><a href="user.exit.{$bid}">退出</a></span>
</div>
{if $qrcode}
  <h3>微信扫码订阅虎绿林推送服务</h3>
  <p class="txt">
    <img src="{$qrcode.url|code}" width="240"/><br/>
  </p>
  <p>扫码后请<a href="{$CID}.{$PID}.{$BID}?r={time()}">手动刷新</a>查看是否成功</p>
{/if}
{if $wechat}
  <h3>您已订阅虎绿林微信推送服务</h3>
  <form method="post" action="{$CID}.{$PID}.{$BID}">
    <input type="submit" name="unsubscribe" value="退订" />
  </form>
{/if}
{/block}
