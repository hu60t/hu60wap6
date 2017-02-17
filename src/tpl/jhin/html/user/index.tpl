{extends file='tpl:comm.default'}
{block name='title'}
用户中心
{/block}
{block name='body'}
<div class="breadcrumb">
  <a href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
  <span class="pt_c">用户中心</span>
  <span class="pt_y"><a href="user.exit.{$bid}">退出</a></span>
</div>
<!--p class="txt">
<img src="{$USER->getinfo('avatar.url')|code}" width="30"/><br/>
</p-->
<table>
  <tr>
    <td>
      UID：{$USER->uid|code}
    </td>
    <td>
      用户名：{$USER->name|code}
    </td>
  </tr>
  <tr>
    <td>
      邮箱
    </td>
    <td>
      {$USER->mail|code}
    </td>
  </tr>
  <tr>
    <td>
      个性签名：{$USER->getinfo('signature')|code}
    </td>
  </tr>
  <tr>
    <td>
      联系方式
    </td>
    <td>{$USER->getinfo('contact')|code}
    </td>
  </tr>
  <tr>
    <td>
      注册时间
    </td>
    <td>
      {if $USER->regtime == 0}您是很久以前注册的，那时没有记录注册时间{else}{date('Y年m月d日 H:i:s',$USER->regtime)}{/if}
    </td>
  </tr>
  <tr>
    <td>
      查看
    </td>
    <td>
      <a href="msg.index.{$bid}">内信</a>/<a href="msg.index.@.{$bid}">@消息</a>/<a href="bbs.search.send.{$bid}?username={$USER->name|urlencode}">帖子</a>
    </td>
  </tr>
  <tr>
    <td>
      更改
    </td>
    <td>
      <a href="{$cid}.chname.{$bid}">用户名</a>/<a href="{$cid}.chpwd.{$bid}">密码</a>/<a href="{$cid}.chinfo.{$bid}">个性签名/联系方式</a>
    </td>
  </tr>
  <tr>
    <td>
      绑定
    </td>
    <td>
      {if $hasRegPhone}已绑定手机号{else}<a href="{$CID}.active.{$BID}?sid={$USER->sid}">手机号</a>{/if}
    </td>
  </tr>
  <tr>
    <td>
      主题
    </td>
    <td>
      <a href="link.tpl.classic.{$BID}?url64={code::b64e($page->geturl())}">经典主题</a>/
      Jhin主题
    </td>
  </tr>
  <tr>
    <td>
      论坛楼层排序
    </td>
    <td>
      {if $floorReverse}
      <a href="?floorReverse=0">正序</a>/倒序
      {else}
      正序/<a href="?floorReverse=1">倒序</a>
      {/if}
    </td>
  </tr>
  <tr>
    <td>
      功能
    </td>
    <td>
      <a href="addin.webplug.{$BID}">网页插件</a>/<a href="addin.jhtml.{$BID}">JHTML</a>

    </td>
  </tr>
  <tr>
    {if $mmbt}
    <td>
      管理员
    </td>
    <td>
      进入：<a href='admin.index.{$bid}' class="cr_login_submit">管理后台</a>
    </td>
    {/if}
  </tr>
</table>
{/block}
