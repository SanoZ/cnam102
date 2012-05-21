<?php

class Zend_View_Helper_TimeAgo {
   
    function TimeAgo($original)
    {
        return Common_Misc_Time::niceTime(strtotime($original));
    }
}