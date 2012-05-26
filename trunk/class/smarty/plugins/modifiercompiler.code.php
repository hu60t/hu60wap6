<?php
/*对文字进行html/wml编码*/
function smarty_modifiercompiler_code($p,$compiler)
{
/*参数：code::html($str,$br=false,$NOnbsp=false*/
return 'code::html('.implode(',',$p).')';

}
