{include file="tpl:comm.head" title="『聊天室』{$roomname}"}
<div class="pt">
<div class="cr180_ptzmenu">
                <a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="回首页" class="pt_z">回首页</a>
            <span class="pt_c"><a  href="javascript:;" onclick="location.href='addin.chat.{$bid}'" >切换聊天室</a>{$roomname}</span>
<span class="pt_y"><a href="addin.chat.test.{$bid}">刷新</a></span>
</div>
</div>
{$err_msg}
<div class="topic_area">
    <div class="cr180_form">
    <form method="post" action="addin.chat.{$roomname}.{$bid}"><div >
<p>
        <textarea class="txt" name="neirong" style="width:100%;height:100px;"></textarea>
                <input name="token" type="hidden" value="25b10c32f67f18bb48716ab09f8f0084"/></p>
<p>
</p>
    <p><input type="submit" name="go" id="submit" class="cr_login_submit" value="快速发言" /></p>
            </div>
	</form>    </div>

{foreach $chatlist.row as $k}
{$k.lid}。 {$ubbs->display($k.content,true)}<br />
(<a href="">{$k.uname}</a> {date("m-d h:i:s",{$k.time})})<hr />
{/foreach}

<div class="pt">
<div class="cr180_ptzmenu">
<a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="回首页" class="pt_z">回首页</a>
            <span class="pt_c">{$chatlist.px}</span>
<span class="pt_y"><a href="addin.chat.test.{$bid}">刷新</a></span>
</div>
</div>
{include file="tpl:comm.foot"}