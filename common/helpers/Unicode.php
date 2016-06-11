<?php

namespace common\helpers;

class Unicode
{
    const SQUARE_BASIC = 1;
    const SQUARE_A = 2;
    const SQUARE_B = 3;
    const SQUARE_C = 4;
    const SQUARE_D = 5;
    const SQUARE_E = 6;

    /**
     * utf8字符转换成Unicode字符，little endian
     * @param  [type] $utf8mb_str Utf-8字符
     * @return [type]           Unicode字符
     */
    static public function unicode_square($word) {
        if ($word >= '一' && $word <= '鿕' ) {
            return self::SQUARE_BASIC;
        } elseif ($word >= '㐀' && $word <= '䶵' ) {
            return self::SQUARE_A;
        } elseif ($word >= '𠀀' && $word <= '𪛖' ) {
            return self::SQUARE_B;
        } elseif ($word >= '𪜀' && $word <= '𫜴' ) {
            return self::SQUARE_C;
        } elseif ($word >= '𫝀' && $word <= '𫠝' ) {
            return self::SQUARE_D;
        } elseif ($word >= '𫠠' && $word <= '𬺡' ) {
            return self::SQUARE_E;
        } else {
            return false;
        }

    }


    /**
     * utf8字符转换成Unicode字符，little endian
     * @param  [type] $utf8mb_str Utf-8字符
     * @return [type]           Unicode字符
     */
    static public function unicode_encode($utf8mb_str) {
        $len = strlen($utf8mb_str);
        $unicode = 0;
        if($len == 3) {
            $unicode = (ord($utf8mb_str[0]) & 0x0F) << 12;
            $unicode |= (ord($utf8mb_str[1]) & 0x3F) << 6;
            $unicode |= (ord($utf8mb_str[2]) & 0x3F);
        } elseif($len == 4) {

            $unicode = (ord($utf8mb_str[0]) & 0x07) << 18;
            $unicode |= (ord($utf8mb_str[1]) & 0x3F) << 12;
            $unicode |= (ord($utf8mb_str[2]) & 0x3F) << 6;
            $unicode |= (ord($utf8mb_str[3]) & 0x3F);
        }     

        return dechex($unicode);
    }

    /**
     * Unicode字符转换成utf8字符
     * @param  [type] $unicode_str Unicode字符
     * @return [type]              Utf-8字符
     */
    static public function unicode_decode($unicode_str) {
        $utf8_str = '';
        $code = intval(hexdec($unicode_str));
        //这里注意转换出来的code一定得是整形，这样才会正确的按位操作
        $ord_1 = decbin(0xe0 | ($code >> 12));
        $ord_2 = decbin(0x80 | (($code >> 6) & 0x3f));
        $ord_3 = decbin(0x80 | ($code & 0x3f));
        $utf8_str = chr(bindec($ord_1)) . chr(bindec($ord_2)) . chr(bindec($ord_3));
        return $utf8_str;
    }


}


