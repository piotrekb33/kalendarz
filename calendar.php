<?php
require "db.php";
date_default_timezone_set("Europe/Warsaw");

/* ===================== ZAPIS / ODCZYT JĘZYKA Z COOKIE ===================== */
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    setcookie("lang", $lang, time() + 3600*24*30, "/"); // zapis na 30 dni
} elseif (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
} else {
    $lang = 'pl'; // domyślny język
}

/* ===================== TŁUMACZENIA ===================== */


$translations = [
    'pl' => [
        'prev' => 'Poprzedni',
        'next' => 'Następny',
        'add' => 'Dodaj',
        'add_event' => 'Dodaj wydarzenie',
        'event_title' => 'Opis wydarzenia',
        'save' => 'Zapisz',
        'language' => 'Język',
    ],
    'en' => [
        'prev' => 'Previous',
        'next' => 'Next',
        'add' => 'Add',
        'add_event' => 'Add event',
        'event_title' => 'Event description',
        'save' => 'Save',
        'language' => 'Language',
    ]
];

if (!isset($translations[$lang])) {
    $lang = 'pl';
}

$t = $translations[$lang];

/* ===================== DNI / MIESIĄCE ===================== */
$days = [
    'pl' => ['Pon','Wto','Śro','Czw','Pią','Sob','Nie'],
    'en' => ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']
];

$months = [
    'pl' => [1=>'Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
    'en' => [1=>'January','February','March','April','May','June','July','August','September','October','November','December']
];

/* ===================== DATA ===================== */
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

/* === KOREKTA MIESIĄCA === */
if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

/* ===================== NAWIGACJA ===================== */
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

/* ===================== KALENDARZ ===================== */
$firstDay = mktime(0,0,0,$month,1,$year);
$daysInMonth = date('t', $firstDay);
$startDay = date('N', $firstDay);

/* ===================== ZAPIS WYDARZENIA ===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $title = trim($_POST['title'] ?? '');

    if ($date && $title) {
        $stmt = $conn->prepare(
            "INSERT INTO events (event_date, title) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $date, $title);
        $stmt->execute();
    }

    header("Location: calendar.php?month=$month&year=$year&lang=$lang");
    exit;
}

/* ===================== USUWANIE WYDARZENIA ===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: calendar.php?month=$month&year=$year&lang=$lang");
    exit;
}

/* ===================== EDYCJA WYDARZENIA ===================== */
if (isset($_POST['edit_id'])) {
    $id = (int)$_POST['edit_id'];
    $title = trim($_POST['title'] ?? '');
    if ($title) {
        $stmt = $conn->prepare("UPDATE events SET title=? WHERE id=?");
        $stmt->bind_param("si", $title, $id);
        $stmt->execute();
    }
    header("Location: calendar.php?month=$month&year=$year&lang=$lang");
    exit;
}


/* ===================== WYDARZENIA ===================== */
$events = [];
$result = $conn->query("SELECT id, event_date, title FROM events");
while ($row = $result->fetch_assoc()) {
    $events[$row['event_date']][] = [
        'id' => $row['id'],
        'title' => $row['title']
    ];
}

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title>Calendar</title>
<link rel="stylesheet" href="style.css">


</head>

<body>

<!-- JĘZYK -->
<div class="lang-switch">
    <a href="?lang=pl&month=<?= $month ?>&year=<?= $year ?>"
       class="<?= $lang === 'pl' ? 'active' : '' ?>"
       title="Polski">🇵🇱</a>

    <a href="?lang=en&month=<?= $month ?>&year=<?= $year ?>"
       class="<?= $lang === 'en' ? 'active' : '' ?>"
       title="English">🇬🇧</a>

    <p class="current-lang">
        <strong><?= strtoupper($lang) ?></strong></p>
    
</div>


<h2><?= $months[$lang][$month] . " " . $year ?></h2>

<!-- NAWIGACJA -->
<a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>&lang=<?= $lang ?>">⬅ <?= $t['prev'] ?></a> |
<a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>&lang=<?= $lang ?>"><?= $t['next'] ?> ➡</a>

<table>
<tr>
<?php foreach ($days[$lang] as $d): ?>
    <th><?= $d ?></th>
<?php endforeach; ?>
</tr>

<tr>
<?php
for ($i=1; $i<$startDay; $i++) echo "<td></td>";

for ($day=1; $day<=$daysInMonth; $day++) {

    if ((($day+$startDay-1)%7)==1 && $day!=1) echo "</tr><tr>";

    $date = sprintf("%04d-%02d-%02d",$year,$month,$day);

    echo "<td class='day'><strong>$day</strong>";

    if (isset($events[$date])) {
        foreach ($events[$date] as $e) {
            $event_id = $e['id'];
            $event_title = htmlspecialchars($e['title']);
            echo "<div class='event'>
                    $event_title
                    <a href='?edit=$event_id&month=$month&year=$year&lang=$lang' title='Edytuj'>✏️</a>
                    <a href='?delete=$event_id&month=$month&year=$year&lang=$lang' title='Usuń' onclick='return confirm(\"Na pewno usunąć?\")'>🗑️</a>
                </div>";
        }
    }


    echo "<a class='add' href='?add=$date&month=$month&year=$year&lang=$lang'>➕ {$t['add']}</a>";
    echo "</td>";
}
?>
</tr>
</table>

<?php if (isset($_GET['add'])): ?>
<h3><?= $t['add_event'] ?> (<?= htmlspecialchars($_GET['add']) ?>)</h3>

<form method="post">
    <input type="hidden" name="date" value="<?= htmlspecialchars($_GET['add']) ?>">
    <input type="text" name="title" placeholder="<?= $t['event_title'] ?>" required>
    <button><?= $t['save'] ?></button>
</form>
<?php endif; ?>

<?php
if (isset($_GET['edit'])):
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT title FROM events WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
?>
<h3>Edytuj wydarzenie</h3>
<form method="post">
    <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
    <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" required>
    <button>Zapisz</button>
</form>
<?php endif; ?>


</body>
</html>
