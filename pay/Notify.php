<?php

namespace xooooooox\wechat\pay;

use SodiumException;

/**
 * 微信支付回调通知
 * Class Notify
 * @package xooooooox\wechat\pay
 */
class Notify
{

    /**
     * 校验微信回调通知签名
     * @param string $MchId 微信商户号
     * @param string $MchPrivateKeyContent 微信支付商户私钥
     * @param string $MchCertSerialNo 微信支付证书序列号
     * @param string $ApiV3Secret 微信支付V3秘钥
     * @param string $Nonce 微信回调通知头部信息: http.header.Wechatpay-Nonce
     * @param string $Signature 微信回调通知头部信息: http.header.Wechatpay-Signature
     * @param string $Timestamp 微信回调通知头部信息: http.header.Wechatpay-Timestamp
     * @param string $Body 微信回调通知正文信息: http.body
     * @return bool
     */
    public static function VerifySignature(string $MchId = '', string $MchPrivateKeyContent = '', string $MchCertSerialNo = '', string $ApiV3Secret = '', string $Nonce = '', string $Signature = '', string $Timestamp = '', string $Body = '') : bool {
        $certLists = SignCertificates::Lists($MchId,$MchPrivateKeyContent,$MchCertSerialNo);
        $certs = json_decode($certLists,true);
        if (!isset($certs['data'])) {
            return false;
        }
        $data = $certs['data'];
        $Signature = base64_decode($Signature);
        if ($Body === ''){
            $str = $Timestamp."\n".
                $Nonce."\n";
        }else{
            $str = $Timestamp."\n".
                $Nonce."\n".
                $Body."\n";
        }
        foreach ($data as $v){
            if (!isset($v['encrypt_certificate'])){
                continue;
            }
            $w = $v['encrypt_certificate'];
            if (!isset($w['ciphertext']) || !isset($w['associated_data']) || !isset($w['nonce'])) {
                continue;
            }
            $cert = self::DecryptCipherText($w['ciphertext'],$w['associated_data'],$w['nonce'],$ApiV3Secret);
            if (!is_string($cert)){
                continue;
            }
            $ok = openssl_verify($str,$Signature,$cert,OPENSSL_ALGO_SHA256);
            if (is_bool($ok) && $ok === true){
                return true;
            }
        }
        return false;
    }

    /**
     * 解密回调通知的数据
     * @param string $CipherText 微信通知 http.body.resource.ciphertext $bodies['resource']['ciphertext']
     * @param string $AssociatedData 微信通知 http.body.resource.associated_data $bodies['resource']['associated_data']
     * @param string $Nonce 微信通知 http.body.resource.nonce $bodies['resource']['nonce']
     * @param string $ApiV3Secret 微信支付V3秘钥
     * @return string
     */
    public static function DecryptCipherText(string $CipherText = '', string $AssociatedData = '', string $Nonce = '', string $ApiV3Secret = '') : string {
        try {
            $result = sodium_crypto_aead_aes256gcm_decrypt($CipherText,$AssociatedData,$Nonce,$ApiV3Secret);
            if (is_bool($result) && $result === false) {
                return 'Error: decrypt failed';
            }
            return (string)$result;
        } catch (SodiumException $e) {
            return 'Error: '.$e->getMessage();
        }
    }

}