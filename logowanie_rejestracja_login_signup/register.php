<?php
session_start();
$rootDir = dirname(__DIR__);
require $rootDir . "/baza_danych_polaczenie_db_connection/db.php";
require $rootDir . "/klasy_classes/urzytkownicy_users/User.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = (string)($_POST['pass'] ?? '');
    $pass2 = (string)($_POST['pass2'] ?? '');

    if ($user === '' || $pass === '' || $pass2 === '') {
        $error = 'Wypełnij wszystkie pola';
    } elseif ($pass !== $pass2) {
        $error = 'Hasła się różnią';
    } elseif (mb_strlen($user) < 3) {
        $error = 'Login musi mieć min. 3 znaki';
    } elseif (strlen($pass) < 6) {
        $error = 'Hasło musi mieć min. 6 znaków';
    } else {
        // Sprawdź czy użytkownik już istnieje używając metody statycznej klasy User
        if (User::exists($conn, $user)) {
            $error = 'Taki login już istnieje';
        } else {
            // Utwórz nowy obiekt User (automatycznie ustawi uprawnienie 'user')
            $newUser = new User($user, $pass, 'user');
            
            try {
                // Zapisz użytkownika w bazie (transakcja jest wewnątrz metody save)
                if ($newUser->save($conn)) {
                    $success = 'Konto utworzone z uprawnieniem user. Możesz się zalogować.';
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="../style/registration.css">

</head>
<body>
<div class="login-box">
    <h2>Rejestracja</h2>
    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
    <form method="post">
        <input type="text" name="user" placeholder="Login" required autofocus value="<?= htmlspecialchars($user ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="password" name="pass" placeholder="Hasło (min. 6 znaków)" required>
        <input type="password" name="pass2" placeholder="Powtórz hasło" required>
        <button type="submit">Zarejestruj</button>
    </form>
    <div class="link"><a href="../logowanie_rejestracja_login_signup/login.php">Masz konto? Zaloguj</a></div>
</div>
</body>
</html>
