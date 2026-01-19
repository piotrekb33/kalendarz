<?php
require "db.php";



/* ===================== JĘZYK ===================== */
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    setcookie("lang", $lang, time() + 3600*24*30, "/");
} elseif (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
} else {
    $lang = 'en';
}

/* ===================== STREFA CZASOWA ===================== */
if (isset($_GET['tz'])) {
    $tz_code = $_GET['tz'];
    setcookie("tz_code", $tz_code, time() + 3600*24*30, "/");
} elseif (isset($_COOKIE['tz_code'])) {
    $tz_code = $_COOKIE['tz_code'];
} else {
    $tz_code = 'uk'; // domyślnie UK
}


$translations = [
    'pl' => [
        'prev'=>'Poprzedni','next'=>'Następny','add'=>'Dodaj',
        'add_event'=>'Dodaj wydarzenie','event_title'=>'Opis wydarzenia',
        'event_time'=>'Godzina (HH:MM)','save'=>'Zapisz','language'=>'Język',
        'edit_event'=>'Edytuj wydarzenie',
        'timezone_label'=>'Strefa:',
        'tz_pl'=>'Polska',
        'tz_uk'=>'Wielka Brytania',
    ],
    'en' => [
        'prev'=>'Previous','next'=>'Next','add'=>'Add',
        'add_event'=>'Add event','event_title'=>'Event description',
        'event_time'=>'Time (HH:MM)','save'=>'Save','language'=>'Language',
        'edit_event'=>'Edit event',
        'timezone_label'=>'Timezone:',
        'tz_pl'=>'Poland',
        'tz_uk'=>'UK',
    ]
];

if (!isset($translations[$lang])) $lang='pl';
$t = $translations[$lang];

if ($tz_code === 'pl') {
    $tz = new DateTimeZone('Europe/Warsaw');
    $tz_label = $t['tz_pl'];
} else {
    $tz = new DateTimeZone('Europe/London');
    $tz_label = $t['tz_uk'];
}

$translations = [
    'pl' => [
        'prev'=>'Poprzedni','next'=>'Następny','add'=>'Dodaj',
        'add_event'=>'Dodaj wydarzenie','event_title'=>'Opis wydarzenia',
        'event_time'=>'Godzina (HH:MM)','save'=>'Zapisz','language'=>'Język',
        'edit_event'=>'Edytuj wydarzenie',
        'timezone_label'=>'Strefa:',
        'tz_pl'=>'Polska',
        'tz_uk'=>'Wielka Brytania',
    ],
    'en' => [
        'prev'=>'Previous','next'=>'Next','add'=>'Add',
        'add_event'=>'Add event','event_title'=>'Event description',
        'event_time'=>'Time (HH:MM)','save'=>'Save','language'=>'Language',
        'edit_event'=>'Edit event',
        'timezone_label'=>'Timezone:',
        'tz_pl'=>'Poland',
        'tz_uk'=>'UK',
    ]
];

if (!isset($translations[$lang])) $lang='pl';
$t = $translations[$lang];

/* ===================== DNI / MIESIĄCE ===================== */
$days = [
    'pl'=>['Pon','Wto','Śro','Czw','Pią','Sob','Nie'],
    'en'=>['Mon','Tue','Wed','Thu','Fri','Sat','Sun']
];
$months = [
    'pl'=>[1=>'Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
    'en'=>[1=>'January','February','March','April','May','June','July','August','September','October','November','December']
];

/* ===================== DATA ===================== */
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

if ($month<1){$month=12;$year--;}
if ($month>12){$month=1;$year++;}

/* ===================== NAWIGACJA ===================== */
$prevMonth = $month-1; $prevYear=$year; if($prevMonth<1){$prevMonth=12;$prevYear--;}
$nextMonth = $month+1; $nextYear=$year; if($nextMonth>12){$nextMonth=1;$nextYear++;}

/* ===================== ZAPIS WYDARZENIA ===================== */
/*if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? null;
    $title = trim($_POST['title'] ?? '');

    if ($date && $title) {
        $stmt = $conn->prepare("INSERT INTO events (event_date, event_time, title) VALUES (?,?,?)");
        $stmt->bind_param("sss",$date,$time,$title);
        $stmt->execute();
    }

    header("Location: calendar.php?month=$month&year=$year&lang=$lang");
    exit;
} */

/* ===================== USUWANIE ===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    header("Location: calendar.php?month=$month&year=$year&lang=$lang");
    exit;
}


// ===================== EDYCJA WYDARZENIA =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id    = (int)$_POST['edit_id'];
    $title = trim($_POST['title'] ?? '');
    $time  = $_POST['time'] ?? null;

    if ($title !== '') {
        $stmt = $conn->prepare(
            "UPDATE events SET title = ?, event_time = ? WHERE id = ?"
        );
        $stmt->bind_param("ssi", $title, $time, $id);
        $stmt->execute();
        echo "✅ Zapisano zmiany";
    } else {
        echo "❌ Błąd danych";
    }
    exit;
}

/* ===================== DODAWANIE WYDARZENIA ===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {

    $date  = $_POST['date'];
    $title = trim($_POST['title'] ?? '');
    $time  = $_POST['time'] ?? null;

    if ($date && $title !== '') {
        $stmt = $conn->prepare(
            "INSERT INTO events (event_date, event_time, title)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $date, $time, $title);
        $stmt->execute();
        echo "✅ Dodano";
    } else {
        echo "❌ Błąd danych";
    }
    exit;
}


/* ===================== WYDARZENIA ===================== */
$events = [];
$result = $conn->query("SELECT id,event_date,event_time,title FROM events");
while($row = $result->fetch_assoc()) {
    $events[$row['event_date']][] = $row;
}

/* ===================== KALENDARZ ===================== */
$firstDay = mktime(0,0,0,$month,1,$year);
$daysInMonth = date('t',$firstDay);
$startDay = date('N',$firstDay);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title>Calendar</title>
<link rel="stylesheet" href="style.css">

<script src="javascripts/blysk.js"></script>
</head>
<body>




<div class="lang-switch">
    <a href="?lang=pl&month=<?= $month ?>&year=<?= $year ?>" class="<?= $lang==='pl'?'active':'' ?>">🇵🇱</a>
    <a href="?lang=en&month=<?= $month ?>&year=<?= $year ?>" class="<?= $lang==='en'?'active':'' ?>">🇬🇧</a>
</div>

<div class="tz-switch" style="text-align:center; margin-bottom:10px;">
    <?php if ($tz_code === 'pl'): ?>
        <a href="?tz=uk&month=<?= $month ?>&year=<?= $year ?>" class="tz-btn active" title="Przełącz na UK">
            <span class="tz-flag" style="display:inline-block;width:32px;height:32px;vertical-align:middle;">
                <img src="maps/pl.png" alt="Polska" width="32" height="32" style="display:block;width:32px;height:32px;">
            </span>
        </a>
    <?php else: ?>
        <a href="?tz=pl&month=<?= $month ?>&year=<?= $year ?>" class="tz-btn active" title="Przełącz na Polską strefę">
            <span class="tz-flag" style="display:inline-block;width:32px;height:32px;vertical-align:middle;">
                <img src="maps/uk.png" alt="UK" width="32" height="32" style="display:block;width:32px;height:32px;">
            </span>
        </a>
    <?php endif; ?>
    <span style="margin-left:10px;font-size:1em;vertical-align:middle;">
        <?= $t['timezone_label'] ?> <?= $tz_label ?>
    </span>
</div>

<h2><?= $months[$lang][$month]." ".$year ?></h2>
<div class="nav">
<a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>&lang=<?= $lang ?>">⬅ <?= $t['prev'] ?></a>
|
<a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>&lang=<?= $lang ?>"><?= $t['next'] ?> ➡</a>
</div>

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
    $weekday = ($day + $startDay - 2) % 7; // 0=pon, 6=nd
    $classes = 'day';
    
    if($weekday>=5) $classes .= ' weekend'; // sobota=5, niedziela=6
    if(isset($events[$date])) $classes .= ' has-event';

    // Ustal dzisiejszą datę zgodnie z wybraną strefą czasową
    $today = (new DateTime('now', $tz))->format('Y-m-d');
    if ($date === $today) {
    $classes .= ' today';
    }

    echo "<td class='$classes'><strong>$day</strong>";

    if(isset($events[$date])){
        foreach($events[$date] as $e){
            $event_title = htmlspecialchars($e['title']);
            $event_time = $e['event_time'] ? $e['event_time'] : '';
            $eid = $e['id'];
            if (isset($_GET['edit']) && $_GET['edit'] == $eid) {
                echo "<form class='edit-form' method='post' style='margin:5px 0;' onsubmit='return saveEditAjax(this, $eid);'>
                    <input type='hidden' name='edit_id' value='" . $eid . "'>
                    <input type='time' name='time' value='" . htmlspecialchars($event_time) . "'>
                    <input type='text' name='title' value='" . htmlspecialchars($event_title) . "' required>
                    <button type='submit'>{$t['save']}</button>
                    <button type='button' onclick=\"window.location.href='calendar.php?month=$month&year=$year&lang=$lang'\">" . ($lang === 'pl' ? 'Anuluj' : 'Cancel') . "</button>
                    <span class='edit-status' id='edit-status-$eid'></span>
                </form>";
            } else {
                echo "<div class='event'>
                        <span class='time'>$event_time</span> $event_title
                        <a href='?edit=$eid&month=$month&year=$year&lang=$lang' title='Edit'>✏️</a>
                        <a href='?delete=$eid&month=$month&year=$year&lang=$lang' title='Delete' onclick='return confirm(\"Na pewno usunąć?\")'>🗑️</a>
                    </div>";
            }
        }
    }
    echo "<a class='add' href='?add=$date&month=$month&year=$year&lang=$lang'>➕ {$t['add']}</a>";
    // Wyświetl formularz dodawania, jeśli kliknięto Dodaj dla tego dnia
    if (isset($_GET['add']) && $_GET['add'] === $date) {
        echo "<form method='post' class='add-form' style='margin-top:5px;' onsubmit='return addEventAjax(this);'>
        <input type='hidden' name='date' value='" . htmlspecialchars($date) . "'>
        <input type='time' name='time' required>
        <input type='text' name='title' placeholder='{$t['event_title']}' required>
        <button type='submit'>{$t['save']}</button>
        <button type='button' onclick=\"window.location.href='calendar.php?month=$month&year=$year&lang=$lang'\">" . ($lang === 'pl' ? 'Anuluj' : 'Cancel') . "</button>
        <span class='add-status' id='add-status'></span>
    </form>";
    }
    echo "</td>";
}
?>
</tr>
</table>


<script src="javascripts/refajax.js" defer></script>

</body>
</html>
