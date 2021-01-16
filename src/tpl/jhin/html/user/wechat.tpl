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
  <h3>微信扫码绑定虎绿林推送服务</h3>
  <p class="txt">
    <img src="{$qrcode.url|code}" width="240"/><br/>
  </p>
  <p>扫码并关注公众号，然后<a href="{$CID}.{$PID}.{$BID}?r={time()}">手动刷新</a>查看是否成功</p>
{/if}
{if $wechat}
  <h3>您已绑定虎绿林微信推送服务</h3>
  <table>
    <tr>
      <td>绑定用户：</td>
      <td>{$wechat.userName|code}</td>
    </tr>
    <tr>
      <td></td>
      <td><img src="{$wechat.userHeadImg|code}" alt="{$wechat.userName|code}" /></td>
    </tr>
    <tr>
      <td>绑定时间：</td>
      <td>{date('Y-m-d H:i:s', $wechat.time / 1000)}</td>
    </tr>
  </table>
  <hr>
  <form method="post" action="{$CID}.{$PID}.{$BID}">
    <input type="submit" name="unsubscribe" value="解绑" />
  </form>
{/if}
<hr>
<p class="txt">
  微信推送服务暂时只会推送内信和@消息，更多功能开发中……
</p>
<hr>
<div class="breadcrumb">
  <a href="msg.index.inbox.all.{$bid}">收件箱</a> |
  <a href="msg.index.outbox.all.{$bid}">发件箱</a> |
  <a href="msg.index.@.{$bid}">@消息</a> |
  微信推送: {$wechat = $USER->getinfo('wechat')}{if $wechat.uid}开{else}关{/if}
</div>
{/block}
