<?php
session_start();
require __DIR__ . "/db.php";

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
        $stmt = $conn->prepare("SELECT 1 FROM urzytkownicy WHERE nazwa_urzytkownika = ? LIMIT 1");
        if ($stmt === false) {
            $error = 'Błąd zapytania (sprawdź tabelę urzytkownicy)';
        } else {
            $stmt->bind_param("s", $user); // s - string , bind_param - przypisyje wartosc do zapytania w miejsce "?"
            $stmt->execute();
            $exists = $stmt->get_result()->fetch_assoc();

            if ($exists) {
                $error = 'Taki login już istnieje';
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                
                // Rozpocznij transakcję
                $conn->begin_transaction();
                
                try {
                    // 1. Dodaj użytkownika
                    $stmt = $conn->prepare("INSERT INTO urzytkownicy (nazwa_urzytkownika, haslo_hash) VALUES (?, ?)");
                    if ($stmt === false) {
                        throw new Exception('Błąd przygotowania zapytania INSERT użytkownika');
                    }
                    $stmt->bind_param("ss", $user, $hash);
                    if (!$stmt->execute()) {
                        throw new Exception('Nie udało się zapisać użytkownika');
                    }
                    
                    // 2. Przypisz domyślne uprawnienie "user"
                    $uzytkownika_id = $conn->insert_id;
                    $perm = 'user';
                    $today = date('Y-m-d');
                    $stmt2 = $conn->prepare("INSERT INTO uprawnienia (uprawnienie, data_dodania, uzytkownika_id) VALUES (?, ?, ?)");
                    if ($stmt2 === false) {
                        throw new Exception('Błąd przygotowania zapytania INSERT uprawnienia');
                    }
                    $stmt2->bind_param("ssi", $perm, $today, $uzytkownika_id);
                    if (!$stmt2->execute()) {
                        throw new Exception('Nie udało się zapisać uprawnienia');
                    }
                    
                    // Wszystko OK - zatwierdź transakcję
                    $conn->commit();
                    $success = 'Konto utworzone z uprawnieniem user. Możesz się zalogować.';
                    
                } catch (Exception $e) {
                    // Błąd - wycofaj transakcję
                    $conn->rollback();
                    $error = $e->getMessage();
                }
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
        .login-box .success { color:#090; text-align:center; margin-bottom:10px; width:100%; }
        .login-box .link { margin-top:10px; font-size:0.95em; }
        a { color:#1976d2; text-decoration:none; }
    </style>
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
    <div class="link"><a href="login.php">Masz konto? Zaloguj</a></div>
</div>
</body>
</html>
