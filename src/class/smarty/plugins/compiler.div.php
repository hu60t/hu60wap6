<?php
/* <div> */
function smarty_compiler_div($p,$smarty)
{
global $PAGE;
if($PAGE['bid']=='wml') return isset($p['wml']) ? "<?php echo $p[wml];?>" : "<p>";
if(isset($p['wml'])) unset($p['wml']);
$html="<div";
foreach($p as $n=>$v)
 {
$html.=" $n=\"<?php echo $v;?>\"";
 }
$html.=">";

return $html;
}
