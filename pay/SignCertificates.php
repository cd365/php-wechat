<?php

namespace xooooooox\wechat\pay;

use xooooooox\http\client\Curl;
use xooooooox\nonce\Str;

/**
 * 微信官方证书
 * Class SignCertificates
 * @package xooooooox\wechat\pay
 */
class SignCertificates
{

    /**
     * get wechat certificates list url
     * @var string
     */
    public static string $UrlCertList = 'https://api.mch.weixin.qq.com/v3/certificates';



    /**
     * 获取平台证书列表(微信支付平台证书, 不是商户API证书) https://pay.weixin.qq.com/wiki/doc/apiv3/wechatpay/wechatpay5_1.shtml
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 微信商户私钥
     * @param string $MchCertSerialNo 商户证书序列号
     * @return string
     */
    public static function Lists(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo) : string {
        $method = 'GET';
        $url = self::$UrlCertList;
        $timestamp = (string)time();
        $nonce = strtoupper(md5(Str::Nonce(32)));
        $authorization = SignV3::CountAuthorization(
            $method,
            $url,
            $timestamp,
            $nonce,
            '',
            $MchPrivateKeyContent,
            $MchId,
            $MchCertSerialNo
        );
        $header = [
            'Authorization: '.$authorization,
            'Accept: application/json',
            'User-Agent: https://zh.wikipedia.org/wiki/User_agent',
        ];
        return Curl::GetBody(Curl::Get($url,$header));
    }

}