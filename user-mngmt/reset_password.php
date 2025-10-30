<?php
session_start();
require_once '../config/conn_db.php';

$message = '';
$error = '';
$token = '';
$show_form = false;

// Bagian 1: Verifikasi Token saat halaman di-LOAD (GET)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Cek apakah token valid DAN belum kedaluwarsa (expiry > NOW())
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Token valid, tampilkan form
        $show_form = true;
    } else {
        $error = "Token tidak valid atau sudah kedaluwarsa. Silakan minta link reset baru.";
    }
    $stmt->close();

// Bagian 2: Proses Form saat di-SUBMIT (POST)
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($new_password) || empty($confirm_password) || empty($token)) {
        $error = "Semua field wajib diisi!";
        $show_form = true; // Tetap tampilkan form
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru dan konfirmasi password tidak cocok!";
        $show_form = true; // Tetap tampilkan form
    } elseif (strlen($new_password) < 6) {
        $error = "Password baru minimal harus 6 karakter!";
        $show_form = true; // Tetap tampilkan form
    } else {
        // 1. Cek ulang token (untuk keamanan, jika user butuh waktu lama mengisi form)
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
        $stmt_check->bind_param("s", $token);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows == 1) {
            // 2. Token masih valid. Update password!
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // 3. Update password DAN hapus token-nya agar tidak bisa dipakai lagi
            $stmt_update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
            $stmt_update->bind_param("ss", $new_hashed_password, $token);

            if ($stmt_update->execute()) {
                $message = "Password Anda telah berhasil direset! Silakan login dengan password baru Anda.";
                $show_form = false; // Sembunyikan form, tampilkan pesan sukses
            } else {
                $error = "Gagal memperbarui password. Silakan coba lagi.";
                $show_form = true;
            }
            $stmt_update->close();
        } else {
            $error = "Token tidak valid atau sudah kedaluwarsa. Proses dibatalkan.";
            $show_form = false;
        }
        $stmt_check->close();
    }
} else {
    $error = "Akses tidak valid.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; display: grid; place-items: center; min-height: 90vh; background-color: #f4f4f4; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; width: 400px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { background-color: #007bff; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        .btn:hover { background-color: #0056b3; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password Baru</h2>

        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
            <p style="text-align: center;">
                <a href="login.php" class="btn">Menuju Halaman Login</a>
            </p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($show_form): ?>
            <p style="text-align: center; margin-bottom: 20px;">Masukkan password baru Anda.</p>
            <form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="new_password">Password Baru:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password Baru:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn">Reset Password</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($error) && !$show_form): ?>
            <p style="text-align: center; margin-top: 20px;">
                <a href="login.php">Kembali ke Login</a>
            </p>
        <?php endif; ?>

    </div>
</body>
</html>