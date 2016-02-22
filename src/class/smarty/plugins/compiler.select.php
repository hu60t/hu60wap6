<?php
/*xhtml/wml兼容表单 select控件*/
function smarty_compiler_select($p,$smarty)
{
if(!isset($p['name'])) $p['name']='null';
if(!isset($p['option'])) $p['option']='null';
if(!isset($p['value'])) $p['value']='null';
if(!isset($p['output'])) $p['output']='null';
if(!isset($p['selected'])) $p['selected']='null';
if(!isset($p['multiple'])) $p['multiple']='false';
return "<?php form::select($p[name],$p[option],$p[value],$p[output],$p[selected],$p[multiple]); ?>";
}
