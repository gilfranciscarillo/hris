<?php

use CodeIgniter\I18n\Time;

if (!function_exists('stringDateTimeToSemiLongDateTime')) {
    function stringDateTimeToSemiLongDateTime(string $dateTime): string
    {
        $time = Time::parse($dateTime);

        return $time->toLocalizedString('MMM d, yyyy H:mm:ss');
    }
}