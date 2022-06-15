{include file="tpl:comm.head" title="用户中心" no_user=true}
{config_load file="conf:site.info"}
<div class="tp">
<a href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
<span class="pt_c">用户中心</span>
<span class="pt_y"><a href="user.exit.{$bid}">退出</a></span>
</div>
<p class="txt">
 <img src="{$USER->avatar()|code}" width="48"/><br/>
</p>
<p class="txt">
 UID：{$USER->uid|code}
</p>
<p class="txt">
 用户名：{$USER->name|code}<br/>
</p>
<p class="txt">
 邮箱：{$USER->mail|code}<br/>
</p>
<p class="txt">
 个性签名：{$USER->getinfo('signature')|code}<br/>
</p>
<p class="txt">
 联系方式：{$USER->getinfo('contact')|code}<br/>
</p>
<p class="txt">
 注册时间：{if $USER->regtime == 0}您是很久以前注册的，那时没有记录注册时间{else}{date('Y年m月d日 H:i:s',$USER->regtime)}{/if}
</p>
<p class="txt">
查看：<a href="msg.index.{$bid}">内信</a> /
	 <a href="msg.index.@.{$bid}">@消息</a> /
	 <a href="bbs.search.{$bid}?username={$USER->name|urlenc}">帖子</a> /
	 <a href="bbs.search.{$bid}?username={$USER->name|urlenc}&searchType=reply">回复</a> /
	 <a href="bbs.myfavorite.{$bid}">收藏</a> /
     <a href="user.relationship.follow.{$bid}">关注</a> /
	 <a href="user.relationship.block.{$bid}">屏蔽</a><br/>
</p>
<p class="txt">
更改：<a href="{$cid}.avatar.{$bid}">头像</a> /
	 <a href="{$cid}.chname.{$bid}">用户名</a> /
	 <a href="{$cid}.chpwd.{$bid}">密码</a> /
	 <a href="{$cid}.chinfo.{$bid}">个性签名/联系方式</a>
</p>
<p class="txt">
绑定：{if $hasRegPhone}已绑定手机号{else}<a href="{$CID}.active.{$BID}?sid={$USER->sid}">手机号</a>{/if} /
	 <a href="{$CID}.wechat.{$BID}">微信推送</a>: {$wechat = $USER->getinfo('wechat')}{if $wechat.uid}开{else}关{/if}
</p>
<p class="txt">
主题：经典主题 /
	 <a href="link.tpl.jhin.{$BID}?url64={code::b64e($page->geturl())}">Jhin主题</a><br/>
</p>
<p class="txt" id="dark_mode_bar">夜间模式：</p>
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
<p class="txt">
论坛楼层排序：
{if $floorReverse}
	<a href="?floorReverse=0">正序</a> / 倒序
{else}
	正序 / <a href="?floorReverse=1">倒序</a>
{/if}
<br/>
</p>
<form class="txt" method="post" action="{$CID}.{$PID}.{$BID}"> 
	底部聊天室个数：
	<input name="newChatNum" type="number" min="1" max="10" value="{$newChatNum}" />
    <input type="submit" value="保存">
</form>
<p class="txt">
功能：<a href="addin.webplug.{$BID}">网页插件</a> / <a href="addin.jhtml.{$BID}">JHTML</a>
<br/>
</p>
{if $mmbt}
<p class="txt">
进入：<a href='admin.index.{$bid}' class="cr_login_submit">管理后台</a>
<br/>
</p>
{/if}
<p class="txt">
  HEIF图片缓存：
  <span id="cache_size">计算中...</span>
  <input id="clean_cache" type="button" value="清除缓存" />
  <br/>
</p>
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
{include file="tpl:comm.foot"}
