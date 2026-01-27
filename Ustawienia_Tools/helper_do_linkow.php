<?php
/* ===================== HELPER DO LINKÓW =====================
function q(array $extra = []) {
    $base = [
        'month' => $GLOBALS['month'],
        'year'  => $GLOBALS['year'],
        'lang'  => $GLOBALS['lang'] ?? 'en',
        'tz'    => $GLOBALS['tz_code'] ?? 'uk'
    ];
    return '?' . http_build_query(array_merge($base, $extra));
}*/

/* ===================== HELPER DO LINKÓW ===================== */
function q(array $overrides = []): string {
    $params = $_GET;

    // Nadpisz parametry przekazane do funkcji
    foreach ($overrides as $k => $v) {
        $params[$k] = $v;
    }

    // Walidacja month i year
    if (isset($params['month'])) {
        $params['month'] = max(1, min(12, (int)$params['month']));
    }
    if (isset($params['year'])) {
        $params['year'] = max(1, (int)$params['year']);
    }

    $query = http_build_query($params);
    return basename($_SERVER['PHP_SELF']) . '?' . $query;
}
?>