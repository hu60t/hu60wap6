<?php
function getRunTime() {
	static $time = 0;
	
	$now = microtime(true);
	$runTime = $now - $time;
	$time = $now;
	
	return $runTime;
}