<?php
/* ===================== DATA ===================== */
// Pobierz miesiąc i rok z GET, jeśli są poprawne, inaczej użyj bieżącej daty
$month = isset($_GET['month']) && ((int)$_GET['month'] >= 1 && (int)$_GET['month'] <= 12)
         ? (int)$_GET['month']
         : (int)date('m');

$year  = isset($_GET['year']) && ((int)$_GET['year'] > 0)
         ? (int)$_GET['year']
         : (int)date('Y');

/* ===================== NAWIGACJA ===================== */
$prevMonth = $month - 1;
$prevYear  = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $month + 1;
$nextYear  = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

/* ===================== KALENDARZ ===================== */
$firstDay = mktime(0,0,0,$month,1,$year);
$daysInMonth = date('t',$firstDay);
$startDay = date('N',$firstDay);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>



