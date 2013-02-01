<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File: resource.tpl.php
 * Type: resource
 * Name: tpl
 * Purpose: 从PAGE_DIR读模板
 * -------------------------------------------------------------
 */
function smarty_resource_tpl_realpath($name)
{
global $PAGE;
return $PAGE->tplPath($name,'.tpl');
}
function smarty_resource_tpl_source($name, &$source, $smarty)
{
 $source=file_get_contents(smarty_resource_tpl_realpath($name));
 return $source ? true : false;
}
function smarty_resource_tpl_timestamp($name, &$timestamp, $smarty)
{
 $timestamp=filemtime(smarty_resource_tpl_realpath($name));
 return $timestamp ? true : false;
}
function smarty_resource_tpl_secure($name, $smarty)
{
 return true;
}
function smarty_resource_tpl_trusted($name, $smarty)
{
 return true;
}
