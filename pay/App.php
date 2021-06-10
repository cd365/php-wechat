<?php

namespace xooooooox\wechat\pay;

use xooooooox\http\client\Curl;
use xooooooox\nonce\Str;

/**
 * 微信APP支付
 * Class App
 * @package xooooooox\wechat\pay
 */
class App
{

    /**
     * place order url
     * @var string
     */
    public static string $UrlPlace = 'https://api.mch.weixin.qq.com/v3/pay/transactions/app';



    /**
     * 微信支付-APP-下单
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
     * @return array
     */
    public static function Place(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $AppId, string $OutTradeNo, string $Description, string $NotifyUrl, int $Total, string $Attach, string $Currency = 'CNY') : array {
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
        $bodies = json_decode(Curl::GetBody(Curl::Post(self::$UrlPlace,$body,$header)),true);
        if (!is_array($bodies)){
            return [];
        }
        $bodies['timestamp'] = $timestamp;
        $bodies['nonce'] = $nonce;
        return $bodies;
    }

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
     * @param string $MchId
     * @param string $MchPrivateKeyContent
     * @param string $MchCertSerialNo
     * @param string $OutTradeNo
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
     * 申请退款
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
            'refund' => $Refund,
            'total' => $Total,
            'currency' => $Currency,
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
     * 申请退款
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
            'refund' => $Refund,
            'total' => $Total,
            'currency' => $Currency,
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
     * 查询退款
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