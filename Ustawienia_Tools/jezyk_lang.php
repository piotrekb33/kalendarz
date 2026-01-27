<?php

/* ===================== JĘZYK ===================== */
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    setcookie("lang", $lang, time() + 3600*24*30, "/");
} elseif (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
} else {
    $lang = 'en';
}
/* Ustawienia domyślnego języka na angielski, jeśli nie istnieje tłumaczenie */
if (!isset($translations[$lang])) $lang='en';
$t = $translations[$lang]; //domyślnie angielski

?>