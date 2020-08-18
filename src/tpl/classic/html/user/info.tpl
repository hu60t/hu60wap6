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
<p class="txt">
 个性签名：{$uinfo->getinfo('signature')|code}<br/>
</p>
<p class="txt">
 联系方式：{$uinfo->getinfo('contact')|code}<br/>
</p>
<p class="txt">
 注册时间：{if $uinfo->regtime == 0}{if $uinfo->uid == $USER->uid}您{else}该用户{/if}是很久以前注册的，那时没有记录注册时间{else}{date('Y年m月d日 H:i:s',$uinfo->regtime)}{/if}
</p>
{if $user->uid != $uinfo->uid }
<p class="txt">
 交友：{if $isFollow}<a href="javascript:relationship({$uinfo->uid}, 'unfollow')">取消关注</a>{else}<a href="javascript:relationship({$uinfo->uid}, 'follow')">关注</a>{/if}
 / {if $isBlock}<a href="javascript:relationship({$uinfo->uid}, 'unblock')">取消屏蔽</a>{else}<a href="javascript:relationship({$uinfo->uid}, 'block')">屏蔽</a>{/if}
 / {if $hideUserCSS}<a href="javascript:relationship({$uinfo->uid}, 'showUserCSS')">显示小尾巴</a>{else}<a href="javascript:relationship({$uinfo->uid}, 'hideUserCSS')">隐藏小尾巴</a>{/if}<br/>
</p>
{/if}
<p class="txt">
发送：<a href="msg.index.send.{$uinfo.uid}.{$bid}">内信</a> / <a href="msg.index.chat.{$uinfo.uid}.{$bid}">聊天模式</a><br/>
</p>
<p class="txt">
查看：<a href="bbs.search.send.{$bid}?username={$uinfo->name|urlencode}">帖子</a> / <a href="bbs.search.send.{$bid}?username={$uinfo->name|urlencode}&searchType=reply">回复</a><br/>
</p>
<p class="txt">
状态：{if $blockPostStat}被禁言{else}正常{/if}
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
