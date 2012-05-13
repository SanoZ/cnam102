<?php
date_default_timezone_set('Australia/Sydney');
class Zend_View_Helper_TimeAgo {
   
    function TimeAgo($original)
    {
        return Ziller_Misc_Time::niceTime(strtotime($original));
    }
}