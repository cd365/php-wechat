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
     * @param string $AppId 移动应用id
     * @param string $AppSecret 移动应用密钥
     * @param string $Code 授权code
     * @return array {"access_token":"ACCESS_TOKEN","expires_in":7200,"refresh_token":"REFRESH_TOKEN","openid":"OPENID","scope":"SCOPE","unionid":"o6_bmasdasdsad6_2sgVt7hMZOPfL"}
     */
    public static function GetAccessToken(string $AppId, string $AppSecret, string $Code) : array {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppId.'&secret='.$AppSecret.'&code='.$Code.'&grant_type=authorization_code';
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

    /**
     * 获取RefreshToken
     * @param string $AppId 移动应用id
     * @param string $RefreshToken 刷新access_token
     * @return array {"access_token":"ACCESS_TOKEN","expires_in":7200,"refresh_token":"REFRESH_TOKEN","openid":"OPENID","scope":"SCOPE"}
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
     * @param string $AccessToken access_token
     * @param string $Openid openid
     * @return array {"openid":"OPENID","nickname":"NICKNAME","sex":1,"province":"PROVINCE","city":"CITY","country":"COUNTRY","headimgurl":"https://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0","privilege":["PRIVILEGE1","PRIVILEGE2"],"unionid":"o6_bmasdasdsad6_2sgVt7hMZOPfL"}
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