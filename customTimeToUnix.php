<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function customMonthToCanon($str) {
    if(preg_match('/\D/', $str)) {
        $str = preg_replace('/^(.{3}).*/', '$1', $str);
        $date = strtotime($str);
        return date('m', $date);
    }
    else {
        return $str;
    }
}

function toCustomWithDate($str) { // d m h i y -> h i d m y
    $params = preg_split('/[ .:,-]/', $str);
    $cp = count($params);
    if($cp < 4) {
        array_unshift($params, '0');
    }
    if($cp < 3) {
        array_unshift($params, '0');
    }
    if($cp < 2) {
        array_unshift($params, date('m'));
    }
    return implode(' ', $params);
}

function customTimeToUnix($str) {
    $params = preg_split('/[ .:,-]/', $str);
    $cp = count($params);

    if($cp > 3) {
        $params[3] = customMonthToCanon($params[3]);
    }

    foreach($params as &$param) {
        $param = sprintf("%02d", $param);
    }
    if($cp > 4) {
        $params[4] = preg_replace('/^(\d{2}).*/', '20$1', $params[4]);
    }

    $dateStr = '';
    $dateStr .= ($cp < 5 ? date('Y') : $params[4]) . '-';
    $dateStr .= ($cp < 4 ? date('m') : $params[3]) . '-';
    $dateStr .= ($cp < 3 ? date('d') : $params[2]) . ' ';    
    $dateStr .= $params[0];
    $dateStr .= ':' . ($cp < 2 ? date('i') : $params[1]);
    $dateStr .= ':00';
    
    if(defined('TEST')) {
        echo $dateStr . ' | ';
    }
    $date = strtotime($dateStr);
    return $date;
}

define('TEST', 1);
header('Content-type: text/plain');
echo customTimeToUnix('16') . "\n";
echo customTimeToUnix('16 04') . "\n";
echo customTimeToUnix('16 04 8') . "\n";
echo customTimeToUnix('16:04 08.11') . "\n";
echo customTimeToUnix('16:04 08.11.17') . "\n";
echo customTimeToUnix('16:04 08 11 16') . "\n";
echo customTimeToUnix('16:04 8 nov') . "\n";
echo customTimeToUnix('16:04 8 November 17') . "\n";

echo toCustomWithDate('8 nov') . "\n";
echo customTimeToUnix(toCustomWithDate('8 nov')) . "\n";

echo customMonthToCanon('febnahooy') . "\n";
echo customMonthToCanon('02') . "\n";