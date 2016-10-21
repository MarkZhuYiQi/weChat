<?php
/**
 * Created by PhpStorm.
 * User: SZL4ZSY
 * Date: 9/7/2016
 * Time: 2:49 PM
 */

function pkcs5_pad ($text, $blocksize) {  //加密时的字节填充，保持和java 一致
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}
function myCrypt($input,$key) //加密
{
    $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    $input =pkcs5_pad($input, $size);
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $data = mcrypt_generic($td, $input);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    $data = base64_encode($data);
    return $data;
}
function myDecrypt($str,$key) //解密
{
    $decrypted= mcrypt_decrypt(
        MCRYPT_RIJNDAEL_128,
        $key,
        base64_decode($str),
        MCRYPT_MODE_ECB
    );

    $dec_s = strlen($decrypted);
    $padding = ord($decrypted[$dec_s-1]);
    $decrypted = substr($decrypted, 0, -$padding);
    return $decrypted;
}

