<?php
session_start();
/* ===================== ŚCIEŻKA GŁÓWNA PROJEKTU ===================== */
$rootDir = dirname(__DIR__); // jeśli ten plik jest w podfolderze
// albo:
// $rootDir = __DIR__;        // jeśli jest w katalogu głównym
//echo $rootDir;
require $rootDir . "/baza_danych_polaczenie_db_connection/db.php";
require $rootDir . "/klasy_classes/urzytkownicy_users/User.php";
require_once $rootDir . '/Tlumaczenia_Translations/tlumaczenia_translations.php';
require_once $rootDir . '/Ustawienia_Tools/jezyk_lang.php';
require_once $rootDir . '/Ustawienia_Tools/helper_do_linkow.php';



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
        $error = $translations[$lang]['wypelnij_pola'];
    } elseif ($pass !== $pass2) {
        $error = $translations[$lang]['password_mismatch'];
    } elseif (mb_strlen($user) < 3) {
        $error = $translations[$lang]['usermin3'];
    } elseif (strlen($pass) < 6) {
        $error = $translations[$lang]['passmin6'];
    } elseif (!in_array($permission, ['user', 'admin'])) {
        $error = $translations[$lang]['brakuprawnienia'];
    } else {
        // Sprawdź czy użytkownik już istnieje
        if (User::exists($conn, $user)) {
            $error = $translations[$lang]['user_exists'];
        } else {
            // Utwórz nowy obiekt User z wybranym uprawnieniem
            $newUser = new User($user, $pass, $permission);
            
            try {
                // Zapisz użytkownika w bazie
                if ($newUser->save($conn)) {
                    $success = $translationtext[$lang]['user'] . "'$user'" . $translationtext[$lang]['utworzony'] . "'$permission'.";
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
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $translationstext[$lang]['panel'] ?> - <?= $translationstext[$lang]['user_create'] ?></title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/adminCreateUser.css">
</head>
<body>
<div class="container">
    <h1><?= $translationstext[$lang]['panel'] ?></h1>
    <p class="subtitle"><?= $translationstext[$lang]['user_create'] ?></p>

    <!-- ===================== ZMIANA JĘZYKA ===================== -->

    <div class="lang-switch">
        <a href="<?= q(['lang'=>'pl']) ?>" class="<?= $lang==='pl'?'active':'' ?>">🇵🇱</a>
        <a href="<?= q(['lang'=>'en']) ?>" class="<?= $lang==='en'?'active':'' ?>">🇬🇧</a>
    </div>
    
    <div class="info-box">
        <strong><?= $translationstext[$lang]['uwaga'] ?></strong> <?= $translationstext[$lang]['tylko_administratorzy'] ?>
    </div>

    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="user"><?= $translationstext[$lang]['user'] ?>:</label>
            <input type="text" id="user" name="user" placeholder="<?= $translations[$lang]['usermin3'] ?>" required autofocus value="<?= htmlspecialchars($user ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="pass"><?= $translations[$lang]['pass'] ?>:</label>
            <input type="password" id="pass" name="pass" placeholder="<?= $translations[$lang]['passmin6'] ?>" required>
        </div>

        <div class="form-group">
            <label for="pass2"><?= $translations[$lang]['pass2'] ?>:</label>
            <input type="password" id="pass2" name="pass2" placeholder="<?= $translations[$lang]['pass2'] ?>" required>
        </div>

        <div class="form-group">
            <label for="permission"><?= $translations[$lang]['uprawnienia'] ?>:</label>
            <select id="permission" name="permission" required>
                <option value="user" <?= ($permission ?? 'user') === 'user' ? 'selected' : '' ?>><?= $translationstext[$lang]['user_uprawnienia'] ?></option>
                <option value="admin" <?= ($permission ?? 'user') === 'admin' ? 'selected' : '' ?>><?= $translationstext[$lang]['admin_uprawnienia'] ?></option>
            </select>
        </div>

        <button type="submit"><?= $translationstext[$lang]['stworz_uzytkownika'] ?></button>
    </form>

    <div class="links">
        <a href="../Kalendarz/calendar.php">← <?= $translationstext[$lang]['powrot_do_kalendarza'] ?></a>
        <a href="logout.php"><?= $translations[$lang]['logout'] ?></a>
    </div>
</div>
</body>
</html>
