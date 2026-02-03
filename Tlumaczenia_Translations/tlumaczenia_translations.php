<?php
// lang.php
// Translation strings for Polish and English languages
$translations = [
    'pl' => [
        'prev'           => 'Poprzedni',
        'next'           => 'Następny',
        'add'            => 'Dodaj',
        'add_event'      => 'Dodaj wydarzenie',
        'event_title'    => 'Opis wydarzenia',
        'event_time'     => 'Godzina (HH:MM)',
        'save'           => 'Zapisz',
        'language'       => 'Język',
        'edit_event'     => 'Edytuj wydarzenie',
        'timezone_label' => 'Strefa:',
        'tz_pl'          => 'Polska',
        'tz_uk'          => 'Wielka Brytania',
        'admin_panel'    => 'Panel Administratora',
        'cancel'         => 'Anuluj',
        'logout'         => 'Wyloguj',
        'powitanie'      => 'Witaj',
        'login'          => 'Zaloguj się',
        'register'       => 'Zarejestruj się',
        'logowanie'      => 'Logowanie do kalendarza',
        'user'           => 'Nazwa użytkownika',
        'wypelnij_pola' => 'Wypełnij wszystkie pola!',
        'pass'           => 'Hasło',
        'pass2'          => 'Powtórz hasło',
        'password_mismatch' => 'Hasła się różnią!',
        'user_exists'      => 'Taki login już istnieje!',
        'passmin6'        => 'Hasło musi mieć min. 6 znaków!',
        'usermin3'        => 'Login musi mieć min. 3 znaki!',
        'masz_konto'     => 'Masz konto? Zaloguj się!',
        'brakkonta'      => 'Nie masz konta? Zarejestruj się!',
        'uprawnienia'   => 'Uprawnienia',
        'brakuprawnienia' => 'Nie masz uprawnień do dostępu do tej strony.',
        'konto_utworzone' => 'Konto utworzone z uprawnieniem user. Możesz się zalogować.'
    ],

    'en' => [
        'prev'           => 'Previous',
        'next'           => 'Next',
        'add'            => 'Add',
        'add_event'      => 'Add event',
        'event_title'    => 'Event description',
        'event_time'     => 'Time (HH:MM)',
        'save'           => 'Save',
        'language'       => 'Language',
        'edit_event'     => 'Edit event',
        'timezone_label' => 'Timezone:',
        'tz_pl'          => 'Poland',
        'tz_uk'          => 'UK',
        'admin_panel'    => 'Admin Panel',
        'cancel'         => 'Cancel',
        'logout'         => 'Logout',
        'powitanie'      => 'Welcome',
        'login'          => 'Log in',
        'register'       => 'Register',
        'logowanie'      => 'Calendar Login',
        'user'           => 'Username',
        'wypelnij_pola' => 'Fill in all fields!',
        'pass'           => 'Password',
        'pass2'          => 'Repeat Password',
        'password_mismatch' => 'Passwords do not match!',
        'user_exists'      => 'Username already exists!',
        'passmin6'        => 'Password must be at least 6 characters!',
        'usermin3'        => 'Username must be at least 3 characters!',
        'masz_konto'     => 'Have an account? Log in!',
        'brakkonta'      => 'No account? Register!',
        'uprawnienia'   => 'Permissions',
        'brakuprawnienia' => 'You do not have permission to access this page.',
        'konto_utworzone' => 'Account created with user permissions. You can now log in.'
    ],
];

$translationstext = [
    'pl' => [
        'user' => 'Użytkownik ',
        'utworzony' => ' utworzony z uprawnieniem ',
        'panel' => 'Panel Administratora',
        'user_create' => 'Tworzenie nowego użytkownika',
        'uwaga' => 'Uwaga:',
        'tylko_administratorzy' => 'Tylko administratorzy mogą tworzyć nowych użytkowników z dowolnymi uprawnieniami.',
        'user_uprawnienia' => 'Użytkownik otrzyma uprawnienia zwykłego użytkownika.',
        'admin_uprawnienia' => 'Użytkownik otrzyma uprawnienia administratora.',
        'stworz_uzytkownika' => 'Stwórz użytkownika',
        'powrot_do_kalendarza' => 'Powrót do kalendarza',
    ],
    'en' => [
        'user' => 'User ',
        'utworzony' => ' created with permission ',
        'panel' => 'Admin Panel',
        'user_create' => 'Creating New User',
        'uwaga' => 'Attention:',
        'tylko_administratorzy' => 'Only administrators can create new users with any permissions.',
        'user_uprawnienia' => 'The user will have regular user permissions.',
        'admin_uprawnienia' => 'The user will have administrator permissions.',
        'stworz_uzytkownika' => 'Create User',
        'powrot_do_kalendarza' => 'Return to Calendar',

    ],
        
];


/* ===================== DNI / MIESIĄCE ===================== */
$days = [
    'pl'=>['Pon','Wto','Śro','Czw','Pią','Sob','Nie'],
    'en'=>['Mon','Tue','Wed','Thu','Fri','Sat','Sun']
];
$months = [
    'pl'=>[1=>'Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
    'en'=>[1=>'January','February','March','April','May','June','July','August','September','October','November','December']
];