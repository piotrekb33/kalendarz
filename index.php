<?php
session_start();

require_once __DIR__.'/Tlumaczenia_Translations/tlumaczenia_translations.php';
require_once __DIR__.'/Ustawienia_Tools/jezyk_lang.php';
require_once __DIR__.'/Ustawienia_Tools/helper_do_linkow.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalendarz - Strona główna</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style.php">
    <link rel="stylesheet" href="style/kalendarztlo.css">
</head>

<body class="calendar">

    <!-- ===================== ZMIANA JĘZYKA ===================== -->
<div class="index-wrapper">
    <div class="lang-switch">
        <a href="<?= q(['lang'=>'pl']) ?>" class="<?= $lang==='pl'?'active':'' ?>">🇵🇱</a>
        <a href="<?= q(['lang'=>'en']) ?>" class="<?= $lang==='en'?'active':'' ?>">🇬🇧</a>
    </div>

    <div class="auth-links">
        <a href="./logowanie_rejestracja_login_signup/login.php" class="btn-auth btn-login">
             <?= $translations[$lang]['login'] ?>
        </a>
        <a href="./logowanie_rejestracja_login_signup/register.php" class="btn-auth btn-register">
            <?= $translations[$lang]['register'] ?>
        </a>
    </div>
</div>
</body>
</html>