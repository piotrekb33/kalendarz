<?php
require __DIR__ . "/db.php";

$login = "piotr";
$haslo = "123";

$hash = password_hash($haslo, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO urzytkownicy (nazwa_urzytkownika, haslo_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $login, $hash);
$stmt->execute();

echo "OK";