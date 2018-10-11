<?php
/**
 * Config for wechat;
 * You will have this values when you apply to be developer by "微信公众平台"
 * https://mp.weixin.qq.com/;
 */
$config['wxpay'] = array(
    'appid' => 'wx49ee2123f0ea5254', // Find this from your "微信公众平台"
    'appsecret' => '15d80508b8f25e75835f8fcf73bbd298',
    'mchid' => '10027977', // 受理商ID，身份标识
    'key' => '23tu8dd3cgj52qa9741eb05b6hzwvj8q', // 商户支付密钥Key。
    'url_js_api_call' => '', // 【JSAPI路径设置】获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
    'path_sslcert' => APPPATH . 'libraries/wcpay/cacert/apiclient_cert.pem', // 【证书路径设置】证书路径,注意应该填写绝对路径
    'path_ssl_key' => APPPATH . 'libraries/wcpay/cacert/apiclient_key.pem',
    'url_notify' => base_url() . "wechat/wcpay/notify_pay_result"
); // 异步通知url
