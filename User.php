<?php
class User {
    public $name;
    public $passwordHash;
    public $permission;
    public $createdAt;

    // Konstruktor – ustawia wszystko od razu
    public function __construct($name, $password, $permission = 'user') {
        $this->name = $name;
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->permission = $permission;
        $this->createdAt = date('Y-m-d');
    }

    // Metoda statyczna do sprawdzania czy użytkownik o danym loginie już istnieje
    public static function exists(mysqli $conn, $name) {
        $stmt = $conn->prepare("SELECT 1 FROM urzytkownicy WHERE nazwa_urzytkownika = ? LIMIT 1");
        if (!$stmt) return false;
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result !== null;
    }

    // Metoda statyczna do sprawdzania czy użytkownik ma dane uprawnienie
    public static function hasPermission(mysqli $conn, $userId, $permission) {
        $stmt = $conn->prepare("SELECT 1 FROM uprawnienia WHERE uzytkownika_id = ? AND uprawnienie = ? LIMIT 1");
        if (!$stmt) return false;
        $stmt->bind_param("is", $userId, $permission);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result !== null;
    }

    // Metoda, która zapisuje użytkownika w bazie
    public function save(mysqli $conn) {
        // Rozpoczynamy transakcję
        $conn->begin_transaction();

        try {
            // INSERT do tabeli urzytkownicy (dostosowane do Twojej struktury bazy)
            $stmt = $conn->prepare("INSERT INTO urzytkownicy (nazwa_urzytkownika, haslo_hash) VALUES (?, ?)");
            if (!$stmt) {
                throw new Exception("Błąd przygotowania zapytania INSERT urzytkownicy");
            }
            $stmt->bind_param("ss", $this->name, $this->passwordHash);
            if (!$stmt->execute()) {
                throw new Exception("Nie udało się zapisać użytkownika");
            }

            // Pobieramy ID nowego użytkownika
            $userId = $conn->insert_id;

            // INSERT do tabeli uprawnienia
            $stmt2 = $conn->prepare(
                "INSERT INTO uprawnienia (uprawnienie, data_dodania, uzytkownika_id) VALUES (?, ?, ?)"
            );
            if (!$stmt2) {
                throw new Exception("Błąd przygotowania zapytania INSERT uprawnienia");
            }
            $stmt2->bind_param("ssi", $this->permission, $this->createdAt, $userId);
            if (!$stmt2->execute()) {
                throw new Exception("Nie udało się zapisać uprawnienia");
            }

            // Zatwierdzamy transakcję
            $conn->commit();
            return true;

        } catch (Exception $e) {
            // Cofamy wszystko w przypadku błędu
            $conn->rollback();
            throw $e; // Przekazujemy wyjątek dalej, żeby register.php mógł go obsłużyć
        }
    }
}
?>
