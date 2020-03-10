<?php
/*使模板能够发送Content-type头信息*/
function smarty_compiler_header($p,$smarty)
{
if(isset($p['content_type']))
 return "<?php header('Content-type: '.$p[content_type]".(isset($p['charset']) ? ".'; charset='.$p[charset]" : "")."); ?>";
else
 return '';
}
