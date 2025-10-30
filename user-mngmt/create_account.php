<?php
// 1. Memulai session
session_start();

// 2. Menginclude file koneksi database
// Path-nya '../' karena 'config' ada di luar folder 'user-mngmt'
require_once '../config/conn_db.php';

// 3. Variabel untuk menyimpan pesan
$message = '';
$error = '';
$activation_link = '';

// 4. Cek apakah form sudah di-submit (method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 5. Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 6. Validasi data (sederhana)
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Semua field wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        
        // 7. Cek apakah email sudah terdaftar (TUGAS POIN 2)
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Email sudah ada
            $error = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
        } else {
            // 8. Email tersedia. Lanjutkan registrasi.
            
            // Hash password untuk keamanan (JANGAN SIMPAN PASSWORD PLAINTEXT)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Buat token aktivasi unik
            $activation_token = bin2hex(random_bytes(32));
            
            // Set status default = 0 (belum aktif)
            $status = 0; 
            
            // 9. Siapkan query INSERT
            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, status, activation_token) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssis", $name, $email, $hashed_password, $status, $activation_token);

            // 10. Eksekusi query
            if ($stmt_insert->execute()) {
                $message = "Registrasi berhasil! Silakan cek email Anda untuk link aktivasi.";
                
                // ==========================================================
                // SIMULASI PENGIRIMAN EMAIL (TUGAS POIN 3)
                // ==========================================================
                // Mengirim email asli itu rumit (perlu SMTP/PHPMailer).
                // Untuk tugas ini, kita SIMULASIKAN dengan menampilkan link-nya langsung.
                // Pastikan path URL ini BENAR sesuai struktur folder Anda (TANPA webpro5d).
                $activation_link = "http://localhost/UTS/user-mngmt/activate.php?token=" . $activation_token;
                // ==========================================================

            } else {
                $error = "Registrasi gagal. Silakan coba lagi. Error: " . $stmt_insert->error;
            }
            
            $stmt_insert->close();
        }
        
        $stmt_check->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna</title>
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
        <h2>Registrasi Admin Gudang</h2>

        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($activation_link)): ?>
            <div class="form-group">
                <label>Simulasi Link Aktivasi:</label>
                <div class="activation-link">
                    <p>Klik link ini untuk mengaktifkan akun (link ini seharusnya dikirim ke email):</p>
                    <a href="<?php echo $activation_link; ?>" target="_blank"><?php echo $activation_link; ?></a>
                </div>
            </div>
        <?php else: ?>
            <form action="create_account.php" method="POST">
                <div class="form-group">
                    <label for="name">Nama Lengkap:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email (untuk login):</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Daftar</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>