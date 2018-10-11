<?php
if (! function_exists('log_debug')) {

    function log_debug($message)
    {
        log_message('debug', $message);
    }
}

if (! function_exists('log_info')) {

    function log_info($message)
    {
        log_message('info', $message);
    }
}

if (! function_exists('log_error')) {

    function log_error($message)
    {
        log_message('error', $message);
    }
}
?>