<?php
/*xhtml/wml兼容表单 form::end()*/
function smarty_compiler_formclose($p,$smarty)
{
return "<?php form::end(); ?>";
}
