{include file="tpl:comm.head" title="编码解码器" no_user=true}
<div id="coder">
    <form action="{$CID}.{$PID}.{$BID}" method="post">
        <p><textarea name="content" id="content">{$smarty.post.content|code:false:true}</textarea></p>
        <p>编码所采用的字符集：<select name="code">
            <option value="UTF-8" {if $smarty.post.code == 'UTF-8'}selected{/if}>UTF-8</option>

            <option value="GB2312" {if $smarty.post.code == 'GB2312'}selected{/if}>GB2312</option>
            <option value="GBK" {if $smarty.post.code == 'GBK'}selected{/if}>GBK</option>
            <option value="GB18030" {if $smarty.post.code == 'GB18030'}selected{/if}>GB18030</option>

            <option value="BIG5" {if $smarty.post.code == 'BIG5'}selected{/if}>BIG5</option>

            <option value="UTF-16LE" {if $smarty.post.code == 'UTF-16LE'}selected{/if}>UTF-16LE</option>
            <option value="UTF-16BE" {if $smarty.post.code == 'UTF-16BE'}selected{/if}>UTF-16BE</option>
            <option value="UTF-32LE" {if $smarty.post.code == 'UTF-32LE'}selected{/if}>UTF-32LE</option>
            <option value="UTF-32BE" {if $smarty.post.code == 'UTF-32BE'}selected{/if}>UTF-32BE</option>

            <option value="ISO-8859-1" {if $smarty.post.code == 'ISO-8859-1'}selected{/if}>ISO-8859-1</option>
        </select></p>
        <p>欲进行的操作：<select name="action">
            <option value="ehex" {if $smarty.post.action == 'ehex'}selected{/if}>十六进制编码</option>
            <option value="dhex" {if $smarty.post.action == 'dhex'}selected{/if}>十六进制解码</option>

            <option value="eb64" {if $smarty.post.action == 'eb64'}selected{/if}>base64编码</option>
            <option value="db64" {if $smarty.post.action == 'db64'}selected{/if}>base64解码</option>
            <option value="xdb64" {if $smarty.post.action == 'xdb64'}selected{/if}>base64编码(输入十六进制值)</option>
            <option value="db64x" {if $smarty.post.action == 'db64x'}selected{/if}>base64解码(显示十六进制结果)</option>
            <option value="eb64u" {if $smarty.post.action == 'eb64u'}selected{/if}>base64编码(用于URL)</option>
            <option value="db64u" {if $smarty.post.action == 'db64u'}selected{/if}>base64解码(用于URL)</option>
            <option value="xdb64u" {if $smarty.post.action == 'xdb64u'}selected{/if}>base64编码(用于URL，十六进制输入)</option>
            <option value="db64ux" {if $smarty.post.action == 'db64ux'}selected{/if}>base64解码(用于URL，十六进制结果)</option>

            <option value="eb32" {if $smarty.post.action == 'eb32'}selected{/if}>base32编码</option>
            <option value="db32" {if $smarty.post.action == 'db32'}selected{/if}>base32解码</option>
            <option value="xdb32" {if $smarty.post.action == 'xdb32'}selected{/if}>base32编码(输入十六进制值)</option>
            <option value="db32x" {if $smarty.post.action == 'db32x'}selected{/if}>base32解码(显示十六进制结果)</option>

            <option value="eb58" {if $smarty.post.action == 'eb58'}selected{/if}>base58编码</option>
            <option value="db58" {if $smarty.post.action == 'db58'}selected{/if}>base58解码</option>
            <option value="xdb58" {if $smarty.post.action == 'xdb58'}selected{/if}>base58编码(输入十六进制值)</option>
            <option value="db58x" {if $smarty.post.action == 'db58x'}selected{/if}>base58解码(显示十六进制结果)</option>

            <option value="eurl" {if $smarty.post.action == 'eurl'}selected{/if}>URL编码</option>
            <option value="durl" {if $smarty.post.action == 'durl'}selected{/if}>URL解码</option>
            <option value="eurls" {if $smarty.post.action == 'eurls'}selected{/if}>智能URL编码</option>
            <option value="jsure" {if $smarty.post.action == 'jsure'}selected{/if}>JS风格URL编码</option>
            <option value="jsurd" {if $smarty.post.action == 'jsurd'}selected{/if}>JS风格URL解码</option>

            <option value="htmlspecialchars" {if $smarty.post.action == 'htmlspecialchars'}selected{/if}>HTML实体编码（仅特殊字符）</option>
            <option value="htmlentities" {if $smarty.post.action == 'htmlentities'}selected{/if}>HTML实体编码（全部可用实体）</option>
            <option value="html_entity_decode" {if $smarty.post.action == 'html_entity_decode'}selected{/if}>HTML实体解码</option>

            <option value="emd5" {if $smarty.post.action == 'emd5'}selected{/if}>MD5加密</option>
            <option value="esha1" {if $smarty.post.action == 'esha1'}selected{/if}>SHA1加密</option>
            <option value="esha256" {if $smarty.post.action == 'esha256'}selected{/if}>SHA256加密</option>

            <option value="djson" {if $smarty.post.action == 'djson'}selected{/if}>JSON解码</option>
            <option value="ndjson" {if $smarty.post.action == 'ndjson'}selected{/if}>JSON解码（移除所有空白）</option>
            <option value="ejson" {if $smarty.post.action == 'ejson'}selected{/if}>JSON编码</option>

            <option value="json2serialize" {if $smarty.post.action == 'json2serialize'}selected{/if}>JSON转serialize</option>
            <option value="njson2serialize" {if $smarty.post.action == 'njson2serialize'}selected{/if}>JSON转serialize（移除所有空白）</option>

            <option value="date" {if $smarty.post.action == 'date'}selected{/if}>时间戳转日期</option>
            <option value="str2time" {if $smarty.post.action == 'str2time'}selected{/if}>日期转时间戳</option>

            <option value="str2lower" {if $smarty.post.action == 'str2lower'}selected{/if}>字母转为小写</option>
            <option value="str2upper" {if $smarty.post.action == 'str2upper'}selected{/if}>字母转为大写</option>
            <option value="str2ucwords" {if $smarty.post.action == 'str2ucwords'}selected{/if}>首字母大写</option>

            <option value="nbsp2space" {if $smarty.post.action == 'nbsp2space'}selected{/if}>UTF-8特殊空格转普通空格</option>

            <option value="markdown2html" {if $smarty.post.action == 'markdown2html'}selected{/if}>Markdown转HTML</option>
            <option value="markdown2html_nolink" {if $smarty.post.action == 'markdown2html_nolink'}selected{/if}>Markdown转HTML（不解析链接）</option>
            <option value="markdown2html_nohtml" {if $smarty.post.action == 'markdown2html_nohtml'}selected{/if}>Markdown转HTML（转义HTML）</option>
        </select></p>
        <p><input type="submit" value="操作"/></p>
    </form>
</div>
<div id="result">
    <p><textarea>{$result|code:false:true}</textarea></p>
    <p>当前时间戳：{$smarty.server.REQUEST_TIME|code}</p>
</div>
{include file="tpl:comm.foot"}
