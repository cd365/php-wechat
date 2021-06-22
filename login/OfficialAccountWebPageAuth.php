<?php


namespace xooooooox\wechat\login;


use xooooooox\http\client\Curl;

/**
 * 公众号授权登录
 * Class OfficialAccountWebPageAuth
 * @package xooooooox\wechat\login
 */
class OfficialAccountWebPageAuth
{

    /**
     * 1.用户同意授权,获取code
     * @param string $AppId 公众号的唯一标识
     * @param string $RedirectUri 授权后重定向的回调链接地址
     * @param string $Scope snsapi_base, snsapi_userinfo   应用授权作用域, snsapi_base (不弹出授权页面,直接跳转,只能获取用户openid), snsapi_userinfo (弹出授权页面,可通过openid拿到昵称,性别,所在地.并且, 即使在未关注的情况下,只要用户授权,也能获取其信息)
     * @param string $State 重定向后会带上state参数,开发者可以填写a-zA-Z0-9的参数值,最多128字节
     * @return string 如果用户同意授权,页面将跳转至 redirect_uri/?code=CODE&state=STATE
     */
    public static function GetUserAuthScopeUri(string $AppId, string $RedirectUri, string $Scope = 'snsapi_base', string $State = 'STATE') : string {
        $RedirectUri = urlencode($RedirectUri); // 使用 urlencode 对链接进行处理
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$AppId.'&redirect_uri='.$RedirectUri.'&response_type=code&scope='.$Scope.'&state='.$State.'#wechat_redirect';
    }

    /**
     * 2.通过code换取网页授权access_token
     * @param string $AppId 公众号的唯一标识
     * @param string $AppSecret 公众号密钥
     * @param string $Code 授权code
     * @return array {"access_token":"ACCESS_TOKEN","expires_in":7200,"refresh_token":"REFRESH_TOKEN","openid":"OPENID","scope":"SCOPE"}
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
     * 3. 获取刷新access_token(如果需要) 由于access_token拥有较短的有效期, 当access_token超时后, 可以使用refresh_token进行刷新, refresh_token有效期为30天, 当refresh_token失效之后, 需要用户重新授权
     * @param string $AppId 公众号的唯一标识
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
     * 4.拉取用户信息(需scope为snsapi_userinfo)
     * @param string $AccessToken access_token
     * @param string $Openid openid
     * @return array {"openid":"OPENID","nickname":"NICKNAME","sex":1,"province":"PROVINCE","city":"CITY","country":"COUNTRY","headimgurl":"https://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/46","privilege":["PRIVILEGE1","PRIVILEGE2"],"unionid":"o6_bmasdasdsad6_2sgVt7hMZOPfL"}
     */
    public static function GetUserInfomation(string $AccessToken, string $Openid) : array {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$AccessToken.'&openid='.$Openid.'&lang=zh_CN';
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

    /**
     * 检验授权凭证(access_token)是否有效
     * @param string $AccessToken access_token
     * @param string $Openid openid
     * @return array {"errcode":0,"errmsg":"ok"} | {"errcode":40003,"errmsg":"invalid openid"}
     */
    public static function VerifyAccessTokenIsValid(string $AccessToken, string $Openid) : array {
        $url = 'https://api.weixin.qq.com/sns/auth?access_token='.$AccessToken.'&openid='.$Openid;
        $result = Curl::GetBody(Curl::Get($url));
        $bag = json_decode($result,true);
        if (!is_array($bag)){
            return [];
        }
        return $bag;
    }

}