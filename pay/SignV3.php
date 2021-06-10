<?php

namespace xooooooox\wechat\pay;

/**
 * 微信支付V3签名
 * Class SignV3
 * @package xooooooox\wechat\pay
 */
class SignV3
{

    /**
     * 获取请求的绝对UR,并去除域名部分得到参与签名的URL;如果请求中有查询参数,URL末尾应附加有'?'和对应的查询字符串
     * @param string $url
     * @return string
     */
    public static function GetAbsUrlPath(string $url) : string {
        $urls = parse_url($url);
        if (!isset($urls['path'])){
            return '';
        }
        $path = (string)$urls['path'];
        if (isset($urls['query'])){
            $path .= '?'.(string)$urls['query'];
            if (isset($urls['fragment'])){
                $path .= '#'.(string)$urls['fragment'];
            }
        }
        return $path;
    }

    /**
     * 打包签名原始字符串
     * @param string $Method
     * @param string $Url
     * @param string $Timestamp
     * @param string $Nonce
     * @param string $Body
     * @return string
     */
    public static function PackageSignOriginStr(string $Method, string $Url, string $Timestamp, string $Nonce, string $Body) : string {
        $Newline = "\n";
        $Method = strtoupper($Method);
        $first = substr($Url,0,1);
        if ($first !== '/'){
            $Url = self::GetAbsUrlPath($Url);
        }
        return $Method.$Newline.
            $Url.$Newline.
            $Timestamp.$Newline.
            $Nonce.$Newline.
            $Body.$Newline;
    }

    /**
     * 微信支付V3 RSA-BASE64签名计算
     * @param string $Str 待加密的字符串
     * @param string $MchPrivateKeyContent 微信商户私钥内容
     * @return string
     */
    public static function EncryptRsaBase64(string $Str, string $MchPrivateKeyContent = '') : string {
        $sign = '';
        openssl_sign($Str,$sign, $MchPrivateKeyContent,'sha256WithRSAEncryption');
        if ($sign === ''){
            return $sign;
        }
        return base64_encode($sign);
    }

    /**
     * 微信支付V3签名 http.header.Authorization
     * @param string $Method 请求方法
     * @param string $Url 请求完整地址
     * @param string $Timestamp 时间戳
     * @param string $Nonce 随机字符串
     * @param string $Body 请求BODY
     * @param string $MchPrivateKeyContent 私钥内容
     * @param string $MchId 商户ID
     * @param string $MchCertSerialNo 商户证书序列号
     * @return string
     */
    public static function CountAuthorization(string $Method, string $Url, string $Timestamp, string $Nonce, string $Body, string $MchPrivateKeyContent, string $MchId, string $MchCertSerialNo) : string {
        $Str = self::PackageSignOriginStr($Method,$Url,$Timestamp,$Nonce,$Body);
        $signature = self::EncryptRsaBase64($Str,$MchPrivateKeyContent);
        $schema = 'WECHATPAY2-SHA256-RSA2048';
        $authorization = sprintf('mchid="%s",serial_no="%s",nonce_str="%s",timestamp="%d",signature="%s"', $MchId,$MchCertSerialNo, $Nonce, $Timestamp,  $signature);
        return $schema.' '.$authorization;
    }

}