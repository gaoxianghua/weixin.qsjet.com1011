<?php
/**
 * Config for send email;
 */
 /*
  *     属性	类型	约束	说明
        sid	    String	必选	主账户id
        appId	String	必选	应用Id
        sign	String	必选	验证信息，使用MD5加密（账户id+时间戳+账户授权令牌），共32位（小写）
        time	String	必选	时间戳yyyyMMddHHmmssSSS，有效时间为30分钟
        templateId	String	必选	模板Id
        to	String	必选	短信接收端手机号码（国内短信不要加前缀，国际短信号码前须带相应的国家区号，如日本：0081）
        param	String	必选	内容数据，用于替换模板中{数字}，若有多个替换内容，用英文逗号隔开即可 
  * 
  */

$config['email'] = array(
        'uri'=> "http://www.ucpaas.com/maap/sms/code",
        'token'=>'798fbb499cf365253a2d84663a74f04c',
        'sid'=> "bee116cd3223f452b5c79a80536b6cfb",
        'appId'=> "8484424a31674eb891dc44e9fbca9429",
);
/*$config['email'] = array(
        'uri'=> "https://open.ucpaas.com/ol/sms",
        'token'=>'798fbb499cf365253a2d84663a74f04c',
        'sid'=> "bee116cd3223f452b5c79a80536b6cfb",
        'appId'=> "8484424a31674eb891dc44e9fbca9429",
);*/
/*$config['email'] = array(
    'uri'=> "https://open.ucpaas.com/ol/sms",
    'token'=>'798fbb499cf365253a2d84663a74f04c',
    'sid'=> " bee116cd3223f452b5c79a80536b6cfb",
    'appId'=> "1a8b0c5629cc4bfaa404f349d90c0abf",
);*/

