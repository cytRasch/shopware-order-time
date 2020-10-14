<?php

/**
 * @param $params
 * @param $template
 * @return string
 */
function smarty_function_holidayMessage( $params, &$template )
{

    //$test = date('2019-12-23');

    $days = [
        date('Y-m-d'),
        date('Y-m-d', strtotime(' +1 day')),
        date('Y-m-d', strtotime('+2 days'))
    ];

    $holiday = null;

    foreach ( $days as $day ) {

        $isHoliday = feiertag($day);

        if ( $isHoliday !== 'weekday' && date('w', strtotime($day)) !== "0" ) {

            $holiday = $isHoliday;
            break;
        }

    }

    return $holiday;
}