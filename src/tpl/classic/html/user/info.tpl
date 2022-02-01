{include file="tpl:comm.head" title="用户信息"}
{config_load file="conf:site.info"}
<div class="tp">
<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
<span class="pt_c">用户信息</span>
</div>
<p class="txt">
 <img src="{$uinfo->avatar()|code}" width="48"/><br/>
</p>
<p class="txt">
 UID：{$uinfo->uid|code}
</p>
<p class="txt">
 用户名：{$uinfo->name|code}<br/>
</p>
{if $USER->unlimit() || $uinfo->hasPermission(UserInfo::PERMISSION_REVIEW_POST)}
<p class="txt">
 个性签名：{$uinfo->getinfo('signature')|code}<br/>
</p>
<p class="txt">
 联系方式：{$uinfo->getinfo('contact')|code}<br/>
</p>
{/if}
<p class="txt">
 注册时间：{if $uinfo->regtime == 0}{if $uinfo->uid == $USER->uid}您{else}该用户{/if}是很久以前注册的，那时没有记录注册时间{else}{date('Y年m月d日 H:i:s',$uinfo->regtime)}{/if}
</p>
{if $USER->islogin && $USER->uid != $uinfo->uid}
<p class="txt">
 交友：
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
</p>
{/if}
<p class="txt">
发送：
  <a href="msg.index.send.{$uinfo.uid}.{$bid}">内信</a> /
  <a href="msg.index.chat.{$uinfo.uid}.{$bid}">聊天模式</a><br/>
</p>
<p class="txt">
查看：
  <a href="bbs.search.{$bid}?username={$uinfo->name|urlenc}">帖子</a> /
  <a href="bbs.search.{$bid}?username={$uinfo->name|urlenc}&searchType=reply">回复</a> /
  <a href="msg.index.chat.{$uinfo.uid}.{$bid}">内信</a> /
  <a href="msg.index.@.all.{$bid}?uid={$uinfo->uid}">@消息</a>
</p>
<p class="txt">
状态：
  {if $blockPostStat}被禁言{else}正常{/if}
  {if $showBlockButton} / <a href="user.block_post.{$uinfo.uid}.{$bid}">{if $blockPostStat}解除禁言{else}设置禁言{/if}</a>{/if}
</p>

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
{include file="tpl:comm.foot"}
