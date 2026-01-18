<?php
require "db.php";

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
