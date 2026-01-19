<?php
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';
    // Prosty login: admin / haslo123
    if ($user === 'admin' && $pass === 'haslo123') {
        $_SESSION['user'] = $user;
        header('Location: calendar.php');
        exit;
    } else {
        $error = 'Nieprawidłowy login lub hasło';
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
</div>
</body>
</html>
