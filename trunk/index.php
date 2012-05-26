<?php
require_once dirname(__FILE__).'/config.inc.php';
require_once SUB_DIR.'/reg_page_bid.php';

hu60::get_page_mime();
$path=dirname($_SERVER['PHP_SELF']);
if(strlen($path)<2) $path='';
header('Location: '.$path.'/m.php/index.index.'.$PAGE['bid']);
