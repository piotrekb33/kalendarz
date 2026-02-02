<?php
$rootDir = dirname(__DIR__); // Ustawienia_Tools folder

require $rootDir . '/baza_danych_polaczenie_db_connection/db.php';
//echo $rootDir . '/baza_danych_polaczenie_db_connection/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id    = (int)($_POST['edit_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $time  = $_POST['time'] ?? null;

    if ($id && $title !== '') {
        $stmt = $conn->prepare(
            "UPDATE events SET title = ?, event_time = ? WHERE id = ?"
        );
        $stmt->bind_param("ssi", $title, $time, $id);
        $stmt->execute();

        echo "✅ Zapisano zmiany";
    } else {
        echo "❌ Błąd danych";
    }
}
