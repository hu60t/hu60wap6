<?php
function page_gzip($f)
{
global $PAGE;
if($PAGE['gzip'])
{
$f=gzencode($f,$PAGE['gzip']);
header('Content-Encoding: gzip');
header('Vary: Accept-Encoding');
}
header('Content-Length: '.strlen($f));
return $f;
}
