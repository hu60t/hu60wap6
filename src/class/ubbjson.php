<?php

class ubbJson extends XUBBP
{
    public function display($ubbArray, $serialize = false, $maxLen = null, $page = null)
    {
        if ($serialize) {
            $ubbArray = unserialize($ubbArray);
        }

        return $ubbArray;
    }
}
















