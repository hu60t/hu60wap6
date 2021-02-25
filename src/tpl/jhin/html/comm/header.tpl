<div class="layout-head-inner">
  {if !$base}
  	{if false && is_object($user)}
  		<div class="profile" id="profile">
  		{if $user->uid}
  			{if $user->islogin}
  				{$MSG=msg::getInstance($USER)}
          <div class="userHeader">
            <img src="{$PAGE->getTplUrl("img/48_avatar_middle.jpg.gif")}" class="userAvatar"/>
            <span class='userName' id="userName">{$user->name|code}</span>
          </div>
          <div id="profileClose" class="profileClose">
            <i class="material-icons">close</i>
          </div>
          <div id="profileFilm" class="profileFilm"></div>
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
  				已掉线，<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登录</a>
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
    $("#profile").toggleClass("profileMobile");
  });
  $("#profileClose,#profileFilm").on("click",function(){
    $("#userName").trigger('click');
  })
});
</script>
