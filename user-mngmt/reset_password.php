<?php
// 1. Menginclude file koneksi
require_once '../config/conn_db.php';

// 2. Variabel
$message = '';
$error = '';
$token_valid = false;
$token = '';

// 3. Cek apakah ada token di URL (method GET)
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    // 4. Validasi token di database
    $stmt_check = $conn->prepare("SELECT id, reset_token_expires FROM users WHERE reset_token = ?");
    $stmt_check->bind_param("s", $token);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $expires = $user['reset_token_expires'];
        $current_time = date("Y-m-d H:i:s");

        // 5. Cek apakah token sudah kedaluwarsa
        if ($current_time > $expires) {
            $error = "Link reset password ini sudah kedaluwarsa. Silakan ajukan permintaan baru.";
        } else {
            // Token valid dan belum kedaluwarsa
            $token_valid = true;
        }
    } else {
        $error = "Token tidak valid. Pastikan Anda menggunakan link yang benar.";
    }
    $stmt_check->close();

} else {
    // 6. Handle jika form password baru di-submit (method POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $token = $_POST['token']; // Ambil token dari hidden field
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        // 7. Validasi input
        if (empty($password) || empty($password_confirm)) {
            $error = "Semua field password wajib diisi.";
            $token_valid = true; // Tetap tampilkan form
        } elseif ($password !== $password_confirm) {
            $error = "Password dan Konfirmasi Password tidak cocok.";
            $token_valid = true; // Tetap tampilkan form
        } elseif (strlen($password) < 6) {
            $error = "Password minimal harus 6 karakter.";
            $token_valid = true; // Tetap tampilkan form
        } else {
            // 8. Validasi token sekali lagi (untuk keamanan)
            $stmt_check_post = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
            $stmt_check_post->bind_param("s", $token);
            $stmt_check_post->execute();
            $result_post = $stmt_check_post->get_result();

            if ($result_post->num_rows > 0) {
                // Token valid, hash password baru
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // 9. Update password dan hapus token
                // Kita set token & expires jadi NULL agar tidak bisa dipakai lagi
                $stmt_update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = ?");
                $stmt_update->bind_param("ss", $hashed_password, $token);
                
                if ($stmt_update->execute()) {
                    $message = "Password Anda telah berhasil diperbarui! Silakan login dengan password baru Anda.";
                    // $token_valid kita biarkan false agar form-nya hilang
                } else {
                    $error = "Gagal memperbarui password. Silakan coba lagi.";
                    $token_valid = true;
                }
                $stmt_update->close();
            } else {
                $error = "Token tidak valid atau sudah kedaluwarsa.";
            }
            $stmt_check_post->close();
        }
    } else {
        // Jika halaman dibuka tanpa token
        $error = "Token tidak ditemukan. Silakan gunakan link dari email Anda.";
    }
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
        .btn { background-color: #28a745; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        .btn:hover { background-color: #218838; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .login-link { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Atur Password Baru</h2>

        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php 
        // Tampilkan form HANYA jika token valid
        if ($token_valid): 
        ?>
            <form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="password">Password Baru:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password Baru:</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                <button type="submit" class="btn">Simpan Password Baru</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="login-link">
                <a href="login.php">Ke Halaman Login</a>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>