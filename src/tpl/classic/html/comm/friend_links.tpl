<style>
.friend-link-item {
    display: inline-block;
    margin: 8px 10px;
}
.friend-link-item img {
    height: 32px;
    width: 32px;
    min-height: 32px;
    min-width: 32px;
    border-radius: 50%;
}
@media screen and (max-width: 600px) {
    .friend-link-item {
        margin: 3px 5px;
    }
    .friend-link-item img {
        height: 24px;
        width: 24px;
        min-height: 24px;
        min-width: 24px;
    }
}
</style>
<div class="friend-link-box">
{$uinfo = userinfo::getInstance()}
{$FRIEND_LINKS = FriendLinks::get()}
{foreach $FRIEND_LINKS as $k=>$link}
    <div class="friend-link-item">
        {if isset($link[2])}
            {$tmp=$uinfo->uid($link[2])}
            <a href="user.info.{$uinfo->uid}.{$BID}"><img src="{$uinfo->avatar()|code}" alt="{$uinfo->name|code}" /></a>
        {else}
            <a href="bbs.topic.86480.{$BID}"><img src="{page::getFileUrl("{AVATAR_DIR}/default.jpg")}" alt="默认头像" /></a>
        {/if}
		<a href="{$smarty.server.PHP_SELF}/link.friend.{$k}.html">{$link[0]|code}</a>
    </div>
{/foreach}
