<?php

namespace App\Utils;

class Oauth
{
    /**
     * 计算 password hash 值
     *
     * @param string $stringToEncrypt
     *
     * @return string
     */
    public static function hashString($stringToEncrypt)
    {
        $hash = md5($stringToEncrypt);

        return $hash;
    }

    /**
     * 生成 salt
     *
     * @return string
     */
    public static function generateSalt()
    {
        return substr(base64_encode(mcrypt_create_iv(100, MCRYPT_DEV_URANDOM)), 0, 10);
    }
}
