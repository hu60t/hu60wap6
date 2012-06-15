{header content_type=$page.mime charset="utf-8"}
<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{hu60::getmyurl()|code}{else}{$url|code}{/if}"/>{/if}
{if $css === null}{$css="tpl:comm.css.xhtml"}{/if}{include file=$css}
<title>{$title|code}</title>
</head>
{if !$base}<body><a id="top" href="#bottom" accesskey="6"></a>
{/if}