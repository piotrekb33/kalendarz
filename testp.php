<?php
$months = [
    'pl'=>[1=>'Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
    'en'=>[1=>'January','February','March','April','May','June','July','August','September','October','November','December']
];
foreach ($months['en'] as $month) {
    echo $month . "\n";
}

foreach ($months['en'] as $month) {
    echo $month . "<br>";
}

foreach ($months['en'] as $key => $value) {
    echo "$key: $value<br>";
    echo "Wszystko z tabicy ponizej jest w tablicy months <br>";
}

foreach ($months as $key => $value) {
    foreach ($value as $k => $v) {
        echo "$key: $k: $v<br>";
    }
}
echo $months['pl'][8];
?>
