<?php


namespace xooooooox\wechat\sdk;


use xooooooox\http\client\Curl;


/**
 * 微信网页授权(公众号)
 * Class JavaScriptWebPage
 * @package xooooooox\wechat\sdk
 */
class JavaScriptWebPage
{

    /**
     * 微信公众号 JsApiTicket
     * @param string $AccessToken
     * @return array
     */
    public static function JsApiTicket(string $AccessToken) : array {
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$AccessToken.'&type=jsapi';
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

}