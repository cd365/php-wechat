<?php

namespace xooooooox\wechat\pay;

use xooooooox\http\client\Curl;
use xooooooox\nonce\Str;

/**
 * 微信支付公共方法
 * Class Communal
 * @package xooooooox\wechat\pay
 */
class Publics
{

    /**
     * 根据微信支付订单号查询支付订单
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $TransactionId 微信支付订单号
     * @return string
     */
    public static function QueryByTransactionId(string $MchId, string $MchPrivateKeyContent , string $MchCertSerialNo, string $TransactionId) : string {
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
    public static function QueryByOutTradeNo(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $OutTradeNo) : string {
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
     * 关闭订单
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $OutTradeNo 商户订单号
     * @return string
     */
    public static function Close(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $OutTradeNo) : string {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/'.$OutTradeNo.'/close';
        $bodies = [
            'mchid' => $MchId
        ];
        $time = time();
        $method = 'POST';
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
     * 申请退款(微信支付订单号)
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $TransactionId 微信支付订单号
     * @param string $OutRefundNo 微信退款订单号
     * @param int $Total 微信支付订单总金额
     * @param int $Refund 退款总金额
     * @param string $NotifyUrl 退款结果回调
     * @param string $Reason 退款原因
     * @param string $Currency 交易货币
     * @return string
     */
    public static function RefundsByTransactionId(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $TransactionId, string $OutRefundNo, int $Total, int $Refund, string $NotifyUrl, string $Reason, string $Currency = 'CNY') : string {
        if ($OutRefundNo === ''){
            $OutRefundNo = 'Ri'.date('YmdHis').Str::Nonce(16);
        }
        $bodies = [
            'transaction_id' => $TransactionId,
            'out_refund_no' => $OutRefundNo,
            'amount' => [
                'refund' => $Refund,
                'total' => $Total,
                'currency' => $Currency,
            ],
        ];
        if ($NotifyUrl !== ''){
            $bodies['notify_url'] = $NotifyUrl;
        }
        if ($Reason !== ''){
            $bodies['reason'] = $Reason;
        }
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
     * 申请退款(商户订单号)
     * @param string $MchId 商户号
     * @param string $MchPrivateKeyContent 商户私钥内容
     * @param string $MchCertSerialNo 商户证书序列号
     * @param string $OutTradeNo 商户订单号
     * @param string $OutRefundNo 微信退款订单号
     * @param int $Total 微信支付订单总金额
     * @param int $Refund 退款总金额
     * @param string $NotifyUrl 退款结果回调
     * @param string $Reason 退款原因
     * @param string $Currency 交易货币
     * @return string
     */
    public static function RefundsByOutTradeNo(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $OutTradeNo, string $OutRefundNo, int $Total, int $Refund, string $NotifyUrl, string $Reason, string $Currency = 'CNY') : string {
        if ($OutRefundNo === ''){
            $OutRefundNo = 'Rn'.date('YmdHis').Str::Nonce(16);
        }
        $bodies = [
            'out_trade_no' => $OutTradeNo,
            'out_refund_no' => $OutRefundNo,
            'amount' => [
                'refund' => $Refund,
                'total' => $Total,
                'currency' => $Currency,
            ],
        ];
        if ($NotifyUrl !== ''){
            $bodies['notify_url'] = $NotifyUrl;
        }
        if ($Reason !== ''){
            $bodies['reason'] = $Reason;
        }
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
     * 查询单笔退款
     * @param string $MchId
     * @param string $MchPrivateKeyContent
     * @param string $MchCertSerialNo
     * @param string $OutRefundNo
     * @return string
     */
    public static function QueryRefunds(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $OutRefundNo) : string {
        $url = 'https://api.mch.weixin.qq.com/v3/refund/domestic/refunds/'.$OutRefundNo;
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

}