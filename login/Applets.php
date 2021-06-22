<?php


namespace xooooooox\wechat\login;


use xooooooox\http\client\Curl;

/**
 * 小程序
 * Class Applets
 * @package xooooooox\wechat\login
 */
class Applets
{

    /**
     * Code 2 Session
     * @param string $AppId
     * @param string $AppSecret
     * @param string $JsCode
     * @return array
     */
    public static function Code2Session(string $AppId, string $AppSecret, string $JsCode) : array {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$AppId.'&secret='.$AppSecret.'&js_code='.$JsCode.'&grant_type=authorization_code';
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

    /**
     * Get Paid UnionId
     * @param string $AccessToken
     * @param string $Openid
     * @return array
     */
    public static function GetPaidUnionId(string $AccessToken, string $Openid) : array {
        $url = 'https://api.weixin.qq.com/wxa/getpaidunionid?access_token='.$AccessToken.'&openid='.$Openid;
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

    /**
     * Get Access Token
     * @param string $AppId
     * @param string $AppSecret
     * @return array
     */
    public static function GetAccessToken(string $AppId, string $AppSecret) : array {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$AppId.'&secret='.$AppSecret;
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

}