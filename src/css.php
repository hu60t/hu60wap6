<form action="" method="post">
<textarea name="data" cols="120" rows="25"></textarea><br/>
<input type="submit" value="CSS 转移"/>
</form>
<?
$content=$_POST['data'];
$content=str_replace("{"," { ",$content);
$content=str_replace("}"," }<br/>",$content);
echo $content;