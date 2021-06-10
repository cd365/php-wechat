<?php

namespace xooooooox\wechat\pay;

use xooooooox\http\client\Curl;
use xooooooox\nonce\Str;

/**
 * 微信H5支付
 * Class Jsapi
 * @package xooooooox\wechat\pay
 */
class H5
{

    /**
     * H5支付下单
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $AppId 应用ID
     * @param string $OutTradeNo 商户订单号
     * @param string $Description 支付订单描述
     * @param string $NotifyUrl 回调通知地址
     * @param int $Total 支付总金额 单位:分
     * @param string $Attach 附加信息
     * @param string $PayerClientIp 客户端IP
     * @param string $H5InfoType H5场景信息
     * @param string $Currency 交易币种
     * @return string
     */
    public static function Place(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $AppId, string $OutTradeNo, string $Description, string $NotifyUrl, int $Total, string $Attach, string $PayerClientIp, string $H5InfoType, string $Currency = 'CNY') : string {
        // 组装微信下单需要的数据格式
        $bodies = [
            'mchid' => $MchId,
            'appid' => $AppId,
            'out_trade_no' => $OutTradeNo,
            'description' => $Description,
            'notify_url' => $NotifyUrl,
            'amount' => [
                'total' => $Total,
                'currency' => $Currency
            ],
            'attach' => $Attach,
            'scene_info' => [
                'payer_client_ip' => $PayerClientIp,
                'h5_info' => [
                  'type' => $H5InfoType // iOS, Android, Wap
                ]
            ]
        ];
        $time = time();
        $method = 'POST';
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/h5';
        $timestamp = (string)$time;
        $nonce = strtoupper(md5(Str::Nonce(32)));
        $body = json_encode($bodies);
        $authorization = SignV3::CountAuthorization(
            $method,
            $url,
            $timestamp,
            $nonce,
            $body,
            $MchPrivateKeyContent,
            $MchId,
            $MchCertSerialNo
        );
        $header = [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: */*',
            'Authorization: '.$authorization
        ];
        return Curl::GetBody(Curl::Post($url,$body,$header));
    }

}