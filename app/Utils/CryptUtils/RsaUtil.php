<?php

namespace App\Utils\CryptUtils;

class RsaUtil
{
    // RSA 加密函数
    function encrypt($data, $key): string
    {
        $encryptedData = null;
        //openssl_public_encrypt($data, $encryptedData, $key, OPENSSL_PKCS1_OAEP_PADDING);
        openssl_private_encrypt($data, $encryptedData, $key);
        return base64_encode($encryptedData);
    }

// RSA 解密函数
    function decrypt($encryptedData, $key) {
        $encryptedData = base64_decode($encryptedData);
        $decryptedData = null;
        //openssl_private_decrypt($encryptedData, $decryptedData, $key, OPENSSL_PKCS1_OAEP_PADDING);
        openssl_public_decrypt($encryptedData, $decryptedData, $key);
        return $decryptedData;
    }
}
