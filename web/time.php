<?php
function getLocalDate($timestamp) {
    $dt = new DateTime($timestamp, new DateTimeZone('UTC'));
    $dt->setTimezone($_SESSION['timezone']);
    return $dt->format('d/m/Y');
}

function getLocalTime($timestamp) {
    $dt = new DateTime($timestamp, new DateTimeZone('UTC'));
    $dt->setTimezone($_SESSION['timezone']);
    return $dt->format('H:i');
}
?>