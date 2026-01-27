<?php
/*Edycja wpisu kalendarza - Calendar Editor*/

/* ===================== USUWANIE ===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Przekierowanie na czysty URL po usunięciu
    $lang = isset($_GET['lang']) && in_array($_GET['lang'], ['pl','en']) ? $_GET['lang'] : 'pl';
    $tz   = isset($_GET['tz']) ? $_GET['tz'] : (isset($_COOKIE['tz_code']) ? $_COOKIE['tz_code'] : 'uk');
    header("Location: calendar.php?month=$month&year=$year&lang=$lang&tz=$tz");
    exit;
}

/* ===================== EDYCJA WYDARZENIA ===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id    = (int)$_POST['edit_id'];
    $title = trim($_POST['title'] ?? '');
    $time  = $_POST['time'] ?? null;

    if ($title !== '') {
        $stmt = $conn->prepare("UPDATE events SET title=?, event_time=? WHERE id=?");
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
        $stmt = $conn->prepare("INSERT INTO events (event_date, event_time, title) VALUES (?, ?, ?)");
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
?>