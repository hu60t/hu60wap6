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
			{if $uinfo->uid == $USER->uid}您{else}该用户{/if}是很久以前注册的，那时没有记录注册时间
		{else}
			{date('Y年m月d日 H:i:s',$uinfo->regtime)}
		{/if}
    </td>
  </tr>
  {if $USER->islogin && $USER->uid != $uinfo->uid}
    <tr>
      <td>交友：</td>
      <td>
        {if $isFollow}
          <a href="javascript:relationship({$uinfo->uid}, 'unfollow')">取消关注</a>
        {else}
          <a href="javascript:relationship({$uinfo->uid}, 'follow')">关注</a>
        {/if} /
        {if $isBlock}
          <a href="javascript:relationship({$uinfo->uid}, 'unblock')">取消屏蔽</a>
        {else}
          <a href='javascript:if (confirm(
            "你确定要屏蔽该用户吗，将执行以下操作：\n1. 你在帖子列表中看不到该用户的帖子（搜索除外）。\n2. 你在回复列表中看不到该用户的回复（会显示屏蔽回复的数量，点击可临时解除屏蔽）。\n3. 你在聊天室中看不到该用户的发言（会显示屏蔽发言的数量，点击可临时解除屏蔽）。\n4. 该用户无法向你发送内信和@消息，你也无法向该用户发送内信和@消息。"
          )) relationship({$uinfo->uid}, "block")'>屏蔽</a>
        {/if} /
        {if $hideUserCSS}
          <a href="javascript:relationship({$uinfo->uid}, 'showUserCSS')">显示小尾巴</a>
        {else}
          <a href="javascript:relationship({$uinfo->uid}, 'hideUserCSS')">屏蔽小尾巴</a>
        {/if}
      </td>
    </tr>
  {/if}
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
      <a href="bbs.search.send.{$bid}?username={$uinfo->name|urlencode}">帖子</a> /
      <a href="bbs.search.send.{$bid}?username={$uinfo->name|urlencode}&searchType=reply">回复</a> /
      <a href="msg.index.chat.{$uinfo.uid}.{$bid}">内信</a> /
      <a href="msg.index.@.all.{$bid}?uid={$uinfo->uid}">@消息</a>
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
<script>
  function relationship(targetUid, type) {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'user.relationship.{$bid}', false);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        let data = JSON.parse(xhr.responseText);
        if (data.success) {
          window.location.reload();
        } else {
          alert(data.message);
        }
      } else {
        alert('请求失败');
      }
    };
    xhr.send('action=' + type + "&targetUid=" + targetUid);
  }
</script>
{/block}
