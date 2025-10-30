<?php
session_start();
require_once '../config/conn_db.php';

$message = '';
$error = '';
$reset_link = '';

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    if (empty($email)) {
        $error = "Email wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        // 1. Cek apakah email ada di database
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND status = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // 2. Email ditemukan. Buat token dan waktu expiry.
            $token = bin2hex(random_bytes(32));
            // Set token expiry 1 jam dari sekarang
            $expiry_time = date("Y-m-d H:i:s", time() + 3600); 

            // 3. Simpan token dan expiry ke database
            $stmt_update = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
            $stmt_update->bind_param("sss", $token, $expiry_time, $email);
            
            if ($stmt_update->execute()) {
                // 4. SIMULASI PENGIRIMAN EMAIL
                // Tampilkan link reset di halaman
                $reset_link = "http://localhost/UTS/user-mngmt/reset_password.php?token=" . $token;
                $message = "Permintaan reset password berhasil. Silakan cek 'email' Anda untuk link reset.";
            } else {
                $error = "Gagal memproses permintaan. Silakan coba lagi.";
            }
            $stmt_update->close();
        } else {
            $error = "Email tidak ditemukan atau akun belum aktif.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
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
        .activation-link { background: #e2e3e5; padding: 10px; border-radius: 4px; font-size: 0.9em; word-wrap: break-word; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lupa Password</h2>

        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($reset_link)): ?>
            <div class="form-group">
                <label>Simulasi Link Reset:</label>
                <div class="activation-link">
                    <p>Klik link ini untuk me-reset password (link ini seharusnya dikirim ke email):</p>
                    <a href="<?php echo $reset_link; ?>" target="_blank"><?php echo $reset_link; ?></a>
                </div>
            </div>
            <p style="text-align: center; margin-top: 20px;">
                <a href="login.php">Kembali ke Login</a>
            </p>
        <?php else: ?>
            <p style="text-align: center; margin-bottom: 20px;">Masukkan email Anda yang terdaftar. Kami akan mengirimkan link untuk me-reset password Anda.</p>
            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn">Kirim Link Reset</button>
            </form>
            <p style="text-align: center; margin-top: 20px;">
                <a href="login.php">Batal</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>