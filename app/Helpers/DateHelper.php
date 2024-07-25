<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function getCurrentDateTime()
    {
        return Carbon::now();
    }

    public static function formatDateTime($datetime, $format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($datetime)->format($format);
    }

    public static function formatDate($date, $format = 'Y-m-d')
    {
        return Carbon::parse($date)->format($format);
    }

    public static function formatTime($time, $format = 'H:i:s')
    {
        return Carbon::parse($time)->format($format);
    }

    // Outras funções úteis relacionadas a datas podem ser adicionadas aqui
}
