<?php
session_start();
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/User.php";

// Jeśli użytkownik chce się wymuszone wylogować (np. po zmianie uprawnień w bazie)
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Jeśli użytkownik jest już zalogowany, zaktualizuj jego uprawnienia i przekieruj
if (isset($_SESSION['user']) && isset($_SESSION['user_id'])) {
    // Zaktualizuj uprawnienia admin w sesji (na wypadek zmiany w bazie)
    $_SESSION['is_admin'] = User::hasPermission($conn, $_SESSION['user_id'], 'admin');
    header('Location: calendar.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = (string)($_POST['pass'] ?? '');

    if ($user === '' || $pass === '') {
        $error = 'Podaj login i hasło';
    } else {
        // Schemat bazy (Twoja tabela):
        // - tabela: urzytkownicy
        // - kolumny: uzytkownika_id, nazwa_urzytkownika, haslo_hash
        $stmt = $conn->prepare("SELECT uzytkownika_id, nazwa_urzytkownika, haslo_hash FROM urzytkownicy WHERE nazwa_urzytkownika = ? LIMIT 1");
        if ($stmt === false) {
            $error = 'Błąd zapytania (sprawdź czy istnieje tabela urzytkownicy)';
        } else {
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;

            $ok = false;
            if ($row && !empty($row['haslo_hash'])) {
                $ok = password_verify($pass, $row['haslo_hash']);
            }

            if ($ok) {
                $_SESSION['user'] = $row['nazwa_urzytkownika'];
                $_SESSION['user_id'] = (int)$row['uzytkownika_id'];
                
                // Sprawdź czy użytkownik ma uprawnienie admin
                $_SESSION['is_admin'] = User::hasPermission($conn, $_SESSION['user_id'], 'admin');
                
                header('Location: calendar.php');
                exit;
            } else {
                $error = 'Nieprawidłowy login lub hasło';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie do kalendarza</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #7fffd4;
            margin: 0;
        }
        .login-box {
            max-width:340px;
            width:100%;
            background:#fff;
            border-radius:10px;
            box-shadow:0 2px 16px #0001;
            padding:32px;
            margin:auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-box h2 { text-align:center; width:100%; }
        .login-box form {
            width:100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-box input {
            width:90%;
            margin:8px 0;
            padding:8px;
            border-radius:5px;
            border:1px solid #bbb;
            box-sizing: border-box;
            text-align: center;
        }
        .login-box button {
            width:90%;
            padding:10px;
            background:#1976d2;
            color:#fff;
            border:none;
            border-radius:5px;
            font-size:1.1em;
            cursor:pointer;
            margin-top:12px;
            text-align: center;
        }
        .login-box .error { color:#c00; text-align:center; margin-bottom:10px; width:100%; }
        .login-box .link { margin-top:10px; font-size:0.95em; }
        a { color:#1976d2; text-decoration:none; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Logowanie</h2>
    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <form method="post">
        <input type="text" name="user" placeholder="Login" required autofocus>
        <input type="password" name="pass" placeholder="Hasło" required>
        <button type="submit">Zaloguj</button>
    </form>
    <div class="link"><a href="register.php">Nie masz konta? Zarejestruj</a></div>
    <?php if (isset($_SESSION['user'])): ?>
        <div class="link" style="margin-top: 10px;">
            <a href="login.php?logout=1" style="color: #c00;">Wymuszone wylogowanie (jeśli strona jest zablokowana)</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
