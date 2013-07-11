<?php
/* </span> */
function smarty_compiler_endspan($p,$smarty)
{
global $PAGE;
return $PAGE['bid']=='wml' ? (isset($p['wml']) ? "<?php echo $p[wml];?>" : "</b>") : "</span>";
}
