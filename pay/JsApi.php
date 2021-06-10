<?php

namespace xooooooox\wechat\pay;

use xooooooox\http\client\Curl;
use xooooooox\nonce\Str;

/**
 * 微信JsApi支付
 * Class Jsapi
 * @package xooooooox\wechat\pay
 */
class JsApi
{

    /**
     * JsApi下单
     * @param string $MchId
     * @param string $MchPrivateKeyContent
     * @param string $MchCertSerialNo
     * @param string $AppId
     * @param string $OutTradeNo
     * @param string $Description
     * @param string $NotifyUrl
     * @param int $Total
     * @param string $OpenId
     * @param string $Attach
     * @param string $Currency
     * @return array
     */
    public static function Place(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $AppId, string $OutTradeNo, string $Description, string $NotifyUrl, int $Total, string $OpenId, string $Attach, string $Currency = 'CNY') : array {
        // 组装微信下单需要的数据格式
        $bodies = [
            'mchid' => $MchId,
            'appid' => $AppId,
            'out_trade_no' => $OutTradeNo,
            'description' => $Description,
            'notify_url' => $NotifyUrl,
            'amount' => [
                'total' => $Total,
                'currency' => $Currency,
            ],
            'attach' => $Attach,
            'payer' => [
                'openid' => $OpenId,
            ]
        ];
        $time = time();
        $method = 'POST';
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi';
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
            'Authorization: '.$authorization,
        ];
        $bodies = json_decode(Curl::GetBody(Curl::Post($url,$body,$header)),true);
        if (!is_array($bodies)){
            return [];
        }
        $bodies['timestamp'] = $timestamp;
        $bodies['nonce'] = $nonce;
        return $bodies;
    }

}