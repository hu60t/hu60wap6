<?php
/* </div> */
function smarty_compiler_enddiv($p,$smarty)
{
global $PAGE;
return $PAGE['bid']=='wml' ? (isset($p['wml']) ? "<?php echo $p[wml];?>" : "</p>") : "</div>";
}
