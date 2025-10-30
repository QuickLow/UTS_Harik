<?php
// 1. Memulai session
session_start();

// 2. Menginclude file koneksi database
require_once '../config/conn_db.php';

$message = '';
$error = '';

// 3. Cek apakah ada token di URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    
    $token = $_GET['token'];

    // 4. Cari token di database
    // Kita cari pengguna yang punya token ini DAN statusnya masih 0 (belum aktif)
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE activation_token = ? AND status = 0");
    $stmt_check->bind_param("s", $token);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows == 1) {
        // 5. Token ditemukan dan valid!
        // Update status menjadi 1 (AKTIF) dan hapus tokennya (agar tidak bisa dipakai lagi)
        $stmt_update = $conn->prepare("UPDATE users SET status = 1, activation_token = NULL WHERE activation_token = ?");
        $stmt_update->bind_param("s", $token);
        
        if ($stmt_update->execute()) {
            $message = "Aktivasi akun berhasil! Akun Anda sekarang aktif dan sudah bisa digunakan untuk login.";
        } else {
            $error = "Aktivasi gagal. Terjadi kesalahan pada database.";
        }
        $stmt_update->close();
    } else {
        // 6. Token tidak ditemukan atau akun sudah diaktifkan sebelumnya
        $error = "Token aktivasi tidak valid, kedaluwarsa, atau akun Anda sudah diaktifkan sebelumnya.";
    }
    
    $stmt_check->close();
} else {
    // 7. Tidak ada token di URL
    $error = "Token aktivasi tidak ditemukan. Link tidak valid.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun</title>
    <style>
        body { font-family: Arial, sans-serif; display: grid; place-items: center; min-height: 90vh; background-color: #f4f4f4; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; width: 400px; text-align: center; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .btn { background-color: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; margin-top: 10px; }
        .btn:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Status Aktivasi Akun</h2>

        <?php if (!empty($message)): ?>
            <div class.message success"><?php echo $message; ?></div>
            <a href="login.php" class="btn">Menuju Halaman Login</a>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

    </div>
</body>
</html>