<?php
header("Content-Type: text/css");
header("Cache-Control: no-cache, must-revalidate");

$img = rand(1, 13) . ".jpg";
?>

body {
    background-image: url('../pictures/<?= $img ?>');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    background-attachment: fixed;
}
