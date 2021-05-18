<?php

namespace App\utils;

class ConsoleUtils
{
    public static function readableElapsedTime(int $time): string
    {
        $timeInMinutes = floor($time / 1000 / 60);
        $extraSeconds = ($time / 1000) % 60;
        return sprintf('%02d:%02d', $timeInMinutes, $extraSeconds);
    }

    public static function readableFileSize(int $bytes, int $decimals = 2): string
    {
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

}