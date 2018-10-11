<?php

class MY_Config extends CI_Config
{

    public function __construct()
    {
        parent::__construct();
    }

    function root_path($uri = '')
    {
        return $this->slash_item('root_path') . ltrim($this->_uri_string($uri), '/');
    }

    function getIPaddress()
    {
        $IPaddress = '';
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $IPaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else 
                if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                    $IPaddress = $_SERVER["HTTP_CLIENT_IP"];
                } else {
                    
                    $IPaddress = $_SERVER["REMOTE_ADDR"];
                }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                
                $IPaddress = getenv("HTTP_X_FORWARDED_FOR");
            } else 
                if (getenv("HTTP_CLIENT_IP")) {
                    
                    $IPaddress = getenv("HTTP_CLIENT_IP");
                } else {
                    
                    $IPaddress = getenv("REMOTE_ADDR");
                }
        }
        return $IPaddress;
    }
}
?>