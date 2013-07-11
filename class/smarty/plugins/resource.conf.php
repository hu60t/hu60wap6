<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File: resource.conf.php
 * Type: resource
 * Name: conf
 * Purpose: 从PAGE_DIR读模板
 * -------------------------------------------------------------
 */
function smarty_resource_conf_realpath($name)
{
global $PAGE;
return $PAGE->tplPath($name,'.conf');
}
function smarty_resource_conf_source($name, &$source, $smarty)
{
 $source=file_get_contents(smarty_resource_conf_realpath($name));
 return $source ? true : false;
}
function smarty_resource_conf_timestamp($name, &$timestamp, $smarty)
{
 $timestamp=filemtime(smarty_resource_conf_realpath($name));
 return $timestamp ? true : false;
}
function smarty_resource_conf_secure($name, $smarty)
{
 return true;
}
function smarty_resource_conf_trusted($name, $smarty)
{
 return true;
}
