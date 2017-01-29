<div class="layout-head-inner">
  <div class="logo">
  	<img src="{$PAGE->getTplUrl("img/hulvlin2.gif")}">
  	<span>分享阳光，树木变成森林！</span>
  </div>
  {if !$base}
  	{if is_object($user)}
  		<div class="profile">
  		{if $user->uid}
  			{if $user->islogin}
  				{$MSG=msg::getInstance($USER)}
          <img src="{$PAGE->getTplUrl("img/48_avatar_middle.jpg.gif")}" class="userHead"/>
          <span class='userName' id="userName">{$user->name|code}</span>
          <ul class="userPanel" id="userPanel">
            <li><a href="addin.webplug.{$BID}">个性化</a></li>
            <li><a href="bbs.search.{$BID}?username={$USER->name|urlencode}">帖子</a></li>
            <li><a href="msg.index.html">信息</a></li>
            <li><a href="msg.index.@.{$bid}">@消息</a></li>
            <li><a href="user.exit.{$bid}?u={urlencode($page->geturl())}">退出</a></li>
            <li><a href="user.index.{$bid}">资料</a></li>
          </ol>

  				{$newMSG=$MSG->newMsg()}
  				{$newATINFO=$MSG->newAtInfo()}
  				{if $newMSG > 0}
            <div class="tips">
              <a href="msg.index.inbox.no.{$bid}">{$newMSG}条新内信</a>
            </div>
          {/if}
  				{if $newATINFO > 0}
            <div class="tips">
              <a href="msg.index.@.no.{$bid}">{$newATINFO}条新@消息</a>
            </div>
          {/if}



  			{else}
  				已掉线，<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登陆</a>
  			{/if}
  		{else}
  			<a href="user.login.{$bid}?u={urlencode($page->geturl())}" title="登录" style="margin-right:10px">登录</a>
  			<a href="user.reg.{$bid}?u={urlencode($page->geturl())}" title="立即注册">立即注册</a>
  		{/if}
  		</div>
  	{/if}
  {/if}
  <div style="clear:both;"></div>
</div>
<script>
$(document).ready(function(){
  $("#userName").on("click",function(){
    $("#userPanel").toggleClass("userPanelMobile");
  });
});
</script>
