<?php
// 1. Mulai session
session_start();

// 2. Hancurkan semua data session
session_unset();
session_destroy();

// 3. Arahkan kembali ke halaman login
header("Location: login.php");
exit;
?>