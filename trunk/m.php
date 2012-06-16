<?php
try {
require_once dirname(__FILE__).'/config.inc.php';
$PAGE=new page;
$PAGE->cutPath();
page::regBid($PAGE->bid);
require_once SUB_DIR.'/reg_page_bid.php';
include $PAGE->load();
} catch(exception $ERR) {
 include $PAGE->load('error','pageerr');
}
