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
<p class="txt">
	<img src="{$USER->avatar()|code}" width="96"/><br/>
</p>
<table>
  <tr>
    <td>UID：</td>
    <td>{$USER->uid|code}</td>
  </tr>
  <tr>
      <td>用户名：</td>
      <td>{$USER->name|code}</td>
  </tr>
  <tr>
    <td>邮箱：</td>
    <td>{$USER->mail|code}</td>
  </tr>
  <tr>
    <td>个性签名：</td>
    <td>{$USER->getinfo('signature')|code}</td>
  </tr>
  <tr>
    <td>联系方式：</td>
    <td>{$USER->getinfo('contact')|code}</td>
  </tr>
  <tr>
    <td>注册时间：</td>
    <td>
		{if $USER->regtime == 0}
			您是很久以前注册的，那时没有记录注册时间
		{else}
			{date('Y年m月d日 H:i:s',$USER->regtime)}
		{/if}
    </td>
  </tr>
  <tr>
    <td>查看：</td>
    <td>
      <a href="msg.index.{$bid}">内信</a> / 
	    <a href="msg.index.@.{$bid}">@消息</a> / 
	    <a href="bbs.search.{$bid}?username={$USER->name|urlenc}">帖子</a> /
      <a href="bbs.search.{$bid}?username={$USER->name|urlenc}&searchType=reply">回复</a> /
      <a href="bbs.myfavorite.{$bid}">收藏</a> /
      <a href="user.relationship.follow.{$bid}">关注</a> /
      <a href="user.relationship.block.{$bid}">屏蔽</a>
    </td>
  </tr>
  <tr>
    <td>更改：</td>
    <td>
    <a href="{$cid}.avatar.{$bid}">头像</a> / 
	  <a href="{$cid}.chname.{$bid}">用户名</a> / 
	  <a href="{$cid}.chpwd.{$bid}">密码</a> / 
	  <a href="{$cid}.chinfo.{$bid}">个性签名&amp;联系方式</a>
    </td>
  </tr>
  <tr>
    <td>绑定：</td>
    <td>
		{if $hasRegPhone}
			已绑定手机号
		{else}
			<a href="{$CID}.active.{$BID}?sid={$USER->sid}">手机号</a>
		{/if} /
    <a href="{$CID}.wechat.{$BID}">微信推送</a>: {$wechat = $USER->getinfo('wechat')}{if $wechat.uid}开{else}关{/if}
    </td>
  </tr>
  <tr>
    <td>主题：</td>
    <td>
      <a href="link.tpl.classic.{$BID}?url64={code::b64e($page->geturl())}">经典主题</a> / 
      Jhin主题
    </td>
  </tr>
  <tr>
    <td>夜间模式：</td>
    <td id="dark_mode_bar"></td>
    <script>
    window.addEventListener('load', function () {
        var scheme = hu60_read_color_scheme_option();
        var options = {
            auto: '跟随系统', 'dark': '开', 'light': '关'
        };
        var select = document.createElement("select");
        select.id = "hu60-color-scheme";
        for (var key in options) {
            var option = document.createElement("option");
            option.value = key;
            option.text = options[key];
            if (key == scheme) {
                option.selected = true;
            }
            select.appendChild(option);
        }
        var box = document.querySelector('#dark_mode_bar');
        if (!box) return;
        box.appendChild(select);
        document.getElementById('hu60-color-scheme').addEventListener('change', function (ev) {
            hu60_set_color_scheme(this.value);
        });
    });
    </script>
  </tr>
  <tr>
    <td>楼层排序：</td>
    <td>
		{if $floorReverse}
			<a href="?floorReverse=0">正序</a> / 倒序
		{else}
			正序 / <a href="?floorReverse=1">倒序</a>
		{/if}
    </td>
  </tr>
  <tr>
    <td>底部聊天室个数：</td>
    <td>
      <form method="post" action="{$CID}.{$PID}.{$BID}">
        <input name="newChatNum" type="number" min="1" max="10" value="{$newChatNum}" />
        <input type="submit" value="保存">
      </form>
    </td>
  </tr>
  <tr>
    <td>功能：</td>
    <td>
      <a href="addin.webplug.{$BID}">网页插件</a> / 
	  <a href="addin.jhtml.{$BID}">JHTML</a>
    </td>
  </tr>
  <tr>
    {if $mmbt}
    <td>管理员：</td>
    <td>
      <a href='admin.index.{$bid}' class="cr_login_submit">管理后台</a>
    </td>
    {/if}
  </tr>
  <tr>
    <td>HEIF图片缓存：</td>
    <td>
      <span id="cache_size">计算中...</span>
      <input id="clean_cache" type="button" value="清除缓存" />
    </td>
  </tr>
</table>
<script src="{$PAGE->getTplUrl('js/humanize/humanize.js')}"></script>
<script src="{$PAGE->getTplUrl('js/heif-web-display/dist/utils.js')}"></script>
<script>
(function() {
    async function getCacheSize() {
        const cacheSize = await document.HeicToPngCacheSize();
        console.log(cacheSize);
        document.querySelector('#cache_size').innerText = cacheSize.count + '条, ' + humanize.filesize(cacheSize.size);
    }
    async function cleanCache() {
        await document.CleanHeicToPngCache();
        getCacheSize();
    }
    window.addEventListener('load', getCacheSize);
    document.querySelector('#clean_cache').addEventListener('click', cleanCache);
})()
</script>
{/block}
