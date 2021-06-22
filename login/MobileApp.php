<?php


namespace xooooooox\wechat\login;


use xooooooox\http\client\Curl;

/**
 * 移动应用
 * Class MobileApp
 * @package xooooooox\wechat\login
 */
class MobileApp
{

    /**
     * 获取AccessToken
     * @param string $AppId
     * @param string $AppSecret
     * @param string $code
     * @return array
     */
    public static function GetAccessToken(string $AppId, string $AppSecret, string $code) : array {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppId.'&secret='.$AppSecret.'&code='.$code.'&grant_type=authorization_code';
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

    /**
     * 获取RefreshToken
     * @param string $AppId
     * @param string $RefreshToken
     * @return array
     */
    public static function GetRefreshToken(string $AppId, string $RefreshToken) : array {
        $url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$AppId.'&grant_type=refresh_token&refresh_token='.$RefreshToken;
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

    /**
     * 获取个人信息
     * @param string $AccessToken
     * @param string $Openid
     * @return array
     */
    public static function GetUserInformation(string $AccessToken, string $Openid) : array {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$AccessToken.'&openid='.$Openid;
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

}