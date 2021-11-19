{include file="tpl:comm.head" title="编辑 Lutris 组件发布"}
<form action="{$CID}.{$PID}.{$BID}" method="post">
组件URL列表：
<br>
<textarea name="urls">{implode("\n", $urls)|code}</textarea>
<br>
<label><input type="checkbox" name="reverse" value="1">倒序</label>
<br>
<input type="submit" value="保存"> | <a href="{$CID}.{$PID}.{$BID}?r={time()}">刷新</a>
</form>
<style>
textarea {
    width: 99%;
    height: 400px;
}
</style>
{include file="tpl:comm.foot"}