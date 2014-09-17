<?php

class CookieHandler
{
    private static $Cookie= "HandledCookie";

    public function SaveCookie($input)
    {
        setcookie(self::$Cookie, $input, 1);
    }

    public function LoadCookie()
    {
        if(isset($_COOKIE[self::$Cookie]))
            $ret = $_COOKIE[self::$Cookie];
        else
            return false;

        return $ret;
    }
}