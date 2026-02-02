<?php
session_start();
/* ===================== ŚCIEŻKA GŁÓWNA PROJEKTU ===================== */
$rootDir = dirname(__DIR__); // jeśli ten plik jest w podfolderze
// albo:
// $rootDir = __DIR__;        // jeśli jest w katalogu głównym
//echo $rootDir;
require $rootDir . "/baza_danych_polaczenie_db_connection/db.php";
require $rootDir . "/klasy_classes/urzytkownicy_users/User.php";



// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Sprawdź czy użytkownik ma uprawnienie 'admin'
if (!User::hasPermission($conn, $_SESSION['user_id'], 'admin')) {
    header('Location: ../Kalendarz/calendar.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = (string)($_POST['pass'] ?? '');
    $pass2 = (string)($_POST['pass2'] ?? '');
    $permission = trim($_POST['permission'] ?? 'user');

    // Walidacja
    if ($user === '' || $pass === '' || $pass2 === '') {
        $error = 'Wypełnij wszystkie pola';
    } elseif ($pass !== $pass2) {
        $error = 'Hasła się różnią';
    } elseif (mb_strlen($user) < 3) {
        $error = 'Login musi mieć min. 3 znaki';
    } elseif (strlen($pass) < 6) {
        $error = 'Hasło musi mieć min. 6 znaków';
    } elseif (!in_array($permission, ['user', 'admin'])) {
        $error = 'Nieprawidłowe uprawnienie';
    } else {
        // Sprawdź czy użytkownik już istnieje
        if (User::exists($conn, $user)) {
            $error = 'Taki login już istnieje';
        } else {
            // Utwórz nowy obiekt User z wybranym uprawnieniem
            $newUser = new User($user, $pass, $permission);
            
            try {
                // Zapisz użytkownika w bazie
                if ($newUser->save($conn)) {
                    $success = "Użytkownik '$user' utworzony z uprawnieniem '$permission'.";
                    // Wyczyść formularz
                    $user = '';
                    $permission = 'user';
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
} else {
    $user = '';
    $permission = 'user';
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Tworzenie użytkownika</title>
    <link rel="stylesheet" href="../style/adminCreateUser.css">
</head>
<body>
<div class="container">
    <h1>Panel Administracyjny</h1>
    <p class="subtitle">Tworzenie nowego użytkownika</p>
    
    <div class="info-box">
        <strong>Uwaga:</strong> Tylko administratorzy mogą tworzyć nowych użytkowników z dowolnymi uprawnieniami.
    </div>

    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="user">Login użytkownika:</label>
            <input type="text" id="user" name="user" placeholder="Login (min. 3 znaki)" required autofocus value="<?= htmlspecialchars($user ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="pass">Hasło:</label>
            <input type="password" id="pass" name="pass" placeholder="Hasło (min. 6 znaków)" required>
        </div>

        <div class="form-group">
            <label for="pass2">Powtórz hasło:</label>
            <input type="password" id="pass2" name="pass2" placeholder="Powtórz hasło" required>
        </div>

        <div class="form-group">
            <label for="permission">Uprawnienia:</label>
            <select id="permission" name="permission" required>
                <option value="user" <?= ($permission ?? 'user') === 'user' ? 'selected' : '' ?>>User - zwykły użytkownik</option>
                <option value="admin" <?= ($permission ?? 'user') === 'admin' ? 'selected' : '' ?>>Admin - administrator</option>
            </select>
        </div>

        <button type="submit">Utwórz użytkownika</button>
    </form>

    <div class="links">
        <a href="../Kalendarz/calendar.php">← Powrót do kalendarza</a>
        <a href="logout.php">Wyloguj</a>
    </div>
</div>
</body>
</html>
