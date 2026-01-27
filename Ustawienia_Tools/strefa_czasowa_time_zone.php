<?php

/* ===================== STREFA CZASOWA ===================== */
if (isset($_GET['tz'])) {
    $tz_code = $_GET['tz'];
    setcookie("tz_code", $tz_code, time() + 3600*24*30, "/");
} elseif (isset($_COOKIE['tz_code'])) {
    $tz_code = $_COOKIE['tz_code'];
} else {
    $tz_code = 'uk'; // domyślnie UK
}

/* Ustawienia strefy czasowej dla Polski 'pl' i UK */
if ($tz_code === 'pl') {
    $tz = new DateTimeZone('Europe/Warsaw');
    $tz_label = $t['tz_pl'];
} else {
    $tz = new DateTimeZone('Europe/London');
    $tz_label = $t['tz_uk'];
}


?>