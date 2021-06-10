<?php

namespace xooooooox\wechat\pay;

use xooooooox\http\client\Curl;
use xooooooox\nonce\Str;

/**
 * 微信NATIVE支付
 * Class Native
 * @package xooooooox\wechat\pay
 */
class Native
{

    /**
     * place order url
     * @var string
     */
    public static string $UrlPlace = 'https://api.mch.weixin.qq.com/v3/pay/transactions/native';



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
    public static function Place(string $MchId = '', string $MchPrivateKeyContent = '', string $MchCertSerialNo = '', string $AppId = '', string $OutTradeNo = '', string $Description = '', string $NotifyUrl = '', int $Total = 0, string $Attach = '', string $Currency = 'CNY') : string {
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
        $url = self::$UrlPlace;
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
        return Curl::GetBody(Curl::Post(self::$UrlPlace,$body,$header));
    }

    /**
     * 根据微信支付订单号查询支付订单
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $TransactionId 微信支付订单号
     * @return string
     */
    public static function QueryByTransactionId(string $MchId = '', string $MchPrivateKeyContent = '', string $MchCertSerialNo = '', string $TransactionId) : string {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/id/'.$TransactionId.'?mchid='.$MchId;
        $timestamp = (string)time();
        $nonce = strtoupper(md5(Str::Nonce(32)));
        $authorization = SignV3::CountAuthorization(
            'GET',
            $url,
            $timestamp,
            $nonce,
            '',
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
        return Curl::GetBody(Curl::Get($url,$header));
    }

    /**
     * 根据商户订单号查询支付订单
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $OutTradeNo 商户订单号
     * @return string
     */
    public static function QueryByOutTradeNo(string $MchId = '', string $MchPrivateKeyContent = '', string $MchCertSerialNo = '', string $OutTradeNo) : string {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/'.$OutTradeNo.'?mchid='.$MchId;
        $timestamp = (string)time();
        $nonce = strtoupper(md5(Str::Nonce(32)));
        $authorization = SignV3::CountAuthorization(
            'GET',
            $url,
            $timestamp,
            $nonce,
            '',
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
        return Curl::GetBody(Curl::Get($url,$header));
    }

    /**
     * 申请退款
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $TransactionId 微信支付订单号
     * @param string $OutRefundNo 微信退款订单号
     * @param int $Total 微信支付订单总金额
     * @param int $Refund 退款总金额
     * @param string $Currency 交易货币
     * @return string
     */
    public static function RefundsByTransactionId(string $MchId = '', string $MchPrivateKeyContent = '', string $MchCertSerialNo = '', string $TransactionId, string $OutRefundNo = '', int $Total = 0, int $Refund = 0, string $Currency = 'CNY') : string {
        if ($OutRefundNo === ''){
            $OutRefundNo = 'Ri'.date('YmdHis').Str::Nonce(16);
        }
        $bodies = [
            'transaction_id' => $TransactionId,
            'out_refund_no' => $OutRefundNo,
            'refund' => $Refund, // 退款金额, 不能超过支付订单的金额
            'total' => $Total, // 原订单金额
            'currency' => $Currency, // 退款币种
        ];
        $url = 'https://api.mch.weixin.qq.com/v3/refund/domestic/refunds';
        $body = json_encode($bodies);
        $authorization = SignV3::CountAuthorization(
            'POST',
            $url,
            (string)time(),
            strtoupper(md5(Str::Nonce(32))),
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

    /**
     * 申请退款
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $OutTradeNo 商户订单号
     * @param string $OutRefundNo 微信退款订单号
     * @param int $Total 微信支付订单总金额
     * @param int $Refund 退款总金额
     * @param string $Currency 交易货币
     * @return string
     */
    public static function RefundsByOutTradeNo(string $MchId = '', string $MchPrivateKeyContent = '', string $MchCertSerialNo = '', string $OutTradeNo, string $OutRefundNo = '', int $Total = 0, int $Refund = 0, string $Currency = 'CNY') : string {
        if ($OutRefundNo === ''){
            $OutRefundNo = 'Rn'.date('YmdHis').Str::Nonce(16);
        }
        $bodies = [
            'out_trade_no' => $OutTradeNo,
            'out_refund_no' => $OutRefundNo,
            'refund' => $Refund, // 退款金额, 不能超过支付订单的金额
            'total' => $Total, // 原订单金额
            'currency' => $Currency, // 退款币种
        ];
        $url = 'https://api.mch.weixin.qq.com/v3/refund/domestic/refunds';
        $body = json_encode($bodies);
        $authorization = SignV3::CountAuthorization(
            'POST',
            $url,
            (string)time(),
            strtoupper(md5(Str::Nonce(32))),
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