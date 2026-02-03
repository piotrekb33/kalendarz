<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../logowanie_rejestracja_login_signup/login.php');
    exit;
}


/* ===================== ŚCIEŻKA BAZOWA ===================== */
// Jeśli calendar.php jest w podfolderze "kalendarz", wchodzimy o poziom wyżej
$rootDir = dirname(__DIR__); 
// Jeśli plik zostanie w głównym folderze, możesz zmienić na: $rootDir = __DIR__;

//Dla HTML

// Określ ścieżkę bazową względem katalogu głównego
// Jeśli calendar.php jest w katalogu głównym, zostaw '/'.
// Jeśli w podfolderze 'kalendarz/', zmień na '/kalendarz/'
$basePath = '/kalendarz/'; // <-- dostosuj jeśli folder jest inny

/* ===================== PLIKI WSPÓLNE HTML Scieżki===================== 
/ na początku linku
href="/..."
➡️ liczone od htdocs

nic na początku linku
bez / na początku
href="..."
➡️ liczone od bieżącego folderu
*/
/* ===================== PLIKI WSPÓLNE PHP Scieżki=====================*/
//echo dirname($rootDir)."<br>";
//echo $rootDir."<br>";
//echo $basePath."<br>";
//echo __DIR__."<br>";

/* ===================== PLIKI WSPÓLNE ===================== */
require $rootDir . '/baza_danych_polaczenie_db_connection/db.php';
require_once $rootDir . '/Tlumaczenia_Translations/tlumaczenia_translations.php';
require_once $rootDir . '/Ustawienia_Tools/jezyk_lang.php';
require_once $rootDir . '/Ustawienia_Tools/strefa_czasowa_time_zone.php';
require_once $rootDir . '/Ustawienia_Tools/helper_do_linkow.php';
require_once $rootDir . '/Ustawienia_Tools/daty_date.php';
require_once $rootDir . '/Ustawienia_Tools/edycja_wpisu_kalendarz_calendar_edit.php';

/* ===================== KALENDARZ ===================== */

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title>Calendar</title>
<!-- CSS -->
<link rel="stylesheet" href="<?= $basePath ?>style/style.css">
<link rel="stylesheet" href="<?= $basePath ?>style/powitanie.css">
<link rel="stylesheet" href="../style/style.php">
<!-- JS -->
<script src="<?= $basePath ?>javascripts/blysk.js"></script>
</head>
<body>

<!-- ===================== ZMIANA JĘZYKA ===================== -->
<div class="lang-switch">
    <a href="<?= q(['lang'=>'pl']) ?>" class="<?= $lang==='pl'?'active':'' ?>">🇵🇱</a>
    <a href="<?= q(['lang'=>'en']) ?>" class="<?= $lang==='en'?'active':'' ?>">🇬🇧</a>
</div>

<!-- ===================== ZMIANA STREFY ===================== -->
<div class="tz-switch" style="text-align:center; margin-bottom:10px;">
    <?php if ($tz_code === 'pl'): ?>
        <a href="<?= q(['tz'=>'uk']) ?>" class="tz-btn active"><img src="<?= $basePath ?>maps/pl.png" alt="Polska" width="32" height="32"></a>
    <?php else: ?>
        <a href="<?= q(['tz'=>'pl']) ?>" class="tz-btn active"><img src="<?= $basePath ?>maps/uk.png"  alt="UK" width="32" height="32"></a>
    <?php endif; ?>
    <span style="margin-left:10px;font-size:1em;"><?= $t['timezone_label'] ?> <?= $tz_label ?></span>
</div>

<!-- ===================== POWITANIE ===================== -->
<div class="welcome <?= $lang==='pl'?'lang-pl':'lang-en' ?>" style="display:flex;justify-content:center;align-items:center;text-align:center;width:fit-content;margin:0 auto;">
    <span class="wave">👋</span>
    <?= $t['powitanie'] ?> <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>!
</div>

<!-- ===================== PANEL ADMINA I LOGOUT ===================== -->
<div style="text-align:center; margin-bottom:25px; margin-top:10px;">
    <?php if ($isAdmin): ?>
        <a href="../tworzenie_urzytkownikow_user_creation/admin_create_user.php" style="color:#1976d2;font-weight:bold;text-decoration:none;padding:6px 16px;border-radius:6px;background:#e3f2fd;border:1px solid #1976d2;margin-right:10px;">
            🔧 <?= $t['admin_panel'] ?>
        </a>
    <?php endif; ?>
    <a href="../logowanie_rejestracja_login_signup/logout.php" style="color:#c00;font-weight:bold;text-decoration:none;padding:6px 16px;border-radius:6px;background:#fff3e0;border:1px solid #ffd600;">
        <?= $t['logout'] ?>
    </a>
</div>

<h2><?= $months[$lang][$month]." ".$year ?></h2>

<!-- ===================== NAWIGACJA MIESIĘCY ===================== -->
<div class="nav">
    <a href="<?= q(['month'=>$prevMonth,'year'=>$prevYear]) ?>">⬅ <?= $t['prev'] ?></a>
    |
    <a href="<?= q(['month'=>$nextMonth,'year'=>$nextYear]) ?>"><?= $t['next'] ?> ➡</a>
</div>

<!-- ===================== KALENDARZ ===================== -->
<table class="calendar">
<tr>
<?php foreach($days[$lang] as $d) echo "<th>$d</th>"; ?>
</tr>
<tr>
<?php
for($i=1;$i<$startDay;$i++) echo "<td></td>";

for($day=1;$day<=$daysInMonth;$day++){
    if((($day+$startDay-1)%7)==1 && $day!=1) echo "</tr><tr>";
    $date = sprintf("%04d-%02d-%02d",$year,$month,$day);
    $weekday = ($day + $startDay - 2) % 7;
    $classes = 'day';
    if($weekday>=5) $classes .= ' weekend';
    if(isset($events[$date])) $classes .= ' has-event';

    $today = (new DateTime('now', $tz))->format('Y-m-d');
    if ($date === $today) $classes .= ' today';

    echo "<td class='$classes'><strong>$day</strong>";

    if(isset($events[$date])){
        foreach($events[$date] as $e){
            $event_title = htmlspecialchars($e['title']);
            $event_time = $e['event_time'] ?? '';
            $eid = $e['id'];

            if (isset($_GET['edit']) && $_GET['edit'] == $eid) {
                echo "<form class='edit-form' method='post' style='margin:5px 0;' onsubmit='return saveEditAjax(this,$eid);'>
                    <input type='hidden' name='edit_id' value='$eid'>
                    <input type='time' name='time' value='".htmlspecialchars($event_time)."'>
                    <input type='text' name='title' value='".htmlspecialchars($event_title)."' required>
                    <button type='submit'>{$t['save']}</button>
                    <button type='button' onclick=\"window.location.href='".q()."'\">
                        ".($lang==='pl'?'Anuluj':'Cancel')."
                    </button>
                    <span class='edit-status' id='edit-status-$eid'></span>
                </form>";
            } else {
                echo "<div class='event'><span class='time'>$event_time</span> $event_title";
                if ($isAdmin) {
                    echo "<a href='".q(['edit'=>$eid])."' title='Edit'>✏️</a>
                          <a href='".q(['delete'=>$eid])."' title='Delete' onclick='return confirm(\"Na pewno usunąć?\")'>🗑️</a>";
                }
                echo "</div>";
            }
        }
    }

    if ($isAdmin) echo "<a class='add' href='".q(['add'=>$date])."'>➕ {$t['add']}</a>";

    if ($isAdmin && isset($_GET['add']) && $_GET['add'] === $date){
        echo "<form method='post' class='add-form' style='margin-top:5px;' onsubmit='return addEventAjax(this);'>
            <input type='hidden' name='date' value='".htmlspecialchars($date)."'>
            <input type='time' name='time' required>
            <input type='text' name='title' placeholder='{$t['event_title']}' required>
            <button type='submit'>{$t['save']}</button>
            <button type='button' onclick=\"window.location.href='".q()."'\">
                ".($lang==='pl'?'Anuluj':'Cancel')."
            </button>
            <span class='add-status' id='add-status'></span>
        </form>";
    }

    echo "</td>";
}
?>
</tr>
</table>

<script src="<?= $basePath ?>javascripts/refajax.js" defer></script>
</body>
</html>
