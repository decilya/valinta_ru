<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class ConsoleController extends Controller
{
    /** Код красного цвета */
    const COLOR_ERROR = 31;

    /** Код зеленого цвета */
    const COLOR_SUCCESS = 32;

    public function getDsnAttribute($dsn, $name = "dbname")
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    public function printColorStr($str, $code)
    {
        $code = array($code);
        echo "\033[" . implode(';', $code) . 'm' . $str . "\033[0m\n";
    }

}