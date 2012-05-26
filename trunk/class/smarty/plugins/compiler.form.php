<?php
/*xhtml/wml自适应表单*/
function smarty_compiler_form($p,$smarty)
{
if(!isset($p['action'])) $p['action']='$_SERVER["REQUEST_URI"]';
if(!isset($p['method'])) $p['method']='"get"';
if(isset($p['enctype'])&&eval("return $p[enctype];")!='application/x-www-form-application') $p['file']='true';
elseif(!isset($p['file'])) $p['file']='false';
return "<?php form::start($p[method],$p[action],$p[file]); ?>";
}
