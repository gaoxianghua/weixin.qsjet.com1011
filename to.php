<?php
define('TOKEN','weixin');

$wx = new WeiXinTest();
$wx->valid();
class WeiXinTest
{
    public function valid()
    {
        if ($this->checkSignature()) {
            echo $_GET['echostr'];
            exit;
        }
    }
 

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        $fp = fopen('wx.txt', 'a+');

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        fwrite($fp, $tmpStr . '---' . $signature);
        fclose($fp);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
}