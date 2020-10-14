<?php

/**
 * @param $params
 * @param $template
 * @return string
 */
function smarty_function_orderTime( $params, &$template )
{

    $date = date('Y-m-d');
    $deadLine = '12:00';
    $isHoliday = feiertag($date, 'SN');

    // TODO: add multi-lang
    $dayNames = [
        "Sonntag",
        "Montag",
        "Dienstag",
        "Mittwoch",
        "Donnerstag",
        "Freitag",
        "Samstag"
    ];

    // no holiday or weekend
    if ( $isHoliday === 'weekday' ) {

        // under deadline
        if ( time() < strtotime($deadLine) ) {

            $targetTime = date_create(date('Y-m-d H:i', strtotime($deadLine)));
            $currentTime = date_create(date('Y-m-d H:i'));

            $timeDifference = date_diff($targetTime, $currentTime);

            return 'In <strong>' . $timeDifference->format('%H Std. %I min.') . '</strong> bestellen, <strong>Versand heute</strong><sup>2</sup>';

        }

        $day = 1;

        while ( $isHoliday !== 'weekday' ) {
            $day++;
        }

        return '<strong>Heute</strong> bestellt, Versand schon <strong>' . $dayNames[date('w', strtotime("+{$day} days"))] . '</strong><sup>2</sup>';
    }

}
