<?php


namespace xooooooox\wechat\app;


use xooooooox\http\client\Curl;


/**
 * 微信公众号
 * Class OfficialAccount
 * @package xooooooox\wechat\app
 */
class OfficialAccount
{

    /**
     * 微信公众号 AccessToken
     * @param string $AppId
     * @param string $AppSecret
     * @return array {"access_token":"ACCESS_TOKEN","expires_in":7200}
     */
    public static function AccessToken(string $AppId, string $AppSecret) : array {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$AppId.'&secret='.$AppSecret;
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

}