<?php
session_start();
session_destroy();
header('Location: ./logowanie_rejestracja_login_signup/login.php');
exit;