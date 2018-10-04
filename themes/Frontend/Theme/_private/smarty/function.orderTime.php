<?php


function smarty_function_orderTime( $params, &$template )
{

    $date = date('Y-m-d');
    $deadLine = '12:00';
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


/**
 * Ermittle Feiertage, Arbeitstage und Wochenenden von einem Datum
 *
 * @param        $datum      als String im Format Y-m-d oder als Timestamp
 * @param string $bundesland <br>
 *                           BW = Baden-Württemberg<br>
 *                           BY = Bayern<br>
 *                           BE = Berlin<br>
 *                           BB = Brandenburg<br>
 *                           HB = Bremen<br>
 *                           HH = Hamburg<br>
 *                           HE = Hessen<br>
 *                           MV = Mecklenburg-Vorpommern<br>
 *                           NI = Niedersachsen<br>
 *                           NW = Nordrhein-Westfalen<br>
 *                           RP = Rheinland-Pfalz<br>
 *                           SL = Saarland<br>
 *                           SN = Sachsen<br>
 *                           ST = Sachsen-Anhalt<br>
 *                           SH = Schleswig-Holstein<br>
 *                           TH = Thüringen
 * @return 'Arbeitstag', 'Wochenende' oder Name des Feiertags als String
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