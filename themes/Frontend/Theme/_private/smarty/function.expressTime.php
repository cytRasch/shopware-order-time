<?php

// fix this
function smarty_function_expressTime( $params, &$template )
{

    $date = $params['date'] ?? date("Y-m-d");
    $deadLine = '17:00';
    $day = time() < strtotime($deadLine) ? 1 : 2;
    $isHoliday = feiertag($date, 'SN');

    $dayNames = [
        "Sonntag",
        "Montag",
        "Dienstag",
        "Mittwoch",
        "Donnerstag",
        "Freitag",
        "Samstag"
    ];

    if ( $isHoliday === 'weekday' ) {

        $targetDate= date('Y-m-d', strtotime("{$date} +{$day} day"));
        $targetIsHoliday = feiertag($targetDate, 'SN');

        while($targetIsHoliday !== 'weekday') {

            $day++;

            $targetDate = date('Y-m-d', strtotime("{$targetDate} +{$day} day"));
            $targetIsHoliday = feiertag($targetDate, 'SN');
        }

    } elseif ( $isHoliday === 'sunday' ) {

        $day++;

        $targetDate= date('Y-m-d', strtotime("{$date} +{$day} day"));
        $targetIsHoliday = feiertag($targetDate, 'SN');

        while($targetIsHoliday !== 'weekday') {

            $day++;

            $targetDate = date('Y-m-d', strtotime("{$targetDate} +{$day} day"));
            $targetIsHoliday = feiertag($targetDate, 'SN');
        }


    } else {

        return 'An gesetzlichen Feiertagen bieten in Sachsen wir <strong>keinen</strong> Expressversand an.';
    }

    $wording = date('w', strtotime($targetDate)) === "6" ? 'Express mit Samstagszustellung' : 'Express';

    return  '<strong> Heute bis ' . $deadLine . ' Uhr</strong> per ' . $wording . ' bestellen, garantierte Lieferung ' . $dayNames[date('w', strtotime($targetDate))] . ', den <strong>' . date('d.m.Y', strtotime($targetDate)) . '</strong><sup>2</sup>.';
}


/**
 * @param        $datum
 * @param string $bundesland
 * @return bool|string
 */
function feiertag( $datum, $bundesland = '' )
{

    $bundesland = strtoupper($bundesland);
    if ( is_object($datum) ) {
        $datum = date("Y-m-d", $datum);
    }
    $datum = explode("-", $datum);

    $datum[1] = str_pad($datum[1], 2, "0", STR_PAD_LEFT);
    $datum[2] = str_pad($datum[2], 2, "0", STR_PAD_LEFT);

    if ( !checkdate($datum[1], $datum[2], $datum[0]) ) return false;

    $datum_arr = getdate(mktime(0, 0, 0, $datum[1], $datum[2], $datum[0]));

    $easter_d = date("d", easter_date($datum[0]));
    $easter_m = date("m", easter_date($datum[0]));

    $status = 'weekday';
    if ( $datum_arr['wday'] == 0 ) $status = 'sunday';

    if ( $datum[1] . $datum[2] == '0101' ) {
        return 'Neujahr';
    } elseif ( $datum[1] . $datum[2] == '0106'
        && ($bundesland == 'BW' || $bundesland == 'BY' || $bundesland == 'ST') ) {
        return 'Heilige Drei Könige';
    } elseif ( $datum[1] . $datum[2] == date("md", mktime(0, 0, 0, $easter_m, $easter_d - 2, $datum[0])) ) {
        return 'Karfreitag';
    } elseif ( $datum[1] . $datum[2] == $easter_m . $easter_d ) {
        return 'Ostersonntag';
    } elseif ( $datum[1] . $datum[2] == date("md", mktime(0, 0, 0, $easter_m, $easter_d + 1, $datum[0])) ) {
        return 'Ostermontag';
    } elseif ( $datum[1] . $datum[2] == '0501' ) {
        return 'Erster Mai';
    } elseif ( $datum[1] . $datum[2] == date("md", mktime(0, 0, 0, $easter_m, $easter_d + 39, $datum[0])) ) {
        return 'Christi Himmelfahrt';
    } elseif ( $datum[1] . $datum[2] == date("md", mktime(0, 0, 0, $easter_m, $easter_d + 49, $datum[0])) ) {
        return 'Pfingstsonntag';
    } elseif ( $datum[1] . $datum[2] == date("md", mktime(0, 0, 0, $easter_m, $easter_d + 50, $datum[0])) ) {
        return 'Pfingstmontag';
    } elseif ( $datum[1] . $datum[2] == date("md", mktime(0, 0, 0, $easter_m, $easter_d + 60, $datum[0]))
        && ($bundesland == 'BW' || $bundesland == 'BY' || $bundesland == 'HE' || $bundesland == 'NW' || $bundesland == 'RP' || $bundesland == 'SL' || $bundesland == 'SN' || $bundesland == 'TH') ) {
        return 'Fronleichnam';
    } elseif ( $datum[1] . $datum[2] == '0815'
        && ($bundesland == 'SL' || $bundesland == 'BY') ) {
        return 'Mariä Himmelfahrt';
    } elseif ( $datum[1] . $datum[2] == '1003' ) {
        return 'Tag der deutschen Einheit';
    } elseif ( $datum[1] . $datum[2] == '1031'
        && ($bundesland == 'BB' || $bundesland == 'MV' || $bundesland == 'SN' || $bundesland == 'ST' || $bundesland == 'TH') ) {
        return 'Reformationstag';
    } elseif ( $datum[1] . $datum[2] == '1101'
        && ($bundesland == 'BW' || $bundesland == 'BY' || $bundesland == 'NW' || $bundesland == 'RP' || $bundesland == 'SL') ) {
        return 'Allerheiligen';
    } elseif ( $datum[1] . $datum[2] == strtotime("-11 days", strtotime("1 sunday", mktime(0, 0, 0, 11, 26, $datum[0])))
        && $bundesland == 'SN' ) {
        return 'Buß- und Bettag';
    } elseif ( $datum[1] . $datum[2] == '1224' ) {
        return 'Heiliger Abend (Bankfeiertag)';
    } elseif ( $datum[1] . $datum[2] == '1225' ) {
        return '1. Weihnachtsfeiertag';
    } elseif ( $datum[1] . $datum[2] == '1226' ) {
        return '2. Weihnachtsfeiertag';
    } elseif ( $datum[1] . $datum[2] == '1231' ) {
        return 'Silvester (Bankfeiertag)';
    } else {
        return $status;
    }
}
