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
}