<?php
/* <span> */
function smarty_compiler_span($p,$smarty)
{
global $PAGE;
if($PAGE['bid']=='wml') return isset($p['wml']) ? "<?php echo $p[wml];?>" : "<b>";
if(isset($p['wml'])) unset($p['wml']);
$html="<span";
foreach($p as $n=>$v)
 {
$html.=" $n=\"<?php echo $v;?>\"";
 }
$html.=">";
return $html;
}
