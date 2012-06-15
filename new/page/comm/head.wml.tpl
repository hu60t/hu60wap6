{header content_type=$page.mime charset="utf-8"}
<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">
<wml>
<head>
<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
</head>
<card id="{if $id}{$id}{else}main{/if}" title="{$title|code}"{if $time !== null} ontimer="{if $url === null}{hu60::getmyurl()|code}{else}{$url|code}{/if}"><timer value="{$time}0"/{/if}>
