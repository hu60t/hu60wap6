{include file="tpl:comm.head" title="选择你喜欢的聊天室-进入聊天室"}
<div class="pt">
<div class="cr180_ptzmenu">
                <a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="回首页" class="pt_z">回首页</a>
            <span class="pt_c">聊天室名称（输入自定义名称可自建聊天室）</span>
<span class="pt_y"><a href="addin.chat.{$bid}">刷新</a></span>
</div>
</div>
<div class="topic_area">
    <div class="cr180_form">
    <form method="get" action="addin.chat.{$bid}"><div >
<p>
<input type="text" name="roomname" id="username_LCxiI" class="txt" placeholder="聊天室名(例如:公共聊天室)" value=""/>
</p>
</p>
    <p><input type="submit" name="go" id="submit" class="cr_login_submit" value="快速进入" /></p>
            </div>
	</form>    </div>

<br />
        <div class="fl cl indexthreadlist">
            <div class="bm">
                <div class="bm_title_2" id="threadalllist"><span class=" a">[New]聊天室列表</span></div>
                <div id="threadalllist_c">
                <div>
                                <ul>
{foreach $list.row as $k}
<li><a href="addin.chat.{$k.name}.{$bid}">{$k.name}  ({$k.ctime})</a></li>
{/foreach}
                                    </ul>
                                </div>
                </div>
            </div>
        </div>
<div class="pt">
<div class="cr180_ptzmenu">
                <a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="回首页" class="pt_z">回首页</a>
            <span class="pt_c">{$list.px}</span>
<span class="pt_y"><a href="addin.chat.{$bid}">刷新</a></span>
</div>
</div>
{include file="tpl:comm.foot"}