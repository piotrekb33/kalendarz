<?php
$conn = new mysqli("localhost", "root", "", "mojadb_calendar_db");

if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych");
}
$conn->set_charset("utf8mb4");
?>

