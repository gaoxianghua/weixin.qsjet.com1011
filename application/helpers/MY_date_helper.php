<?php
if (! function_exists('getMyDate')) {

    function getMyDate($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }
}

if (! function_exists('getMicrosecond')) {

    function getMicrosecond()
    {
        list ($t1, $t2) = explode(' ', microtime());
        return (float) sprintf('%06.0f', (floatval($t1)) * 1000000);
    }
}

?>