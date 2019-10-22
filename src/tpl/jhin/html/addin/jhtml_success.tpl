{$url="index.index.jhtml"}
{config_load file="conf:site.info"}
{include file="tpl:comm.head" title="JHTML" no_webplug=true}
<div class="tp">
        <a href="index.index.{$BID}">首页</a> &gt; JHTML
</div>

<hr>

<div>
        <p>保存成功，您可进入JHTML首页进行体验。在进入体验之前，请仔细阅读以下提示，以防遇到意外：</p>
        <ul>
            <li>需要提醒您的是，进入JHTML首页后，您看到的所有内容都由您刚刚输入的代码生成。</li>
            <li>{#SITE_SIMPLE_NAME#}无法保证生成的页面功能正常，也无法在该页面中向您提供返回常规版本的链接。</li>
            <li>因为您刚刚输入的代码接管了整个页面，一切内容都需要由这些代码来提供。</li>
            <li>如果这些代码功能不正常，您就会看到错乱甚至空白的页面，并且可能无法直接从页面返回{#SITE_SIMPLE_NAME#}的常规版本。</li>
            <li>无论您接下来看到什么，都与{#SITE_SIMPLE_NAME#}无关，也不是{#SITE_SIMPLE_NAME#}的错误或者问题，请直接联系代码作者反馈问题。</li>
            <li>在体验JHTML版的过程中遇到任何问题，都可以在浏览器地址栏输入域名 {$smarty.server.HTTP_HOST} 回到常规版本。</li>
            <li>您也可以通过以前保存的{#SITE_SIMPLE_NAME#}书签，或者在搜索引擎搜索{#SITE_SIMPLE_NAME#}回到常规版本。</li>
            <li>此外，常规版本页面不正常与JHTML代码无关，通常是<a href="addin.webplug.html">网页插件</a>代码引起的。</li>
        </ul>
        <p>我知道了，<a href="{$url|code}">进入JHTML首页</a></p>
        <p>我放弃了，<a href="index.index.html">回到常规版本</a></p>
</div>
{include file="tpl:comm.foot"}
