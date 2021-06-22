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
     * Code 2 Session 登录
     * @param string $AppId 移动应用id
     * @param string $AppSecret 移动应用密钥
     * @param string $JsCode 授权code
     * @return array {"openid":"用户唯一标识","session_key":"会话密钥","unionid":"用户在开放平台的唯一标识符,若当前小程序已绑定到微信开放平台帐号下会返回","errcode":40029,"errmsg":"错误信息"}
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
     * Get Paid UnionId 用户信息
     * @param string $AccessToken access_token
     * @param string $Openid openid
     * @return array {"unionid":"用户唯一标识,调用成功后返回","errcode":40003,"errmsg":"错误信息"}
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
     * Get Access Token 接口调用凭证
     * @param string $AppId 移动应用id
     * @param string $AppSecret 移动应用密钥
     * @return array {"access_token":"获取到的凭证","expires_in":7200,"errcode":40001,"errmsg":"错误信息"}
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