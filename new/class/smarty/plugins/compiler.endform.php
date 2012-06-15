<?php
/*xhtml/wml兼容表单 form::end()*/
function smarty_compiler_endform($p,$smarty)
{
return "<?php form::end(); ?>";
}
