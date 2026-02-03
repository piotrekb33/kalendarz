<?php
session_start();
$rootDir = dirname(__DIR__);
require $rootDir . "/baza_danych_polaczenie_db_connection/db.php";
require $rootDir . "/klasy_classes/urzytkownicy_users/User.php";
require_once $rootDir . '/Tlumaczenia_Translations/tlumaczenia_translations.php';
require_once $rootDir . '/Ustawienia_Tools/jezyk_lang.php';
require_once $rootDir . '/Ustawienia_Tools/helper_do_linkow.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = (string)($_POST['pass'] ?? '');
    $pass2 = (string)($_POST['pass2'] ?? '');

    if ($user === '' || $pass === '' || $pass2 === '') {
        $error = $translations[$lang]['wypelnij_pola'];
    } elseif ($pass !== $pass2) {
        $error = $translations[$lang]['password_mismatch'];
    } elseif (mb_strlen($user) < 3) {
        $error = $translations[$lang]['usermin3'];
    } elseif (strlen($pass) < 6) {
        $error = $translations[$lang]['passmin6'];
    } else {
        // Sprawdź czy użytkownik już istnieje używając metody statycznej klasy User
        if (User::exists($conn, $user)) {
            $error = $translations[$lang]['user_exists'];
        } else {
            // Utwórz nowy obiekt User (automatycznie ustawi uprawnienie 'user')
            $newUser = new User($user, $pass, 'user');
            
            try {
                // Zapisz użytkownika w bazie (transakcja jest wewnątrz metody save)
                if ($newUser->save($conn)) {
                    $success = $translations[$lang]['konto_utworzone'];
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $translations[$lang]['register'] ?></title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/registration.css">

</head>
<body>


<div class="login-box">
    <h2><?= $translations[$lang]['register'] ?></h2>
                <!-- ===================== ZMIANA JĘZYKA ===================== -->

    <div class="lang-switch">
        <a href="<?= q(['lang'=>'pl']) ?>" class="<?= $lang==='pl'?'active':'' ?>">🇵🇱</a>
        <a href="<?= q(['lang'=>'en']) ?>" class="<?= $lang==='en'?'active':'' ?>">🇬🇧</a>
    </div>
    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
    <form method="post">
        <input type="text" name="user" placeholder="<?= $translations[$lang]['usermin3'] ?>" required autofocus value="<?= htmlspecialchars($user ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="password" name="pass" placeholder="<?= $translations[$lang]['passmin6'] ?>" required>
        <input type="password" name="pass2" placeholder="<?= $translations[$lang]['pass2'] ?>" required>
        <button type="submit"><?= $translations[$lang]['register'] ?></button>
    </form>
    <div class="link"><a href="../logowanie_rejestracja_login_signup/login.php"><?= $translations[$lang]['masz_konto'] ?></a></div>
</div>
</body>
</html>
