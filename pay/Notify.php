<?php

namespace xooooooox\wechat\pay;

use ErrorException;
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
    public static function VerifySignature(string $MchId, string $MchPrivateKeyContent, string $MchCertSerialNo, string $ApiV3Secret, string $Nonce, string $Signature, string $Timestamp, string $Body) : bool {
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
            try {
                $ok = (bool)openssl_verify($str,$Signature,$cert,OPENSSL_ALGO_SHA256);
                if ($ok){
                    return true;
                }
            } catch (ErrorException $e) {
                continue;
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
    public static function DecryptCipherText(string $CipherText, string $AssociatedData, string $Nonce, string $ApiV3Secret) : string {
        $CipherText = base64_decode($CipherText);
        if (strlen($CipherText) <= 16) {
            return 'Error: `CipherText` length less than or equal 16';
        }
        try {
            // ext-sodium (default installed on >= PHP 7.2)
            if (function_exists('\sodium_crypto_aead_aes256gcm_is_available') && \sodium_crypto_aead_aes256gcm_is_available()) {
                $result = \sodium_crypto_aead_aes256gcm_decrypt($CipherText, $AssociatedData, $Nonce, $ApiV3Secret);
                if (!is_string($result)){
                    return 'Error: decrypt failed';
                }
                return $result;
            }
            // ext-libsodium (need install libsodium-php 1.x via pecl)
            if (function_exists('\Sodium\crypto_aead_aes256gcm_is_available') && \Sodium\crypto_aead_aes256gcm_is_available()) {
                $result = \Sodium\crypto_aead_aes256gcm_decrypt($CipherText, $AssociatedData, $Nonce, $ApiV3Secret);
                if (!is_string($result)){
                    return 'Error: decrypt failed';
                }
                return $result;
            }
            // openssl (PHP >= 7.1 support AEAD)
            if (PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods())) {
                $ctext = substr($CipherText, 0, -16);
                $authTag = substr($CipherText, -16);
                $result = \openssl_decrypt($ctext, 'aes-256-gcm', $ApiV3Secret, \OPENSSL_RAW_DATA, $Nonce, $authTag, $AssociatedData);
                if (!is_string($result)){
                    return 'Error: decrypt failed';
                }
                return $result;
            }
        } catch (SodiumException $e){
            return 'AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php,'.$e->getMessage();
        }
        return 'Error: decryption not performed';
    }

}