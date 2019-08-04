<?php

class ubbJson extends XUBBP
{
	public function display($ubbArray, $serialize = false, $maxLen = null, $page = null)
	{
		$disable = $this->getOpt('all.blockPost');

		if ($disable) {
			return [
				[
					"type" => "style",
				    "tag" => "div",
    				"opt" => "border:red solid 1px",
	    			"len" => 23
				],
				[
					"type" => "text",
		    		"value" => "用户被禁言，发言自动屏蔽。",
			    	"len" => 13
				],
				[
					"type" => "style",
				    "tag" => "/div",
    				"len" => 3
				]
			];
		}

		if ($serialize) {
			$ubbArray = unserialize($ubbArray);
		}

		return $ubbArray;
	}
}
















