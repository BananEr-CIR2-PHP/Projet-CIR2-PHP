<?php
function getLocalDate($timestamp) {
    $dt = new DateTime($timestamp, new DateTimeZone('UTC'));
    $dt->setTimezone($_SESSION['timezone']);
    return $dt->format('d/m/Y');
}

function getLocalTime($timestamp) {
    $dt = new DateTime($timestamp, $_SESSION['timezone']);
    return $dt->format('H:i');
}

/**
 * Get the specified day of this week. i.e.: monday of this week
 * /!\ Tricky!! user timezone in Unix format
 * @return int Get timestamp of specified day of this week, at given time
 */
function getWeekDay($base_tmstmp, $day, $hour=0, $minute=0, $second=0) {
    $dt = new DateTime();
    $dt->setTimestamp($base_tmstmp);
    $dt->setTimezone($_SESSION['timezone']);

    // $dt->modify("next ".$day)->setTime($hour, $minute, $second);
    $dt->modify($day." this week")->setTime($hour, $minute, $second);

    return $dt->getTimestamp();
}

function formatTimestamp($timestamp, $format) {
    $dt = new DateTime();
    $dt->setTimestamp($timestamp);
    $dt->setTimezone($_SESSION['timezone']);
    return $dt->format($format);
}

function formatDay($timestamp) {
    return formatTimestamp($timestamp, 'd/m/Y');
}

/**
 * Format timestamp to format YYYY-MM-DD hh:mm:ss
 * /!\ requires valid session
 * @param timestamp Time to format
 * @return string Formatted string : YYYY-MM-DD hh:mm:ss
 */
function formatAlpha($timestamp) {
    return formatTimestamp($timestamp, 'Y-m-d H:i:s');
}

/**
 * @return int number of seconds since midnight, in user timezone
 */
function getTimeSinceDaystart($timestamp) {
    $dt = new DateTime();
    $dt->setTimestamp($timestamp);
    $dt->setTimezone($_SESSION['timezone']);
    $dt->setTime(0, 0, 0);

    return $timestamp - $dt->getTimestamp() - $dt->getOffset();
}

/**
 * Get the number of days from $start_tmstmp to $target_tmstmp
 * @param start_tmstmp Start timestamp
 * @param target_tmstmp Target timestamp
 * @return int number of days from start to target
 */
function getDaysTo($start_tmstmp, $target_tmstmp) {
    $dt_start = new DateTime();
    $dt_start->setTimestamp($start_tmstmp);
    $dt_start->setTime(0, 0, 0);

    $dt_target = new DateTime();
    $dt_target->setTimestamp($target_tmstmp);
    $dt_target->setTime(0, 0, 0);

    $interval = $dt_start->diff($dt_target);
    return intval($interval->format("%a"));
}

/**
 * @param free_slots an array. $free_slots[i]['start_tmstmp'] and $free_slots[i]['end_tmstmp'] must be defined timestamps
 * @param position_duration Fixed time for each slot, in seconds
 * @param start_tmstmp Treshold for given slots. Slots before this treshold are ignored.
 * @param end_tmstmp Treshold for given slots. Slots after this treshold are ignored.
 * @param min_position[out] Number of the earliest available slot in a day. slot 0 is at midnight.
 * @param max_position[out] Number of the latest available slot in a day. slot 0 is at midnight.
 */
function getMinMaxAvailableSlots($free_slots, $position_duration, $start_tmstmp, $end_tmstmp, &$min_position, &$max_position) {
    $total_slots = ceil(24*3600 / $position_duration);
    foreach ($free_slots as $slot) {
        $slot_start_tmstmp = $slot['start_tmstmp'];
        $slot_end_tmstmp = $slot['end_tmstmp'];

        // If start and end are not on the same day, then at least one limit is midnight
        if (formatTimestamp($slot_start_tmstmp, 'Y-m-d') != formatTimestamp($slot_end_tmstmp, 'Y-m-d')) {
            if ($slot_start_tmstmp < $start_tmstmp) {
                $min_position = 0;
                continue;
            }
            else if ($slot_start_tmstmp > $start_tmstmp) {
                $max_position = $total_slots;
                continue;
            }
            else {
                $min_position = 0;
                $max_position = $total_slots;
                break;
            }
        }

        // Get slot of start
        $start_slot = floor(getTimeSinceDaystart($slot_start_tmstmp)/$position_duration);
        $end_slot = ceil(getTimeSinceDaystart($slot_end_tmstmp)/$position_duration);
        if (!isset($min_position) || $start_slot < $min_position) {
            $min_position = $start_slot;
        }
        if (!isset($max_position) || $end_slot > $max_position) {
            $max_position = $end_slot;
        }     
    }
}
?>