<?php

namespace xooooooox\wechat\pay;

use xooooooox\http\client\Curl;
use xooooooox\nonce\Str;

/**
 * 微信Native支付
 * Class Native
 * @package xooooooox\wechat\pay
 */
class Native
{

    /**
     * 微信支付-NATIVE-下单
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $AppId 应用ID
     * @param string $OutTradeNo 直营商户订单号
     * @param string $Description 订单商品描述
     * @param string $NotifyUrl 回调通知地址
     * @param int $Total 订单支付总金额 单位: 分
     * @param string $Attach 订单附加数据
     * @param string $Currency 支付币种, 默认 CNY:人民币
     * @return string
     */
    public static function Place(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $AppId, string $OutTradeNo, string $Description, string $NotifyUrl, int $Total, string $Attach, string $Currency = 'CNY') : string {
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
            'attach' => $Attach
        ];
        $time = time();
        $method = 'POST';
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/native';
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
        return Curl::GetBody(Curl::Post($url,$body,$header));
    }

}