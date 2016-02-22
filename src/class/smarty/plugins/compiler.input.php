<?php
/*xhtml/wml兼容表单 input元素*/
function smarty_compiler_input($p,$smarty)
{
if(!isset($p['type'])) $type='text';
else $type=eval("return $p[type];");
switch($type)
 {
case 'hidden':
if(!isset($p['name'])) return '';
if(!isset($p['value'])) $p['value']='""';
 return "<?php form::hidden($p[name],$p[value]); ?>";
break;
case 'submit':
if(!isset($p['value'])) $p['value']='"提交"';
if(!isset($p['name'])) $p['name']='null';
return "<?php form::submit($p[value],$p[name]); ?>";
break;
case 'file':
if(!isset($p['name'])) $p['name']='null';
if(!isset($p['size'])) $p['size']='null';
if(!isset($p['wml'])) $p['wml']='""';
return "<?php form::file($p[name],$p[size],$p[wml]); ?>";
break;
case 'text':
case 'password':
case 'textarea':
if(isset($p['cols']) || isset($p['rows'])) $p['size']='array('.(isset($p['cols'])?$p['cols']:'null').','.($p['rows']?$p['rows']:'null').')';
if(!isset($p['size'])) $p['size']='null';
if(!isset($p['name'])) $p['name']='""';
if(!isset($p['value'])) $p['value']='""';
if($type=='textarea') $istextarea='true';
else $istextarea='false';
if($type=='password') $ispassword='true';
else $ispassword='false';
return "<?php form::input($p[name],$p[size],$p[value],$istextarea,$ispassword); ?>";
break;
case 'checkbox':
if(!isset($p['name'])) $p['name']='null';
if(!isset($p['value'])) $p['value']='null';
if(!isset($p['checked'])) $p['checked']='null';
return "<?php form::checkbox($p[name],$p[value],$p[checked]); ?>";
break;
case 'someinput':
if(!isset($p['name'])) $p['name']='null';
if(!isset($p['value'])) $p['value']='null';
if(isset($p['cols']) || isset($p['rows'])) $p['size']='array('.($p['cols']?$p['cols']:'null').','.($p['rows']?$p['rows']:'null').')';
if(!isset($p['size'])) $p['size']='null';
return "<?php form::someinput_put($p[name],$p[value],$p[size]); ?>";
break;
default:
return "";
break;
 }
}
