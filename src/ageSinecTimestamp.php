<?php

namespace CoonDesign\phpGridcoin;

use DateTime;

function ageSinceTimestamp($sec)
{
    $date1 = new DateTime("@0");
    $date2 = new DateTime("@$sec");
    $interval = date_diff($date1, $date2);
    $parts = ['years' => 'y', 'months' => 'm', 'days' => 'd', 'hours' => 'h', 'minutes' => 'i', 'seconds' => 's'];
    $formatted = [];
    foreach($parts as $i => $part) {
        $value = $interval->$part;
        if ($value !== 0) {
            if ($value == 1) {
                $i = substr($i, 0, -1);
            }
            $formatted[] = "$value $i";
        }
    }

    if (count($formatted) == 0) {
        return '0 seconds';
    }

    if (count($formatted) == 1) {
        return $formatted[0];
    } else {
        $str = implode(', ', array_slice($formatted, 0, -1));
        $str.= ' and ' . $formatted[count($formatted) - 1];
        return $str;
    }
}