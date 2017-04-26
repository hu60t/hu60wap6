{$host=$smarty.server.HTTP_HOST}
{if $host == "hu60.cn"}腾讯云{else}<a href="https://hu60.cn{$smarty.server.REQUEST_URI|code}">腾讯云</a>{/if} |
{if $host == "ssl.hu60.cn"}360{else}<a href="https://ssl.hu60.cn{$smarty.server.REQUEST_URI|code}">360</a>{/if} |
{if $host == "yd.cdn.hu60.cn"}云盾{else}<a href="http://yd.cdn.hu60.cn{$smarty.server.REQUEST_URI|code}">云盾</a>{/if} |
{if $host == "baidu.cdn.hu60.cn"}百度{else}<a href="http://baidu.cdn.hu60.cn{$smarty.server.REQUEST_URI|code}">百度</a>{/if} |
{if $host == "ipv6.hu60.cn"}IPv6{else}<a href="https://ipv6.hu60.cn{$smarty.server.REQUEST_URI|code}">IPv6</a>{/if}
