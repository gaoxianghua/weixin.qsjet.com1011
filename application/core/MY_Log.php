<?php

/* this script will add loaction trace to log message */
class MY_Log extends CI_Log
{

    public function __construct()
    {
        parent::__construct();
    }

    public function write_log($level = 'error', $msg, $php_error = FALSE)
    {
        if ($this->_enabled === FALSE) {
            return FALSE;
        }
        
        $level = strtoupper($level);
        
        if (! isset($this->_levels[$level]) or ($this->_levels[$level] > $this->_threshold)) {
            return FALSE;
        }
        
        $filepath = $this->_log_path . 'log-' . date('Y-m-d') . '.php';
        $message = '';
        
        if (! file_exists($filepath)) {
            $message .= "<" . "?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?" . ">\n\n";
        }
        
        if (! $fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
            return FALSE;
        }
        
        $message .= $level . ' ' . (($level == 'INFO') ? ' -' : '-') . ' ' . date($this->_date_fmt) . ' --> ' . $msg . "\t";
        
        $trace = $this->trace_location();
        $message .= 'classname: ' . $trace['file'] . " " . $trace['line'] . " " . $trace['function'] . "\n";
        
        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);
        
        @chmod($filepath, FILE_WRITE_MODE);
        return TRUE;
    }

    public function trace_location()
    {
        $locationInfo = array();
        $trace = debug_backtrace();
        $prevHop = null;
        // make a downsearch to identify the caller
        $hop = array_pop($trace);
        while ($hop !== null) {
            if (isset($hop['class'])) {
                // we are sometimes in functions = no class available: avoid php warning here
                $className = strtolower($hop['class']);
                if (! empty($className) and ($className !== 'my_log') && strtolower(get_parent_class($className)) !== 'ci_log') {
                    $prevHop = $hop;
                    $hop = array_pop($trace);
                    $locationInfo['line'] = $hop['line'];
                    $locationInfo['file'] = $hop['file'];
                    break;
                }
            }
            $prevHop = $hop;
            $hop = array_pop($trace);
        }
        $locationInfo['class'] = isset($prevHop['class']) ? $prevHop['class'] : 'main';
        if (isset($prevHop['function']) and $prevHop['function'] !== 'include' and $prevHop['function'] !== 'include_once' and $prevHop['function'] !== 'require' and $prevHop['function'] !== 'require_once') {
            
            $locationInfo['function'] = $prevHop['function'];
        } else {
            $locationInfo['function'] = 'main';
        }
        
        return $locationInfo;
    }
}

?>