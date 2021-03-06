<?php
require_once('Zend/Date.php');

class Common_Misc_Time
{
    static public function niceTime($time)
    {
        $delta = time() - $time;
        if ($delta < 60) {
            return 'less than a minute ago';
        } else if ($delta < 120) {
            return 'about a minute ago';
        } else if ($delta < (45 * 60)) {
            return floor($delta / 60) . ' minutes ago';
        } else if ($delta < (90 * 60)) {
            return 'about an hour ago';
        } else if ($delta < (24 * 60 * 60)) {
            return 'about ' . floor($delta / 3600) . ' hours ago';
        } else if ($delta < (48 * 60 * 60)) {
            return '1 day ago';
        } else {
            return floor($delta / 86400) . ' days ago';
        }
    }
    
    static public function niceTimeGMT($time)
    {
        $_date = new Zend_Date($time . ' UTC', Zend_Date::ISO_8601);
        $delta = gmmktime() - $_date->getTimestamp();
        if ($delta < 60) {
            return 'less than minute ago';
        } else if ($delta < 120) {
            return 'about a minute ago';
        } else if ($delta < (45 * 60)) {
            return floor($delta / 60) . ' minutes ago';
        } else if ($delta < (90 * 60)) {
            return 'about an hour ago';
        } else if ($delta < (24 * 60 * 60)) {
            return 'about ' . floor($delta / 3600) . ' hours ago';
        } else if ($delta < (48 * 60 * 60)) {
            return '1 day ago';
        } else {
            return floor($delta / 86400) . ' days ago';
        }
    }
}