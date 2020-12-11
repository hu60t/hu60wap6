{extends file='tpl:comm.default'}
{block name='title'}
查看@消息
{/block}
{block name='body'}
<div class="breadcrumb">

  @消息：
  {if !in_array($PAGE.ext[1],['yes','no'])}全部{else}<a href="msg.index.@.all.{$bid}">全部</a>{/if}&nbsp;
  {if $PAGE.ext[1] == 'no'}未读{else}<a href="msg.index.@.no.{$bid}">未读</a>{/if}&nbsp;
  {if $PAGE.ext[1] == 'yes'}已读{else}<a href="msg.index.@.yes.{$bid}">已读</a>{/if}
</div>
{if $list}
<script>
    function checkCleanAll() {
        var req = prompt("您正在清空@消息，所有发给你的@消息（包括已读和未读）都将被永久删除，此操作不可恢复！\n" +
                         "请在输入框内输入“我要清空@消息”（不包括引号）并点击确认按钮。");
        if (req != '我要清空@消息') {
            alert('操作已取消');
            return false;
        }
        return true;
    }
</script>
<div class="msg_action">
<form action="{$PAGE->getUrl()}" method="post">
    <input type="hidden" name="clean" value="at">
    <input type="hidden" name="actionToken" value="{$actionToken}">
    <input type="submit" name="action" value="全部设为已读">
    <input type="submit" name="action" value="清空@消息" onclick="return checkCleanAll()">
</form>
</div>
<hr />
{foreach $list as $i=>$k}
<div class="msg-box">
  <div class="floor-content" data-floorID="{$i}" id="floor_content_{$i}">
    {$ubbs->display($k.content,true)}
  </div>
  <div class="floor_fold_bar" id="floor_fold_bar_{$i}"></div>
  时间：{date("Y-m-d H:i:s",$k.ctime)}
</div>
{/foreach}
<div class="pager">
  {if $p < $maxP}<a href="?p={$p+1}">下一页</a>{/if}
  {if $p > 1}<a href="?p={$p-1}">上一页</a>{/if}
  {$p}/{$maxP}页,共{$msgCount}楼
  <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='?p='+this.value; }">
</div>
{else}
暂无@消息。
{/if}
<div class="breadcrumb">
  <a href="msg.index.inbox.all.{$bid}">收件箱</a> |
  <a href="msg.index.outbox.all.{$bid}">发件箱</a> |
  @消息 |
  <a href="user.wechat.{$bid}">微信推送</a>: {$wechat = $USER->getinfo('wechat')}{if $wechat.uid}开{else}关{/if}
</div>

<script>
	$(document).ready(function(){
    // 自动折叠过长内容
		var maxHeight = 360;
		$(".floor-content").each(function(){
			var that =$(this);
			var id=this.getAttribute("data-floorID");
			if(that.height() >  maxHeight){
				that.height(maxHeight);
				$('#floor_fold_bar_'+id).html("<button data-floorID='"+id+"'>展开隐藏内容</button>");
				$('#floor_fold_bar_'+id+">button").on('click',function(){
					var id=this.getAttribute("data-floorID");
					var that=$("#floor_content_"+id);
					// 不要使用that.height()进行判断，返回值是浮点数，不一定精确相等
					if(this.innerHTML == '折叠超出内容'){
						that.height(maxHeight);
						this.innerHTML='展开超出内容';
					}else{
						that.height(that[0].scrollHeight);
						this.innerHTML='折叠超出内容';
					}
				});
			}
		});
	});
</script>
{/block}
