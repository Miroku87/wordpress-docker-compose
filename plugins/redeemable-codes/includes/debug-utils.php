<?php

class Debug_Utils
{
    public static function js_console_log($message)
    {
        echo "<script type='text/javascript'>console.log(`" . $message . "`)</script>\n";
    }
}
