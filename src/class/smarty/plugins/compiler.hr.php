<?php
/* <hr/> */
function smarty_compiler_hr($p,$smarty)
{
global $PAGE;
if($PAGE['bid']=='wml') return isset($p['wml']) ? "<?php echo $p[wml];?>" : "<br/>--------<br/>";
if(isset($p['wml'])) unset($p['wml']);
$html="<hr";
foreach($p as $n=>$v)
 {
$html.=" $n=\"<?php echo $v;?>\"";
 }
$html.="/>";
return $html;
}
