{extends file='tpl:comm.default'}
{block name='title'}
用户信息
{/block}
{block name='body'}
<style>
	table {
		word-break: break-all;
	}
</style>
<div class="breadcrumb">
  <a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
  <span class="pt_c">用户信息</span>
</div>
<p class="txt">
	<img src="{$uinfo->avatar()|code}" width="96"/><br/>
</p>
<table>
  <tr>
    <td>UID：</td>
    <td>{$uinfo->uid|code}</td>
  </tr>
  <tr>
      <td>用户名：</td>
      <td>{$uinfo->name|code}</td>
  </tr>
  <tr>
    <td>个性签名：</td>
    <td>{$uinfo->getinfo('signature')|code}</td>
  </tr>
  <tr>
    <td>联系方式：</td>
    <td>{$uinfo->getinfo('contact')|code}</td>
  </tr>
  <tr>
    <td>注册时间：</td>
    <td>
		{if $uinfo->regtime == 0}
			您是很久以前注册的，那时没有记录注册时间
		{else}
			{date('Y年m月d日 H:i:s',$uinfo->regtime)}
		{/if}
    </td>
  </tr>
  <tr>
    <td>发送：</td>
    <td>
      <a href="msg.index.send.{$uinfo.uid}.{$bid}">内信</a> / 
	  <a href="msg.index.chat.{$uinfo.uid}.{$bid}">聊天模式</a>
    </td>
  </tr>
  <tr>
    <td>查看：</td>
    <td>
      <a href="bbs.search.send.{$bid}?username={$uinfo->name|urlencode}">帖子</a>
    </td>
  </tr>
  <tr>
    <td>状态：</td>
    <td>
      {if $blockPostStat}被禁言{else}正常{/if}
      {if $showBlockButton} / <a href="user.block_post.{$uinfo.uid}.{$bid}">{if $blockPostStat}解除禁言{else}设置禁言{/if}</a>{/if}
    </td>
  </tr>
</table>
{/block}
