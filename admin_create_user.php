<?php
session_start();
require __DIR__ . "/db.php";
require __DIR__ . "/User.php";

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Sprawdź czy użytkownik ma uprawnienie 'admin'
if (!User::hasPermission($conn, $_SESSION['user_id'], 'admin')) {
    header('Location: calendar.php');
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
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            min-height: 100vh;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.1);
            padding: 32px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 0.9em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
        }
        select {
            cursor: pointer;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #1565c0;
        }
        .error {
            color: #c00;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background: #ffebee;
            border-radius: 5px;
        }
        .success {
            color: #090;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background: #e8f5e9;
            border-radius: 5px;
        }
        .links {
            margin-top: 20px;
            text-align: center;
        }
        .links a {
            color: #1976d2;
            text-decoration: none;
            margin: 0 10px;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #1976d2;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box strong {
            color: #1976d2;
        }
    </style>
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
        <a href="calendar.php">← Powrót do kalendarza</a>
        <a href="logout.php">Wyloguj</a>
    </div>
</div>
</body>
</html>
